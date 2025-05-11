<x-admin::layouts>
    <x-slot:title>
        Bulk Import
        </x-slot>

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap mb-4">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                Bulk Import
            </p>
        </div>

        <hr>

        <a href="/sample.xlsx" class="primary-button mt-5" style="width: 15%;">Download Sample</a>

        <form action="{{ route('admin.settings.data_transfer.imports.custom.file') }}" enctype="multipart/form-data" method="POST" class="mt-8">
            @csrf
            <input type="file" name="file" />
            <button type="submit" class="primary-button mt-5">Import</button>
        </form>

</x-admin::layouts>