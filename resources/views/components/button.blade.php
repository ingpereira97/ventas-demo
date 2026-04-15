<!-- resources/views/components/button.blade.php -->
<button type="{{ $type ?? 'submit' }}" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-700">
    {{ $slot }}
</button>
