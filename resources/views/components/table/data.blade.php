@props([
    'headers' => [],
    'rows' => [],
    'emptyMessage' => 'Tidak ada data.',
])

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($rows as $row)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    {{ $row }}
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}"
                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
