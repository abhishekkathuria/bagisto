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
use Webkul\Attribute\Models\Attribute;

class FileImport implements ShouldQueue
{
    use Queueable, Batchable;

    protected $chunk;
    protected $locale;

    private const CATEGORYNAMEMAP = [
        'DAMES' => 'DAMESSCHOENEN',
        'HEREN' => 'HERENSCHOENEN',
    ];

    private const CATEGORY_DAMES = [
        "DAMES" => "DAMESSCHOENEN",
        "SNEAKER" => "Sneakers",
        "SLIPPER" => "Slippers",
        "SANDAAL" => "Sandalen",
        "PUMP" => "Pumps",
        "MOCASSIN" => "Mocassins",
        "MUIL" => "Muiltjes",
        "MOLIERE" => "Veterschoenen",
        "BALLERINA" => "Ballerinas",
        "BOTTINE" => "Bottines",
        "PANTOFFEL" => "Pantoffels",
        "LAARS" => "Laarzen",
        "ENKELLAARS" => "Enkellaarzen",
        "ESPADRILLES" => "ESPADRILLES",
        "OPEN" => "Open",
        "CASUAL" => "Casual",
        "GESLOTEN" => "Gesloten",
        "LAGE SCHOEN" => "Lage schoen",
    ];

    private const CATEGORY_HEREN = [
        "HEREN" => "HERENSCHOENEN",
        "BOTTINE" => "Bottines",
        "CASUAL" => "Casual",
        "ENKELLAARS" => "Hoge schoenen",
        "MOCASSIN" => "Loafers",
        "MOLIERE" => "Moliere",
        "PANTOFFEL" => "Pantoffels",
        "SANDAAL" => "Sandalen",
        "SLIPPER" => "Slippers",
        "SNEAKER" => "Sneakers",
    ];

    private const CATEGORY_ACCESSORIES = [
        "HANDTAS" =>  "Handtassen",
        "ONDERHOUDSPRODUKTEN" => "Onderhoudsproducten",
        "PANTY" =>  "Panty",
        "RIEM" =>  "Riem",
        "SOKKEN" =>  "Sokken",
        "ZOLEN" =>  "Zolen",
        "SJAALS" =>  "Sjaals",
    ];

    private const COLORMAP = [
        'ANDERE'        => "#1111c2",
        'AZZURO'        => "#1111c2",
        'BEIGE'         => "#f5f5dc",
        'BLAUW'         => "#1111c2",
        'BLAUW - J'     => "#1111c2",
        'BLAUW-JEA'     => "#1111c2",
        'BLW-JEANS'     => "#1111c2",
        'BORDEAUX'      => "#45150D",
        'BRONS'         => "#c94c48",
        'BRUIN'         => "#79553d",
        'CAMEL'         => "#79553d",
        'CIELO'         => "#1111c2",
        'COGNAC'        => "#79553d",
        'CUOIO'         => "#79553d",
        'D-Pump GIULIA' => "#ff0000",
        'FANGO'         => "#c0c0c0",
        'FUXIA'         => "#f400a1",
        'GEEL'          => "#ffff00",
        'GHIACCIO'      => "#ffffff",
        'GOUD'          => "#d4af37",
        'GRIJS'         => "#808080",
        'GROEN'         => "#008000",
        'KAKI'          => "#008000",
        'KAKKI'         => "#008000",
        'MULTICOLOR'    => "#ffffff",
        'NATUREL'       => "#79553d",
        'PINK'          => "#f3c4cf",
        'ORANJE'        => "#ffa500",
        'PAARS'         => "#800080",
        'PEARL'         => "#800080",
        'ROEST'         => "#79553d",
        'ROOD'          => "#ff0000",
        'ROSE'          => "#f3c4cf",
        'TABACCO'       => "#79553d",
        'TAUPE'         => "#d3c3b0",
        'TEINT'         => "#c0c0c0",
        'TERRACOTT'     => "#79553d",
        'WIT'           => "#ffffff",
        'ZILVER'        => "#d0d2d1",
        'ZWART'         => "#0b0013",
        'ZWART+WIT'     => "#0b0013",
        'ZWART-WIT'     => "#0b0013",
        'MARRON'        => "#800000",
        'BLAUW-JEANS'   => "#5DADEC",
        'VIOLA'         => "#7f678e",
        'GUN-METALLIC'  => "#818589",
        'MINT'          => "#3EB489",
        'WIT-ZWART'     => "#0b0013",
        'MAUVE'         => "#E0B0FF",
        'WIT-BLAUW'     => "#800080",
        'WIT GOUD'      => "#79553d"
    ];

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

        $imageDirectory = storage_path('app/import/product-images');
        // Get all image files
        $allImages = File::files($imageDirectory);

