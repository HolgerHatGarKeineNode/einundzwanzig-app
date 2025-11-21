<?php

use Livewire\Volt\Component;

new class extends Component {
    public $currentRoute = '';
    public $country = 'de';

    public function mount(): void
    {
        $this->currentRoute = request()->route()->getName();
        $this->country = request()->route('country');
    }

    public function updatedCountry()
    {
        $this->redirectRoute($this->currentRoute, ['country' => $this->country]);
    }
}; ?>

<div>
    <flux:select variant="listbox" searchable placeholder="{{ __('Wähle dein Land...') }}" wire:model.live.debounce="country">
        <x-slot name="search">
            <flux:select.search class="px-4" placeholder="{{ __('Suche dein Land...') }}"/>
        </x-slot>
        @foreach(\WW\Countries\Models\Country::all() as $country)
            <flux:select.option value="{{ str($country->iso_code)->lower() }}">
                <div class="flex items-center space-x-2">
                    <img alt="{{ str($country->iso_code)->lower() }}" src="{{ asset('vendor/blade-flags/country-'.str($country->iso_code)->lower().'.svg') }}" width="24" height="12"/>
                    <span>{{ $country->name }}</span>
                </div>
            </flux:select.option>
        @endforeach
    </flux:select>
</div>
