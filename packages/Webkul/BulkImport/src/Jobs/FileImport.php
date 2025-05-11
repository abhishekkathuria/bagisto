<?php

namespace Webkul\BulkImport\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class FileImport implements ShouldQueue
{
    use Queueable;

    protected $chunk;

    /**
     * Create a new job instance.
     */
    public function __construct(array $chunk)
    {
        $this->chunk = $chunk;
    }

    public function handle(): void
    {
        $collection = collect($this->chunk);

        foreach ($collection as $key => $record) {
            if ($key == 0 || !$record[0]) continue;

            $count = 0;

            $categoryTranslation = app('Webkul\Category\Models\CategoryTranslation');
            $categoryExist = $categoryTranslation->where('name', $record[6])->first();

            if (!$categoryExist) {
                $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
                $category = $categoryRepo->create([
                    'locale' => 'en',
                    'name' => $record[6],
                    'description' => $record[6],
                    'slug' => Str::slug($record[6]),
                    'status' => 1,
                    'position' => 1,
                    'display_mode' => 'products_and_description',
                    'parent_id' => 1,
                    'attributes' => [11, 23, 24, 25]
                ]);

                Event::dispatch('catalog.category.create.after', $category);
            } else {
                $category = $categoryExist;
            }

            $attributeOptionRepository = app('Webkul\Attribute\Repositories\AttributeOptionRepository');

            $colorRow = $attributeOptionRepository->findOneWhere(['admin_name' => $record[2], 'attribute_id' => 23])
                ?: $attributeOptionRepository->create(['attribute_id' => 23, 'admin_name' => $record[2]]);

            $sizeRow = $attributeOptionRepository->findOneWhere(['admin_name' => $record[3], 'attribute_id' => 24])
                ?: $attributeOptionRepository->create(['attribute_id' => 24, 'admin_name' => $record[3]]);

            $productRepository = app('Webkul\Product\Repositories\ProductRepository');

            $productExist = $productRepository->findOneWhere(['sku' => $record[0]]);
            if (!$productExist) {
                $productExist = $productRepository->create([
                    'sku' => $record[0],
                    'type' => 'simple',
                    'attribute_family_id' => 1,
                ]);
                Event::dispatch('catalog.product.create.after', $productExist);
            }

            $productArr = [
                "channel" => "default",
                "locale" => "en",
                "sku" => $record[0],
                "name" => $record[7],
                "url_key" => Str::slug($record[7]),
                "color" => $colorRow->id,
                "size" => $sizeRow->id,
                "allow_backorder" => "1",
                "short_description" => $record[1],
                "description" => $record[1],
                "price" => $record[5],
                "cost" => $record[5],
                "new" => "1",
                "featured" => "1",
                "visible_individually" => "1",
                "status" => "1",
                "weight" => 0,
                "guest_checkout" => "1",
                "channels" => [1],
                "categories" => [$category->id],
            ];

            if ($record[4]) {
                $productArr['manage_stock'] = "1";
                $productArr['inventories'] = [1 => $record[4]];
            }

            $product = $productRepository->update($productArr, $productExist->id);
            Event::dispatch('catalog.product.update.after', $product);

            foreach ([23, 24] as $attribute) {
                DB::table('product_super_attributes')
                    ->where('product_id', $product->id)
                    ->where('attribute_id', $attribute)
                    ->delete();

                DB::table('product_super_attributes')->insert([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute,
                ]);
            }

            // Handle configurable variant if duplicate SKU
            foreach ($collection as $row) {
                if ($row[0] === $record[0]) {
                    $count++;
                }
            }

            if ($count > 1) {
                $productExist->update([
                    'sku' => $record[0],
                    'type' => 'configurable',
                    'attribute_family_id' => 1,
                ]);

                $variantProduct = $productRepository->create([
                    'sku' => $record[0] . "-" . $key,
                    'type' => 'simple',
                    'attribute_family_id' => 1,
                    'parent_id' => $productExist->id,
                ]);

                Event::dispatch('catalog.product.update.after', $variantProduct);

                $variantArr = array_merge($productArr, [
                    'sku' => $record[0] . "-" . $key,
                    'url_key' => Str::slug($record[7]) . "-" . $key,
                    'parent_id' => $productExist->id,
                ]);

                $productRepository->update($variantArr, $variantProduct->id);
            }
        }
    }
}
