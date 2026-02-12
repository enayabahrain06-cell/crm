@props([
    'name' => 'country_code',
    'id' => 'country_code',
    'value' => 'BH',
    'required' => false,
    'error' => null,
    'label' => 'Country Code',
    'placeholder' => 'Select Country Code',
    // Phone input props
    'phone_name' => 'phone',
    'phone_id' => 'phone',
    'phone_value' => '',
    'phone_placeholder' => 'Enter phone number',
    'phone_required' => false
])

@php
    $countriesPath = public_path('data/countries.json');
    $countriesJson = file_get_contents($countriesPath);
    $countries = json_decode($countriesJson, true);
    usort($countries, function($a, $b) {
        return $a['name'] <=> $b['name'];
    });
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $phone_id }}" class="form-label">{{ $label }} @if($required || $phone_required)<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="input-group">
        <select id="{{ $id }}"
                class="form-select @error($name) is-invalid @enderror"
                name="{{ $name }}"
                {{ $required ? 'required' : '' }}
                style="width: auto; min-width: 120px; flex: 0 0 auto;">
            @foreach($countries as $country)
                <option value="{{ $country['iso2'] }}" 
                        {{ $value === $country['iso2'] ? 'selected' : '' }}
                        data-flag="{{ $country['flag'] }}"
                        data-call-code="{{ $country['call_code'] }}">
                    {{ $country['flag'] }} {{ $country['call_code'] }}
                </option>
            @endforeach
        </select>
        <input type="text"
               id="{{ $phone_id }}"
               class="form-control @error($phone_name) is-invalid @enderror"
               name="{{ $phone_name }}"
               value="{{ $phone_value }}"
               placeholder="{{ $phone_placeholder }}"
               {{ $phone_required ? 'required' : '' }}>
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @elseif($error)
        <div class="invalid-feedback d-block">{{ $error }}</div>
    @endif
    @error($phone_name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

