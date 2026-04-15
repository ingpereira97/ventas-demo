<!-- resources/views/components/input.blade.php -->
<input type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ $value ?? old($name) }}" class="form-input rounded-md shadow-sm mt-1 block w-full @error($name) border-red-500 @enderror" />
