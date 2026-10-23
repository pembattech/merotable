<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\Validator;
use JsonException;


use App\Models\MenuItem;
use App\Http\Resources\V1\AdminMenuItemResource;

class MenuController extends Controller
{

    /**
     * Create a menu item for the authenticated restaurant.
     *
     * Supports:
     * - Single-price menu items
     * - Half / Full sizes
     * - Small / Medium / Large sizes
     * - Optional image uploads
     * - Vegetarian, popular and availability settings
     */
    public function store(Request $request)
    {

        // Get the restaurant from the restaurant authentication guard.
        $restaurant = auth('restaurant')->user();

        if (!$restaurant) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $limit = $restaurant->getFeatureLimit('menu_limit');

        if ($limit > 0 && $restaurant->menuItems()->count() >= $limit) {
            return response()->json([
                'success' => false,
                'message' => "Menu limit of {$limit} reached. Upgrade your plan.",
            ], 403);
        }


        $restaurantId = $restaurant->id;

        // Validate the form fields.
        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('restaurant_id', $restaurantId),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'pricing_mode' => [
                'required',
                Rule::in(['single', 'sizes']),
            ],

            'price' => [
                'required_if:pricing_mode,single',
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'size_type' => [
                'required_if:pricing_mode,sizes',
                'nullable',
                Rule::in(['hf', 'sml']),
            ],

            'sizes' => [
                'required_if:pricing_mode,sizes',
                'array',
            ],

            'sizes.*.price' => [
                'required_if:pricing_mode,sizes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'is_vegetarian' => [
                'sometimes',
                'boolean',
            ],

            'is_popular' => [
                'sometimes',
                'boolean',
            ],

            'is_available' => [
                'required',
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        // Define the allowed size names and codes on the server.
        // Do not rely on hidden form fields for authoritative size names.
        $sizeDefinitions = [
            'hf' => [
                ['name' => 'Half', 'code' => 'half'],
                ['name' => 'Full', 'code' => 'full'],
            ],

            'sml' => [
                ['name' => 'Small', 'code' => 'small'],
                ['name' => 'Medium', 'code' => 'medium'],
                ['name' => 'Large', 'code' => 'large'],
            ],
        ];

        $sizesToCreate = [];
        $basePrice = $validated['price'] ?? null;

        // Prepare the selected serving sizes and determine the base price.
        if ($validated['pricing_mode'] === 'sizes') {
            $definitions = $sizeDefinitions[$validated['size_type']];
            $submittedSizes = $validated['sizes'] ?? [];

            // Require exactly the number of sizes supported by the selection.
            if (
                count($submittedSizes) !== count($definitions) ||
                array_keys($submittedSizes) !== range(
                    0,
                    count($definitions) - 1
                )
            ) {
                throw ValidationException::withMessages([
                    'sizes' => [
                        'Please provide a price for every selected size.',
                    ],
                ]);
            }

            foreach ($definitions as $index => $definition) {
                $sizesToCreate[] = [
                    ...$definition,
                    'price' => $submittedSizes[$index]['price'],
                    'sort_order' => $index + 1,
                    'is_available' => true,
                ];
            }

            // Use Full for Half/Full items and Medium for S/M/L items.
            $basePrice = $submittedSizes[1]['price'];
        }

        $imagePath = null;

        try {
            // Store the image on the public disk, if provided.
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('menu-items', 'public');
            }

            // Save the item and its sizes atomically.
            $menuItem = DB::transaction(function () use ($validated, $restaurantId, $basePrice, $sizesToCreate, $imagePath) {
                $menuItem = MenuItem::create([
                    'restaurant_id' => $restaurantId,
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'price' => $basePrice,

                    'is_vegetarian' => $validated['is_vegetarian'] ?? false,
                    'is_popular' => $validated['is_popular'] ?? false,
                    'is_available' => $validated['is_available'],

                    'has_sizes' => $validated['pricing_mode'] === 'sizes',
                    'size_type' => $validated['pricing_mode'] === 'sizes'
                        ? $validated['size_type']
                        : null,

                    // These columns must exist in your migration
                    // and be included in MenuItem::$fillable.
                    'description' => $validated['description'] ?? null,
                    'image' => $imagePath,
                ]);

                if (!empty($sizesToCreate)) {
                    $menuItem->sizes()->createMany($sizesToCreate);
                }

                return $menuItem;
            });

            // Return the newly created menu item and its relationships.
            $menuItem->load(['category', 'sizes']);

            activityLog(
                'menu_item_created',
                'Menu item created',
                [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully',
                'data' => new AdminMenuItemResource($menuItem),
            ], 201);

        } catch (Throwable $exception) {
            // Remove the uploaded image if database saving fails.
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Ensure the menu item belongs to this restaurant.
        if ((int) $menuItem->restaurant_id !== (int) $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $restaurantId = $restaurant->id;

        // Validate menu item fields.
        $validated = $request->validate([
            'category_id' => [
                'sometimes',
                'required',
                Rule::exists('categories', 'id')
                    ->where('restaurant_id', $restaurantId),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],

            'pricing_mode' => [
                'sometimes',
                'required',
                Rule::in(['single', 'sizes']),
            ],

            'price' => [
                'nullable',
                'required_if:pricing_mode,single',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'size_type' => [
                'required_if:pricing_mode,sizes',
                'nullable',
                Rule::in(['hf', 'sml']),
            ],

            'sizes' => [
                'required_if:pricing_mode,sizes',
                'array',
            ],

            'sizes.*.price' => [
                'required_if:pricing_mode,sizes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'is_vegetarian' => [
                'sometimes',
                'boolean',
            ],

            'is_popular' => [
                'sometimes',
                'boolean',
            ],

            'is_available' => [
                'sometimes',
                'boolean',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'remove_image' => [
                'sometimes',
                'boolean',
            ],
        ]);

        // Prevent duplicate menu item names within the same restaurant.
        if (isset($validated['name'])) {
            $exists = MenuItem::where('restaurant_id', $restaurantId)
                ->where('name', $validated['name'])
                ->where('id', '!=', $menuItem->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A menu item with this name already exists.',
                ], 422);
            }
        }

        $sizeDefinitions = [
            'hf' => [
                ['name' => 'Half', 'code' => 'half'],
                ['name' => 'Full', 'code' => 'full'],
            ],

            'sml' => [
                ['name' => 'Small', 'code' => 'small'],
                ['name' => 'Medium', 'code' => 'medium'],
                ['name' => 'Large', 'code' => 'large'],
            ],
        ];

        // If pricing_mode is omitted, preserve the current pricing mode.
        $pricingMode = $validated['pricing_mode']
            ?? ($menuItem->has_sizes ? 'sizes' : 'single');

        $sizeType = $validated['size_type']
            ?? $menuItem->size_type;

        $sizesToCreate = [];
        $basePrice = $menuItem->price;

        if ($pricingMode === 'sizes') {
            if (!isset($sizeDefinitions[$sizeType])) {
                throw ValidationException::withMessages([
                    'size_type' => [
                        'Please select a valid size type.',
                    ],
                ]);
            }

            // Require sizes when switching to size-based pricing.
            if (!isset($validated['sizes'])) {
                throw ValidationException::withMessages([
                    'sizes' => [
                        'Please provide prices for all selected sizes.',
                    ],
                ]);
            }

            $definitions = $sizeDefinitions[$sizeType];
            $submittedSizes = $validated['sizes'];

            // Require exactly the expected indexed sizes.
            if (
                count($submittedSizes) !== count($definitions) ||
                array_keys($submittedSizes) !==
                range(0, count($definitions) - 1)
            ) {
                throw ValidationException::withMessages([
                    'sizes' => [
                        'Please provide a price for every selected size.',
                    ],
                ]);
            }

            foreach ($definitions as $index => $definition) {
                $sizesToCreate[] = [
                    ...$definition,
                    'price' => $submittedSizes[$index]['price'],
                    'sort_order' => $index + 1,
                    'is_available' => true,
                ];
            }

            // Use Full or Medium as the base price.
            $basePrice = $submittedSizes[1]['price'];
        } else {
            // Single pricing must have a price when switching modes.
            if (
                $pricingMode === 'single' &&
                !array_key_exists('price', $validated)
            ) {
                if ($menuItem->has_sizes) {
                    throw ValidationException::withMessages([
                        'price' => [
                            'Please provide a price for single-price items.',
                        ],
                    ]);
                }
            }

            if (array_key_exists('price', $validated)) {
                $basePrice = $validated['price'];
            }
        }

        $oldImage = $menuItem->image;
        $newImage = null;
        $removeImage = ($validated['remove_image'] ?? false) === true
            || ($validated['remove_image'] ?? null) === 1
            || ($validated['remove_image'] ?? null) === '1';

        try {
            // Save the new image before updating the database.
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')
                    ->store('menu-items', 'public');
            }

            DB::transaction(function () use ($menuItem, $validated, $pricingMode, $sizeType, $basePrice, $sizesToCreate, $newImage, $removeImage) {
                $updates = [];

                foreach ([
                    'category_id',
                    'name',
                    'is_vegetarian',
                    'is_popular',
                    'is_available',
                    'description',
                ] as $field) {
                    if (array_key_exists($field, $validated)) {
                        $updates[$field] = $validated[$field];
                    }
                }

                $updates['price'] = $basePrice;
                $updates['has_sizes'] = $pricingMode === 'sizes';
                $updates['size_type'] = $pricingMode === 'sizes'
                    ? $sizeType
                    : null;

                if ($newImage !== null) {
                    $updates['image'] = $newImage;
                } elseif ($removeImage) {
                    $updates['image'] = null;
                }

                $menuItem->update($updates);

                // Synchronize size records instead of duplicating them.
                if ($pricingMode === 'sizes') {
                    $menuItem->sizes()->delete();
                    $menuItem->sizes()->createMany($sizesToCreate);
                } else {
                    // Remove obsolete sizes when switching to single pricing.
                    $menuItem->sizes()->delete();
                }
            });

            // Delete the previous image only after a successful database update.
            if (
                ($newImage !== null || $removeImage) &&
                $oldImage &&
                $oldImage !== $newImage
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            $menuItem->refresh()->load(['category', 'sizes']);

            activityLog(
                'menu_item_updated',
                'Menu item updated',
                [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'changes' => $menuItem->getChanges(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Menu item updated successfully.',
                'data' => new AdminMenuItemResource($menuItem),
            ]);
        } catch (Throwable $exception) {
            // Remove the new upload if saving failed.
            if ($newImage !== null) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }
    }
    public function updateAvailability(Request $request, MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check
        if ($menuItem->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $menuItem->update([
            'is_available' => $validated['is_available']
        ]);

        activityLog(
            'menu_item_updated',
            'Menu item updated',
            [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'changes' => $menuItem->getChanges()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Menu availability updated successfully',
            'data' => new AdminMenuItemResource($menuItem),
        ]);
    }

    public function show(MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check: restaurant can only fetch its own items
        if ($menuItem->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $menuItem->load(['restaurant', 'category', 'sizes']);

        return response()->json([
            'success' => true,
            'data' => new AdminMenuItemResource($menuItem),
        ]);
    }


    public function importMenu(Request $request)
    {
        // TODO: add the limit check for menu items before importing
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $restaurant = auth('restaurant')->user();

        if (!$restaurant) {
            return response()->json([
                'message' => 'Unauthenticated restaurant.',
            ], 401);
        }

        $uploadedFile = $request->file('file');
        $file = fopen($uploadedFile->getRealPath(), 'r');

        if (!$file) {
            return response()->json([
                'message' => 'Unable to read the uploaded CSV file.',
            ], 422);
        }

        $imported = 0;
        $updated = 0;
        $totalRows = 0;
        $failed = [];

        try {
            // Read CSV headers
            $headers = fgetcsv($file);

            if (!$headers) {
                throw ValidationException::withMessages([
                    'file' => 'The CSV file is empty.',
                ]);
            }

            // Remove UTF-8 BOM and normalize headers
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

            $headers = array_map(
                fn($header) => strtolower(trim($header)),
                $headers
            );

            $requiredHeaders = [
                'category',
                'name',
                'price',
                'is_vegetarian',
                'is_popular',
                'is_available',
                'has_sizes',
                'size_type',
                'sizes',
            ];

            if (array_diff($requiredHeaders, $headers)) {
                throw ValidationException::withMessages([
                    'file' => 'CSV headers do not match the required menu template.',
                ]);
            }

            $rowNumber = 1;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                $totalRows++;

                if (count($row) !== count($headers)) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'error' => 'Incorrect number of columns.',
                    ];
                    continue;
                }

                $data = array_combine($headers, $row);

                if (
                    trim($data['category']) === '' ||
                    trim($data['name']) === ''
                ) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'error' => 'Category and name are required.',
                    ];
                    continue;
                }

                $validator = Validator::make($data, [
                    'category' => ['required', 'string', 'max:100'],
                    'name' => ['required', 'string', 'max:255'],
                    'price' => ['required', 'numeric', 'min:0'],
                    'is_vegetarian' => ['required', 'in:true,false,1,0'],
                    'is_popular' => ['required', 'in:true,false,1,0'],
                    'is_available' => ['required', 'in:true,false,1,0'],
                    'has_sizes' => ['required', 'in:true,false,1,0'],
                    'size_type' => ['nullable', 'string', 'max:50'],
                    'sizes' => ['nullable', 'string'],
                ]);

                if ($validator->fails()) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'error' => $validator->errors()->first(),
                    ];
                    continue;
                }

                try {
                    $sizes = [];

                    if (trim($data['sizes'] ?? '') !== '') {
                        $sizes = json_decode(
                            $data['sizes'],
                            true,
                            512,
                            JSON_THROW_ON_ERROR
                        );

                        if (!is_array($sizes) || !array_is_list($sizes)) {
                            throw new \RuntimeException(
                                'Sizes must be a JSON array.'
                            );
                        }

                        foreach ($sizes as $size) {
                            $sizeValidator = Validator::make(
                                is_array($size) ? $size : [],
                                [
                                    'name' => ['required', 'string', 'max:100'],
                                    'code' => ['nullable', 'string', 'max:50'],
                                    'price' => ['required', 'numeric', 'min:0'],
                                    'sort_order' => ['nullable', 'integer', 'min:0'],
                                    'is_available' => [
                                        'nullable',
                                        'in:true,false,1,0',
                                    ],
                                ]
                            );

                            if ($sizeValidator->fails()) {
                                throw new \RuntimeException(
                                    'Invalid serving size: ' .
                                    $sizeValidator->errors()->first()
                                );
                            }
                        }
                    }

                    $hasSizes = in_array(
                        strtolower(trim($data['has_sizes'])),
                        ['true', '1'],
                        true
                    );

                    if ($hasSizes && empty($sizes)) {
                        throw new \RuntimeException(
                            'Sizes are required when has_sizes is true.'
                        );
                    }

                    if (!$hasSizes && !empty($sizes)) {
                        throw new \RuntimeException(
                            'Set has_sizes to true when sizes are provided.'
                        );
                    }

                    DB::transaction(function () use ($restaurant, $data, $sizes, $hasSizes, &$imported, &$updated) {
                        // Find or create category for this restaurant
                        $category = $restaurant->categories()
                            ->firstOrCreate([
                                'name' => ucfirst(trim($data['category'])),
                            ]);

                        $attributes = [
                            'price' => $data['price'],
                            'is_vegetarian' => in_array(
                                strtolower($data['is_vegetarian']),
                                ['true', '1'],
                                true
                            ),
                            'is_popular' => in_array(
                                strtolower($data['is_popular']),
                                ['true', '1'],
                                true
                            ),
                            'is_available' => in_array(
                                strtolower($data['is_available']),
                                ['true', '1'],
                                true
                            ),
                            'has_sizes' => $hasSizes,
                            'size_type' => $hasSizes
                                ? (trim($data['size_type'] ?? '') ?: null)
                                : null,
                        ];

                        // Avoid duplicate menu items on repeated imports
                        $menuItem = $restaurant->menuItems()
                            ->where('category_id', $category->id)
                            ->where('name', trim($data['name']))
                            ->first();

                        if ($menuItem) {
                            $menuItem->update($attributes);
                            $updated++;
                        } else {
                            $menuItem = $restaurant->menuItems()->create(
                                array_merge($attributes, [
                                    'category_id' => $category->id,
                                    'name' => trim($data['name']),
                                ])
                            );

                            $imported++;
                        }

                        // Replace sizes with the latest CSV data
                        $menuItem->sizes()->delete();

                        if ($hasSizes) {
                            $menuItem->sizes()->createMany(
                                array_map(
                                    fn($size, $index) => [
                                        'name' => $size['name'],
                                        'code' => $size['code'] ?? null,
                                        'price' => $size['price'],
                                        'sort_order' => $size['sort_order']
                                            ?? $index + 1,
                                        'is_available' => in_array(
                                            strtolower(
                                                (string) ($size['is_available'] ?? 'true')
                                            ),
                                            ['true', '1'],
                                            true
                                        ),
                                    ],
                                    $sizes,
                                    array_keys($sizes)
                                )
                            );
                        }

                    });
                } catch (JsonException | \RuntimeException $e) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                    ];
                } catch (\Throwable $e) {
                    report($e);

                    $failed[] = [
                        'row' => $rowNumber,
                        'error' => 'Unable to import this menu item.',
                    ];
                }
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid CSV file.',
                'errors' => $e->errors(),
            ], 422);
        } finally {
            fclose($file);
        }

        activityLog(
            'menu_imported',
            'Menu CSV import completed',
            [
                'file_name' => $uploadedFile->getClientOriginalName(),
                'total_rows' => $totalRows,
                'imported' => $imported,
                'updated' => $updated,
                'failed' => count($failed),
                'failed_rows' => $failed,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Menu CSV import completed.',
            'imported' => $imported,
            'updated' => $updated,
            'failed_count' => count($failed),
            'failed_rows' => $failed,
        ]);
    }






}