        foreach ($collection as $key => $record) {
            $categoryName = $record[11];
            $color = $record[12];
            $size = $record[5];
            $name = $record[0];
            $description = $record[3];
            $price = $record[7];
            $qty = $record[6];
            $brand = $record[13];
            $special_price = $record[9];
            $season = explode('.', $record[1])[0];

            if ($key == 0 || !$name || !$categoryName || !$price || (substr_count($categoryName, '-') === 2)) continue;

            $categories = $this->createCategoryAndSubCategory($categoryName);

            $attribute = $this->createAttributes($color, $size, $brand, $season);

            $result = $this->createProduct($name, $description, $price, $qty, $special_price, $attribute, $categories, $key);

            if ($result['addImages']) {
                $this->assignImages($result['product'], $name, $allImages);
            }
        }
    }

    public function createCategoryAndSubCategory($categoryName)
    {
        $categories = [];
        // Category Part Done here
        $categoryTranslation = app('Webkul\Category\Models\CategoryTranslation');

        if (Str::contains($categoryName, '-')) {
            [$mainName, $subName] = explode('-', $categoryName, 2);
            $maincategoryName = self::CATEGORYNAMEMAP[$mainName] ?? $mainName;

            if ($maincategoryName == 'DAMESSCHOENEN') {
                $subName = self::CATEGORY_DAMES[$subName];
            } else if ($maincategoryName == 'HERENSCHOENEN') {
                $subName = self::CATEGORY_HEREN[$subName];
            }

            $categoryName = $maincategoryName . '-' . $subName;
        } else {
            $subName = self::CATEGORY_ACCESSORIES[$categoryName] ?? $categoryName;
        }

        $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
        if (Str::contains($categoryName, '-')) {
            $categoryExist = $categoryTranslation->where('name', $maincategoryName)->first();
        } else {
            $categoryExist = $categoryTranslation->where('name', 'ACCESSOIRES')->first();

            if (!$categoryExist) {
                $categoryExist = $categoryRepo->create([
                    'locale' => 'all',
                    'name' => 'ACCESSOIRES',
                    'description' => 'ACCESSOIRES',
                    'slug' => Str::slug('ACCESSOIRES'),
                    'status' => 1,
                    'position' => 1,
                    'display_mode' => 'products_and_description',
                    'parent_id' => 1,
                    'attributes' => [11, 23, 24, 25]
                ]);
            }
        }

        if (!$categoryExist) {
            if (Str::contains($categoryName, '-')) {
                [$mainNameCategory, $subNameCategory] = explode('-', $categoryName, 2);

                $categoryExist = $categoryRepo->create([
                    'locale' => 'all',
                    'name' => $mainNameCategory,
                    'description' => $mainNameCategory,
                    'slug' => Str::slug($mainNameCategory),
                    'status' => 0,
                    'position' => 1,
                    'display_mode' => 'products_and_description',
                    'parent_id' => 1,
                    'attributes' => [11, 23, 24, 25]
                ]);
            }
        }
        $parentCategory = $categoryRepo->find($categoryExist->category_id);
        if ($parentCategory) {
            $subCategoryExist = $parentCategory->children()
                ->whereHas('translations', function ($query) use ($subName) {
                    $query->where('name', $subName);
                })->first();

            if (!$subCategoryExist && $subName) {
                $subCategoryExist = $categoryRepo->create([
                    'locale' => 'all',
                    'name' => $subName,
                    'description' => $subName,
                    'slug' => Str::slug($subName),
                    'status' => 0,
                    'position' => 1,
                    'display_mode' => 'products_and_description',
                    'parent_id' => $categoryExist->category_id ?? $categoryExist->id,
                    'attributes' => [11, 23, 24, 25]
                ]);

                Event::dispatch('catalog.category.create.after', $subCategoryExist);
            }
        }
        return [$categoryExist->category_id, $subCategoryExist->id];
        // Category Part Done here
    }

    public function createAttributes($color, $size, $brand, $season)
    {
        // Attribute Part Done here
        $attributeOptionRepository = app('Webkul\Attribute\Repositories\AttributeOptionRepository');

        $colorAttribute = Attribute::where('code', 'color')->first();
        $colorRow = $attributeOptionRepository->findOneWhere(['admin_name' => $color, 'attribute_id' => $colorAttribute->id])
            ?: $attributeOptionRepository->create(['attribute_id' => $colorAttribute->id, 'admin_name' => $color, 'swatch_value' => self::COLORMAP[$color]]);

        $sizeAttribute = Attribute::where('code', 'size')->first();
        $sizeRow = $attributeOptionRepository->findOneWhere(['admin_name' => $size, 'attribute_id' => $sizeAttribute->id])
            ?: $attributeOptionRepository->create(['attribute_id' => $sizeAttribute->id, 'admin_name' => $size]);

        $brandAttribute = Attribute::where('code', 'brand')->first();
        $brandRow = $attributeOptionRepository->findOneWhere(['admin_name' => $brand, 'attribute_id' => $brandAttribute->id])
            ?: $attributeOptionRepository->create(['attribute_id' => $brandAttribute->id, 'admin_name' => $brand]);

        $seasonAttribute = Attribute::where('code', 'season')->first();
        $seasonRow = $attributeOptionRepository->findOneWhere(['admin_name' => $season, 'attribute_id' => $seasonAttribute->id])
            ?: $attributeOptionRepository->create(['attribute_id' => $seasonAttribute->id, 'admin_name' => $season]);

        return [
            'color' => $colorRow->id,
            'size' => $sizeRow->id,
            'brand' => $brandRow->id,
            'season' => $seasonRow->id
        ];
        // Attribute Part Done here

    }

    public function createProduct($name, $description, $price, $qty, $special_price, $attribute, $categories, $key)
    {
        // Product Part Done here
        $productRepository = app('Webkul\Product\Repositories\ProductRepository');

        $productExist = $productRepository->findOneWhere(['sku' => $name]);

        $isProductAlreadyCreated = true;

        $productArr = [
            "channel" => "default",
            "locale" => $this->locale,
            // "sku" => $name,
            "name" => $name,
            "url_key" => Str::slug($name),
            "color" => $attribute['color'],
            "size" => $attribute['size'],
            "allow_backorder" => 1,
            "short_description" => $description,
            "description" => $description,
            "season" => $attribute['season'],
            "new" => 1,
            "featured" => 1,
            "visible_individually" => 1,
            "status" => 1,
            "weight" => 0,
            "guest_checkout" => 1,
            "channels" => [1],
            "categories" => $categories,
            "brand" => $attribute['brand'],
        ];

        $addImages = false;

        if (! $productExist) {
            $isProductAlreadyCreated = false;

            $productExist = $productRepository->create([
                'sku' => $name,
                'type' => 'configurable',
                'attribute_family_id' => 1,
            ]);
        }

        $addImages = true;

        array_merge($productArr, [
            'sku'       => $name . '-' . $key,
            'url_key'   => Str::slug($name) . '-' . $key,
            'parent_id' => $productExist->id,
        ]);

        if ($qty) {
            $productArr['manage_stock'] = 1;
            $productArr['inventories'] = [1 => $qty];
        }

        $product = $productRepository->update($productArr, $productExist->id);

        Event::dispatch('catalog.product.update.after', $product);

        if (! $isProductAlreadyCreated) {
            foreach ([23, 24] as $attributeCode) {
                DB::table('product_super_attributes')
                    ->where('product_id', $product->id)
                    ->where('attribute_id', $attributeCode)
                    ->delete();

                DB::table('product_super_attributes')->insert([
                    'product_id'   => $product->id,
                    'attribute_id' => $attributeCode,
                ]);
            }
        }

        $sku = $name . "-variant-" . $attribute['size'] . "-" . $attribute['color'];
        $productExistWithSku = $productRepository->findOneWhere(['sku' => $sku]);

        if (! $productExistWithSku) {
            $productExistWithSku = $productRepository->create([
                'sku' => $sku,
                'type' => 'simple',
                'attribute_family_id' => 1,
                'parent_id' => $productExist->id,
            ]);
        }

        unset($productArr['season']);
        $productArr['sku'] = $sku;
        $productArr['parent_id'] = $productExist->id;
        $productArr['visible_individually'] = 0;
        $productArr['price'] = $price;
        $productArr['cost'] = $price;
        $productArr['special_price'] = $special_price;
        $productArr['manage_stock'] = 1;
        $productArr['inventories'] = [1 => $qty];

        $product = $productRepository->update($productArr, $productExistWithSku->id);

        Event::dispatch('catalog.product.update.after', $productExistWithSku);

        return ['product' => $productExist, 'addImages' => $addImages];
        // Product Part Done here
    }

    public function assignImages($productExist, $name, $images)
    {
        $skuPrefix = $name . '-';
        // Product Images Part done here
        foreach ($images as $image) {
            if (str_starts_with($image->getFilename(), $skuPrefix)) {
                $destinationPath = 'product/' . $productExist->id . '/';

                // Make sure the destination folder exists
                Storage::makeDirectory($destinationPath);
                // Copy the image to the destination folder
                Storage::put($destinationPath . $image->getFilename(), File::get($image->getPathname()));
                ProductImage::create([
                    'product_id' => $productExist->id,
                    'path' => $destinationPath . $image->getFilename(),
                    'type' => 'images',
                ]);
            }
        }
        // Product Images Part done here
    }
}
