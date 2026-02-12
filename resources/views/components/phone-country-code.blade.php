@props(['name' => 'country_code', 'id' => 'country_code', 'value' => 'BH', 'required' => false, 'error' => null, 'label' => 'Country Code', 'placeholder' => 'Select Country Code'])

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <select id="{{ $id }}"
            class="form-select phone-country-select @error($name) is-invalid @enderror"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            style="width: 100%;">
        <option value="">{{ $placeholder }}</option>
    </select>
    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @elseif($error)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $error }}</strong>
        </span>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('{{ $id }}');
    if (!selectElement) return;

    // Check if Select2 is already initialized
    if (typeof $ !== 'undefined' && $(selectElement).hasClass('select2-hidden-accessible')) {
        return;
    }

    // Load countries from JSON file
    fetch('/data/countries.json')
        .then(response => response.json())
        .then(countries => {
            // Clear existing options except the first one
            while (selectElement.options.length > 1) {
                selectElement.remove(1);
            }

            // Populate dropdown with call codes (sorted alphabetically)
            const sortedCountries = countries.sort((a, b) => a.name.localeCompare(b.name));
            
            sortedCountries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.iso2;
                option.textContent = country.flag + ' ' + country.name + ' (' + country.call_code + ')';
                option.setAttribute('data-flag', country.flag);
                option.setAttribute('data-name', country.name);
                option.setAttribute('data-call-code', country.call_code);
                selectElement.appendChild(option);
            });

            // Set initial value if provided
            const initialValue = '{{ $value }}';
            if (initialValue) {
                selectElement.value = initialValue;
            }

            // Initialize Select2 for searchable dropdown
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(selectElement).select2({
                    templateResult: function(state) {
                        if (!state.id) {
                            return state.text;
                        }
                        const option = $(state.element);
                        const flagCode = option.data('flag');
                        const callCode = option.data('call-code');
                        const countryName = option.data('name');
                        return $(`<span>${flagCode} ${countryName} (${callCode})</span>`);
                    },
                    templateSelection: function(state) {
                        if (!state.id) {
                            return state.text;
                        }
                        const option = $(state.element);
                        const flagCode = option.data('flag');
                        const callCode = option.data('call-code');
                        return $(`<span>${flagCode} ${callCode}</span>`);
                    },
                    width: '100%',
                    placeholder: '{{ $placeholder }}',
                    allowClear: !{{ $required ? 'true' : 'false' }}
                });
            }
        })
        .catch(error => console.error('Error loading countries:', error));
});
</script>
@endpush

@push('styles')
<style>
    .phone-country-select {
        background-size: 20px 15px;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 4px 10px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }
</style>
@endpush

