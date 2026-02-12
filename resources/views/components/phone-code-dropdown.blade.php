@props(['selected' => null, 'name' => 'phone_code', 'id' => 'phone-code-dropdown'])

@php
    $countriesPath = public_path('data/countries.json');
    $countries = json_decode(file_get_contents($countriesPath), true);
    $selectedCountry = null;
    
    if ($selected) {
        foreach ($countries as $country) {
            if ($country['iso2'] === strtoupper($selected)) {
                $selectedCountry = $country;
                break;
            }
        }
    }
@endphp

<div class="phone-code-dropdown-wrapper" x-data="phoneCodeDropdown(@js($countries), @js($selectedCountry))" @country-changed.window="updateFromCountry($event.detail)">
    <div class="dropdown-container">
        <button 
            type="button" 
            class="dropdown-trigger"
            :class="{ 'active': isOpen }"
            @click="toggleDropdown"
            @click.away="closeDropdown"
        >
            <span class="dropdown-value">
                <template x-if="selectedCountry">
                    <span>
                        <span class="flag-icon" x-text="getFlag(selectedCountry.iso2)"></span>
                        <span x-text="`${selectedCountry.name} ${selectedCountry.call_code}`"></span>
                    </span>
                </template>
                <template x-if="!selectedCountry">
                    <span class="placeholder">Select a phone code...</span>
                </template>
            </span>
            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
        </button>

        <div class="dropdown-menu" x-show="isOpen" x-transition @click.stop style="display: none;">
            <div class="search-container">
                <input 
                    type="text" 
                    class="search-input"
                    placeholder="Search countries..."
                    x-model="searchQuery"
                    @keydown.escape="closeDropdown"
                    @focus="isOpen = true"
                >
            </div>
            
            <div class="options-container">
                <template x-for="country in filteredCountries" :key="country.iso2 + country.call_code">
                    <button
                        type="button"
                        class="option-item"
                        :class="{ 'selected': selectedCountry?.iso2 === country.iso2 }"
                        @click="selectPhoneCode(country)"
                    >
                        <span class="flag-icon" x-text="getFlag(country.iso2)"></span>
                        <span x-text="`${country.name} ${country.call_code}`"></span>
                        <svg x-show="selectedCountry?.iso2 === country.iso2" class="check-icon" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M13 4L6 11L3 8" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <input type="hidden" name="{{ $name }}" :value="selectedCountry?.call_code || ''" id="{{ $id }}">
</div>

<style>
.phone-code-dropdown-wrapper {
    width: 100%;
}

.phone-code-dropdown-wrapper .dropdown-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.phone-code-dropdown-wrapper .dropdown-container {
    position: relative;
}

.phone-code-dropdown-wrapper .dropdown-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.phone-code-dropdown-wrapper .dropdown-trigger:hover {
    border-color: #9ca3af;
}

.phone-code-dropdown-wrapper .dropdown-trigger.active,
.phone-code-dropdown-wrapper .dropdown-trigger:focus {
    border-color: #ef4444;
    background-color: #fef2f2;
    outline: none;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.phone-code-dropdown-wrapper .dropdown-value {
    display: flex;
    align-items: center;
    gap: 8px;
}

.phone-code-dropdown-wrapper .placeholder {
    color: #9ca3af;
}

.phone-code-dropdown-wrapper .flag-icon {
    font-size: 18px;
}

.phone-code-dropdown-wrapper .dropdown-icon {
    color: #6b7280;
    transition: transform 0.2s;
}

.phone-code-dropdown-wrapper .dropdown-trigger.active .dropdown-icon {
    transform: rotate(180deg);
}

.phone-code-dropdown-wrapper .dropdown-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 50;
    max-height: 320px;
    display: flex;
    flex-direction: column;
}

.phone-code-dropdown-wrapper .search-container {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.phone-code-dropdown-wrapper .search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.phone-code-dropdown-wrapper .search-input:focus {
    outline: none;
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1);
}

.phone-code-dropdown-wrapper .options-container {
    overflow-y: auto;
    max-height: 260px;
}

.phone-code-dropdown-wrapper .option-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: white;
    border: none;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.15s;
    font-size: 14px;
    color: #374151;
}

.phone-code-dropdown-wrapper .option-item:hover {
    background-color: #f9fafb;
}

.phone-code-dropdown-wrapper .option-item.selected {
    background-color: #fef2f2;
    color: #dc2626;
}

.phone-code-dropdown-wrapper .check-icon {
    margin-left: auto;
    color: #dc2626;
}

.phone-code-dropdown-wrapper .options-container::-webkit-scrollbar {
    width: 8px;
}

.phone-code-dropdown-wrapper .options-container::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.phone-code-dropdown-wrapper .options-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.phone-code-dropdown-wrapper .options-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>

<script>
function phoneCodeDropdown(countries, selected) {
    return {
        countries: countries,
        selectedCountry: selected,
        isOpen: false,
        searchQuery: '',
        
        get filteredCountries() {
            if (!this.searchQuery) {
                return this.countries;
            }
            
            const query = this.searchQuery.toLowerCase();
            return this.countries.filter(country => 
                country.name.toLowerCase().includes(query) ||
                country.call_code.includes(query) ||
                country.iso2.toLowerCase().includes(query)
            );
        },
        
        getFlag(iso2) {
            const codePoints = iso2
                .toUpperCase()
                .split('')
                .map(char => 127397 + char.charCodeAt());
            return String.fromCodePoint(...codePoints);
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$el.querySelector('.search-input')?.focus();
                });
            }
        },
        
        closeDropdown() {
            this.isOpen = false;
            this.searchQuery = '';
        },
        
        selectPhoneCode(country) {
            this.selectedCountry = country;
            this.closeDropdown();
        },
        
        updateFromCountry(country) {
            if (country) {
                this.selectedCountry = country;
            }
        }
    }
}
</script>
