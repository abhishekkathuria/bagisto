<?php

namespace Webkul\Theme\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Webkul\Core\Eloquent\Repository;
use Webkul\Theme\Contracts\ThemeCustomization;
use Webkul\Theme\Models\HeroSection as ModelsHeroSection;

class ThemeCustomizationRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ThemeCustomization::class;
    }

    /**
     * Update the specified theme
     *
     * @param  array  $data
     * @param  int  $id
     */
    public function update($data, $id): ThemeCustomization
    {
        $locale = core()->getRequestedLocaleCode();

        if ($data['type'] == 'static_content') {
            $data[$locale]['options']['html'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data[$locale]['options']['html']);
            $data[$locale]['options']['css'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data[$locale]['options']['css']);
        }

        if (in_array($data['type'], ['image_carousel', 'services_content'])) {
            unset($data[$locale]['options']);
        }

        $theme = parent::update($data, $id);

        if (in_array($data['type'], ['image_carousel', 'services_content'])) {
            $this->uploadImage(request()->all(), $theme);
        }

        if (in_array($data['type'], ['hero_section'])) {
            $this->heroSection(request()->all(), $theme);
        }

        return $theme;
    }

    public function heroSection(array $data, $theme)
    {
        $heroModal = ModelsHeroSection::first();

        if (!$heroModal) {
            ModelsHeroSection::create([
                'youtube_url' => $data['youtube_url'],
                'youtube_height' => $data['youtube_height'],
                'youtube_width' => $data['youtube_width'],
                'top_image_url' => $data['top_image_url'],
                'top_image_height' => $data['top_image_height'],
                'top_image_width' => $data['top_image_width'],
                'top_image_alt' => $data['top_image_alt'],
                'bottom_image_url' => $data['bottom_image_url'],
                'bottom_image_height' => $data['bottom_image_height'],
                'bottom_image_width' => $data['bottom_image_width'],
                'bottom_image_alt' => $data['bottom_image_alt'],
            ]);
        } else {
            $heroModal->update([
                'youtube_url' => $data['youtube_url'],
                'youtube_height' => $data['youtube_height'],
                'youtube_width' => $data['youtube_width'],
                'top_image_url' => $data['top_image_url'],
                'top_image_height' => $data['top_image_height'],
                'top_image_width' => $data['top_image_width'],
                'top_image_alt' => $data['top_image_alt'],
                'bottom_image_url' => $data['bottom_image_url'],
                'bottom_image_height' => $data['bottom_image_height'],
                'bottom_image_width' => $data['bottom_image_width'],
                'bottom_image_alt' => $data['bottom_image_alt'],
            ]);

            if (isset($data['top_image_file'])) {
                if ($data['top_image_file'] instanceof UploadedFile) {
                    $manager = new ImageManager(['driver' => 'gd']); // or 'imagick' if you prefer
                
                    $path = 'theme/' . $theme->id . '/' . Str::random(40) . '.webp';
                
                    $image = $manager->make($data['top_image_file']->get())->encode('webp');
                
                    Storage::put($path, (string) $image); // cast to string to avoid binary issues
    
                    $heroModal->update([
                        'top_image' => $path
                    ]);
                }
            }

            if (isset($data['bottom_image_file'])) {
                if ($data['bottom_image_file'] instanceof UploadedFile) {
                    $manager = new ImageManager(['driver' => 'gd']); // or 'imagick' if you prefer
                
                    $path = 'theme/' . $theme->id . '/' . Str::random(40) . '.webp';
                
                    $image = $manager->make($data['bottom_image_file']->get())->encode('webp');
                
                    Storage::put($path, (string) $image); // cast to string to avoid binary issues
    
                    $heroModal->update([
                        'bottom_image' => $path
                    ]);
                }
            }
        }
    }

    /**
     * Mass update the status of themes in the repository.
     *
     * This method updates multiple records in the database based on the provided
     * theme IDs.
     *
     * @param  int  $themeIds
     * @return int The number of records updated.
     */
    public function massUpdateStatus(array $data, array $themeIds)
    {
        return $this->model->whereIn('id', $themeIds)->update($data);
    }

    /**
     * Upload images
     *
     * @return void|string
     */
    public function uploadImage(array $data, ThemeCustomization $theme)
    {
        $locale = core()->getRequestedLocaleCode();

        if (isset($data[$locale]['deleted_sliders'])) {
            foreach ($data[$locale]['deleted_sliders'] as $slider) {
                Storage::delete(str_replace('storage/', '', $slider['image']));
            }
        }

        if (! isset($data[$locale]['options'])) {
            return;
        }

        $options = [];

        foreach ($data[$locale]['options'] as $image) {
            if (isset($image['service_icon'])) {
                $options['services'][] = [
                    'service_icon' => $image['service_icon'],
                    'description'  => $image['description'],
                    'title'        => $image['title'],
                ];
            } elseif ($image['image'] instanceof UploadedFile) {
                try {
                    $manager = new ImageManager;

                    $path = 'theme/' . $theme->id . '/' . Str::random(40) . '.webp';

                    Storage::put($path, $manager->make($image['image'])->encode('webp'));
                } catch (\Exception $e) {
                    session()->flash('error', $e->getMessage());

                    return redirect()->back();
                }

                if (($data['type'] ?? '') == 'static_content') {
                    return Storage::url($path);
                }

                $options['images'][] = [
                    'image' => 'storage/' . $path,
                    'link'  => $image['link'],
                    'title' => $image['title'],
                ];
            } else {
                $options['images'][] = $image;
            }
        }

        $translatedModel = $theme->translate($locale);
        $translatedModel->options = $options ?? [];
        $translatedModel->theme_customization_id = $theme->id;
        $translatedModel->save();
    }
}
