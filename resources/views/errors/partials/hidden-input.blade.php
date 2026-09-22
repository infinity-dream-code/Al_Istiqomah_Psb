@if (is_array($value))
    @foreach ($value as $childKey => $childValue)
        @include('errors.partials.hidden-input', [
            'name' => $name . '[' . $childKey . ']',
            'value' => $childValue,
        ])
    @endforeach
@elseif ($value !== null)
    <input type="hidden" name="{{ $name }}" value="{{ is_bool($value) ? ($value ? '1' : '0') : $value }}">
@endif
