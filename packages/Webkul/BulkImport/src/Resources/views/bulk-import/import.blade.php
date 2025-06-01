<x-admin::layouts>
    <x-slot:title>
        @lang('bulk_import::app.bulk_import.title')
        </x-slot>

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap mb-4">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('bulk_import::app.bulk_import.title')
            </p>
        </div>

        <hr>

        <label class="mt-4 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">@lang('bulk_import::app.bulk_import.sample')</label>
        <a href="/sample.xlsx" class="primary-button mt-4" style="width: 15%;">@lang('bulk_import::app.bulk_import.sample')</a>
        
        <form action="{{ route('admin.settings.data_transfer.imports.custom.file') }}" enctype="multipart/form-data" method="POST" class="mt-8">
            @csrf
            <div class="mb-5">
                <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">@lang('bulk_import::app.bulk_import.locale')</label>
                <select
                    name="locale"
                    class="custom-select flex h-10 w-40 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                    required
                >
                    <option value="">@lang('bulk_import::app.bulk_import.locale')</option>
                    @foreach($locales as $locale)
                        <option value="{{ $locale->code }}">{{ $locale->name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">@lang('bulk_import::app.bulk_import.upload-file-products')</label>
            <input type="file" name="file" required />
            <button type="submit" class="primary-button mt-5">@lang('bulk_import::app.bulk_import.import')</button>
        </form>

        <form action="/admin/upload-images" method="POST" enctype="multipart/form-data" class="mt-5">
            @csrf

            <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">@lang('bulk_import::app.bulk_import.upload-file-images')</label>
            <input type="file" name="images[]" multiple required>
            <button type="submit" class="primary-button mt-5">@lang('bulk_import::app.bulk_import.upload')</button>
        </form>
</x-admin::layouts>