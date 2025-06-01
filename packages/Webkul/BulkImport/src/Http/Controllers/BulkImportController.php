<?php

namespace Webkul\BulkImport\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\BulkImport\Imports\CustomFileImport;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductImage;

class BulkImportController extends Controller
{
    /**
     * Display the bulk import form.
     */
    public function importfile()
    {
        $locales = core()->getCurrentChannel()->locales()->orderBy('name')->get();

        return view('bulk_import::bulk-import.import', compact('locales'));
    }

    /**
     * Bulk Import
     */
    public function customFileUpload()
    {
        $file = request()->file('file');

        $locale = request()->locale;

        $import = new CustomFileImport($locale);

        Excel::import($import, $file);

        return redirect()->back()->with('success', trans('bulk_import::app.bulk_import.file_upload'));
    }

    /**
     * Upload Product Images
     */
    public function uploadProductImages(HttpRequest $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        foreach ($request->file('images') as $image) {
            $originalName = $image->getClientOriginalName(); // e.g., SKU-1.jpg
            $sku = pathinfo($originalName, PATHINFO_FILENAME); // e.g., SKU-1

            $sku = explode('-', $sku)[0];

            // Optionally handle lowercase filenames
            $sku = strtolower($sku);

            // Find product by SKU
            $product = Product::where('sku', $sku)->first();
            
            if ($product) {
                $folder = 'product/' . $product->id;
                $image->storeAs($folder, $originalName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $folder . '/' . $originalName,
                    'type' => 'images',
                ]);
            } else {
                // Log missing SKU or return a message if needed
                \Log::warning("Product not found for SKU: {$sku}");
            }
        }

        return redirect()->back()->with('success', trans('bulk_import::app.bulk_import.upload_images'));
    }
}
