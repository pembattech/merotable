
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Starters
    |--------------------------------------------------------------------------
    */

    'Starters' => [
        [
            'name' => 'Veg Soup',
            'price' => 120,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Chicken Soup',
            'price' => 150,
            'is_vegetarian' => false,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Chicken Sekuwa',
            'price' => 330,
            'is_vegetarian' => false,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 180],
                ['name' => 'Full', 'code' => 'full', 'price' => 330],
            ],
        ],
        [
            'name' => 'Crispy Corn',
            'price' => 220,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 120],
                ['name' => 'Full', 'code' => 'full', 'price' => 220],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Momo
    |--------------------------------------------------------------------------
    */

    'Momo' => [
        [
            'name' => 'Veg Momo',
            'price' => 150,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 90],
                ['name' => 'Full', 'code' => 'full', 'price' => 150],
            ],
        ],
        [
            'name' => 'Chicken Momo',
            'price' => 180,
            'is_vegetarian' => false,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 110],
                ['name' => 'Full', 'code' => 'full', 'price' => 180],
            ],
        ],
        [
            'name' => 'Buff Momo',
            'price' => 170,
            'is_vegetarian' => false,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 100],
                ['name' => 'Full', 'code' => 'full', 'price' => 170],
            ],
        ],
        [
            'name' => 'Jhol Momo',
            'price' => 210,
            'is_vegetarian' => false,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 130],
                ['name' => 'Full', 'code' => 'full', 'price' => 210],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Main Course
    |--------------------------------------------------------------------------
    */

    'Main Course' => [
        [
            'name' => 'Chicken Curry',
            'price' => 350,
            'is_vegetarian' => false,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 200],
                ['name' => 'Full', 'code' => 'full', 'price' => 350],
            ],
        ],
        [
            'name' => 'Paneer Curry',
            'price' => 300,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 180],
                ['name' => 'Full', 'code' => 'full', 'price' => 300],
            ],
        ],
        [
            'name' => 'Dal Bhat Set',
            'price' => 300,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rice & Noodles
    |--------------------------------------------------------------------------
    */

    'Rice & Noodles' => [
        [
            'name' => 'Veg Fried Rice',
            'price' => 200,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 120],
                ['name' => 'Full', 'code' => 'full', 'price' => 200],
            ],
        ],
        [
            'name' => 'Chicken Chowmein',
            'price' => 220,
            'is_vegetarian' => false,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 130],
                ['name' => 'Full', 'code' => 'full', 'price' => 220],
            ],
        ],
        [
            'name' => 'Chicken Fried Rice',
            'price' => 240,
            'is_vegetarian' => false,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'hf',
            'sizes' => [
                ['name' => 'Half', 'code' => 'half', 'price' => 140],
                ['name' => 'Full', 'code' => 'full', 'price' => 240],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Snacks
    |--------------------------------------------------------------------------
    */

    'Snacks' => [
        [
            'name' => 'French Fries',
            'price' => 160,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'sml',
            'sizes' => [
                ['name' => 'Small', 'code' => 'small', 'price' => 100],
                ['name' => 'Medium', 'code' => 'medium', 'price' => 130],
                ['name' => 'Large', 'code' => 'large', 'price' => 160],
            ],
        ],
        [
            'name' => 'Veg Pakoda',
            'price' => 100,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Samosa Chaat',
            'price' => 90,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Chicken Sandwich',
            'price' => 200,
            'is_vegetarian' => false,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Drinks
    |--------------------------------------------------------------------------
    */

    'Drinks' => [
        [
            'name' => 'Coke',
            'price' => 80,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Water',
            'price' => 40,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Masala Chai',
            'price' => 40,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'sml',
            'sizes' => [
                ['name' => 'Small', 'code' => 'small', 'price' => 25],
                ['name' => 'Medium', 'code' => 'medium', 'price' => 40],
                ['name' => 'Large', 'code' => 'large', 'price' => 55],
            ],
        ],
        [
            'name' => 'Black Coffee',
            'price' => 70,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => true,
            'size_type' => 'sml',
            'sizes' => [
                ['name' => 'Small', 'code' => 'small', 'price' => 45],
                ['name' => 'Medium', 'code' => 'medium', 'price' => 70],
                ['name' => 'Large', 'code' => 'large', 'price' => 95],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Desserts
    |--------------------------------------------------------------------------
    */

    'Desserts' => [
        [
            'name' => 'Ice Cream',
            'price' => 120,
            'is_vegetarian' => true,
            'is_popular' => true,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Gulab Jamun',
            'price' => 90,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
        [
            'name' => 'Kheer',
            'price' => 100,
            'is_vegetarian' => true,
            'is_popular' => false,
            'is_available' => true,
            'has_sizes' => false,
            'size_type' => null,
        ],
    ],

];
