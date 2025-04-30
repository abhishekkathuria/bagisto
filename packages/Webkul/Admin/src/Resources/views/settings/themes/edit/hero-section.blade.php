<v-hero-section :errors="errors"></v-hero-section>

@pushOnce('scripts')
<script
    type="text/x-template"
    id="v-hero-section-template">
    <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">

                <!-- YouTube Section -->
                <div class='m-2'>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">YouTube</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <input type="text" value="{{ $heroSection->youtube_url }}" name="youtube_url" placeholder="YouTube URL" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" value="{{ $heroSection->youtube_height }}" name="youtube_height" placeholder="Height" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" value="{{ $heroSection->youtube_width }}" name="youtube_width" placeholder="Width" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                    </div>
                </div>

                <!-- Top Image Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Top Image</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <input type="text" value="{{ $heroSection->top_image_url }}" name="top_image_url" placeholder="Image URL" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" value="{{ $heroSection->top_image_height }}" name="top_image_height" placeholder="Height" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" value="{{ $heroSection->top_image_width }}" name="top_image_width" placeholder="Width" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="file" name="top_image_file" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" value="{{ $heroSection->top_image_alt }}" name="top_image_alt" placeholder="Alt Text" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                    </div>
                </div>

                <!-- Bottom Image Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Bottom Image</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <input type="text" name="bottom_image_url" value="{{ $heroSection->bottom_image_url }}" placeholder="Image URL" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" name="bottom_image_height" value="{{ $heroSection->bottom_image_height }}" placeholder="Height" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" name="bottom_image_width" value="{{ $heroSection->bottom_image_width }}" placeholder="Width" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="file" name="bottom_image_file" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                        <input type="text" name="bottom_image_alt" value="{{ $heroSection->bottom_image_alt }}" placeholder="Alt Text" class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" />
                    </div>
                </div>

        </div>
    </div>

    </script>

<script type="module">
    app.component('v-hero-section', {
        template: '#v-hero-section-template',

        props: ['errors'],

        data() {
            return {
                isUpdating: false
            };
        },
    });
</script>
@endPushOnce