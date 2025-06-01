<?php

namespace Webkul\BulkImport\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Product\Models\ProductImage;
use Illuminate\Bus\Batchable;

class FileImport implements ShouldQueue
{
    use Queueable, Batchable;

    protected $chunk;
    protected $locale;

    /**
     * Create a new job instance.
     */
    public function __construct(array $chunk, string $locale)
    {
        $this->locale = $locale;
        $this->chunk = $chunk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $collection = collect($this->chunk);

        foreach ($collection as $key => $record) {
            $categoryName = $record[11];
            $color = $record[12];
            $size = $record[5];
            $name = $record[0];
            $description = $record[3];
            $price = $record[7];
            $qty = $record[6];
            $brand = $record[13];

            if ($key == 0 || !$name || !$categoryName) continue;
            if ($key == 0 || ! $record[0]) {
                continue;
            }

            $count = 0;
            $categoryTranslation = app('Webkul\Category\Models\CategoryTranslation');

            if (Str::contains($categoryName, '-')) {
                [$mainName, $subName] = explode('-', $categoryName, 2);
                $maincategoryName = $mainName;
            } else {
                $maincategoryName = $categoryName;
            }

            $categoryExist = $categoryTranslation->where('name', $maincategoryName)->first();

            $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
            if (!$categoryExist) {
                if (Str::contains($categoryName, '-')) {
                    [$mainNameCategory, $subNameCategory] = explode('-', $categoryName, 2);

                    $mainCategory = $categoryRepo->create([
                        'locale' => 'all',
                        'name' => $mainNameCategory,
                        'description' => $mainNameCategory,
                        'slug' => Str::slug($mainNameCategory),
                        'status' => 1,
                        'position' => 1,
                        'display_mode' => 'products_and_description',
                        'parent_id' => 1,
                        'attributes' => [11, 23, 24, 25]
                    ]);

                    $category = $categoryRepo->create([
                        'locale' => 'all',
                        'name' => $subNameCategory,
                        'description' => $subNameCategory,
                        'slug' => Str::slug($subNameCategory),
                        'status' => 1,
                        'position' => 1,
                        'display_mode' => 'products_and_description',
                        'parent_id' => $mainCategory->id,
                        'attributes' => [11, 23, 24, 25]
                    ]);

                    Event::dispatch('catalog.category.create.after', $category);
                } else {
                    $category = $categoryRepo->create([
                        'locale' => 'all',
                        'name' => $categoryName??'Dummy',
                        'description' => $categoryName,
                        'slug' => Str::slug($categoryName),
                        'status' => 1,
                        'position' => 1,
                        'display_mode' => 'products_and_description',
                        'parent_id' => 1,
                        'attributes' => [11, 23, 24, 25]
                    ]);
            if (! $categoryExist) {
                $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
                $category = $categoryRepo->create([
                    'locale'       => 'en',
                    'name'         => $record[6],
                    'description'  => $record[6],
                    'slug'         => Str::slug($record[6]),
                    'status'       => 1,
                    'position'     => 1,
                    'display_mode' => 'products_and_description',
                    'parent_id'    => 1,
                    'attributes'   => [11, 23, 24, 25],
                ]);

                    Event::dispatch('catalog.category.create.after', $category);
                }
            } else {
                $category = $categoryExist;
            }

            $attributeOptionRepository = app('Webkul\Attribute\Repositories\AttributeOptionRepository');

            $colorRow = $attributeOptionRepository->findOneWhere(['admin_name' => $color, 'attribute_id' => 23])
                ?: $attributeOptionRepository->create(['attribute_id' => 23, 'admin_name' => $color]);

            $sizeRow = $attributeOptionRepository->findOneWhere(['admin_name' => $size, 'attribute_id' => 24])
                ?: $attributeOptionRepository->create(['attribute_id' => 24, 'admin_name' => $size]);

            $brandRow = $attributeOptionRepository->findOneWhere(['admin_name' => $brand, 'attribute_id' => 25])
                ?: $attributeOptionRepository->create(['attribute_id' => 25, 'admin_name' => $brand]);

            $productRepository = app('Webkul\Product\Repositories\ProductRepository');

            $productExist = $productRepository->findOneWhere(['sku' => $name]);
            if (!$productExist) {
            $productExist = $productRepository->findOneWhere(['sku' => $record[0]]);
            if (! $productExist) {
                $productExist = $productRepository->create([
                    'sku' => $name,
                    'type' => 'simple',
                    'sku'                 => $record[0],
                    'type'                => 'simple',
                    'attribute_family_id' => 1,
                ]);

                Event::dispatch('catalog.product.create.after', $productExist);
            }

            $productArr = [
                "channel" => "default",
                "locale" => $this->locale,
                "sku" => $name,
                "name" => $name,
                "url_key" => Str::slug($name),
                "color" => $colorRow->id,
                "size" => $sizeRow->id,
                "allow_backorder" => 1,
                "short_description" => $description,
                "description" => $description,
                "price" => $price,
                "cost" => $price,
                "new" => 1,
                "featured" => 1,
                "visible_individually" => 1,
                "status" => 1,
                "weight" => 0,
                "guest_checkout" => 1,
                "channels" => [1],
                "categories" => [$category->category_id ?? $category->id],
                "brand" => $brandRow->id,
                'channel'              => 'default',
                'locale'               => 'en',
                'sku'                  => $record[0],
                'name'                 => $record[7],
                'url_key'              => Str::slug($record[7]),
                'color'                => $colorRow->id,
                'size'                 => $sizeRow->id,
                'allow_backorder'      => '1',
                'short_description'    => $record[1],
                'description'          => $record[1],
                'price'                => $record[5],
                'cost'                 => $record[5],
                'new'                  => '1',
                'featured'             => '1',
                'visible_individually' => '1',
                'status'               => '1',
                'weight'               => 0,
                'guest_checkout'       => '1',
                'channels'             => [1],
                'categories'           => [$category->id],
            ];

            if ($qty) {
                $productArr['manage_stock'] = 1;
                $productArr['inventories'] = [1 => $qty];
            if ($record[4]) {
                $productArr['manage_stock'] = '1';
                $productArr['inventories'] = [1 => $record[4]];
            }

            $product = $productRepository->update($productArr, $productExist->id);

            // Define the SKU prefix and source folder
            $imageDirectory = storage_path('app/import/product-images');
            $skuPrefix = $name . '-';

            // Get all matching files
            $matchingImages = File::files($imageDirectory);

            foreach ($matchingImages as $image) {
                if (str_starts_with($image->getFilename(), $skuPrefix)) {
                    $destinationPath = 'product/' . $productExist->id . '/';

                    // Make sure the destination folder exists
                    Storage::makeDirectory($destinationPath);

                    // Copy the image to the destination folder
                    Storage::put($destinationPath . $image->getFilename(), File::get($image->getPathname()));

                    ProductImage::create([
                        'product_id' => $productExist->id,
                        'path' => $destinationPath . '/' . $image->getFilename(),
                        'type' => 'images',
                    ]);
                }
            }

            Event::dispatch('catalog.product.update.after', $product);

            foreach ([23, 24] as $attribute) {
                DB::table('product_super_attributes')
                    ->where('product_id', $product->id)
                    ->where('attribute_id', $attribute)
                    ->delete();

                DB::table('product_super_attributes')->insert([
                    'product_id'   => $product->id,
                    'attribute_id' => $attribute,
                ]);
            }

            foreach ($collection as $row) {
                if ($row[0] === $name) {
                    $count++;
                }
            }

            if ($count > 1) {
                $productExist->update([
                    'sku' => $name,
                    'type' => 'configurable',
                    'sku'                 => $record[0],
                    'type'                => 'configurable',
                    'attribute_family_id' => 1,
                ]);

                $variantProduct = $productRepository->create([
                    'sku' => $name . "-" . $key,
                    'type' => 'simple',
                    'sku'                 => $record[0].'-'.$key,
                    'type'                => 'simple',
                    'attribute_family_id' => 1,
                    'parent_id'           => $productExist->id,
                ]);

                Event::dispatch('catalog.product.update.after', $variantProduct);

                $variantArr = array_merge($productArr, [
                    'sku'       => $record[0].'-'.$key,
                    'url_key'   => Str::slug($record[7]).'-'.$key,
                    'parent_id' => $productExist->id,
                ]);

                $productRepository->update($variantArr, $variantProduct->id);
            }
        }
    }
}
