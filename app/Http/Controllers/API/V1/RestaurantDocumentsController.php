<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RestaurantDocuments;

class RestaurantDocumentsController extends Controller
{
    public function storeMultiple(Request $request)
    {

        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*.file' => 'required|file|mimes:jpg,png,webp,pdf|max:2048',
            'documents.*.type' => 'required|string'
        ]);


        foreach ($request->input('documents', []) as $key => $doc) {

            $type = $doc['type'];

            
            $exists = RestaurantDocuments::where('restaurant_id', $request->user()->id)
            ->where('document_type', $type)
            ->exists();


            if ($exists) {
                return response()->json([
                    'message' => "Document type '{$type}' already exists."
                ], 422);
            }

            $file = $request->file("documents.$key.file");

            $path = $file->store('restaurant_documents', 'public');

            RestaurantDocuments::create([
                'restaurant_id' => $request->user()->id,
                'document_type' => $type,
                'document_path' => $path,
                'verified_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Documents uploaded successfully and pending verification'
        ]);
    }



}