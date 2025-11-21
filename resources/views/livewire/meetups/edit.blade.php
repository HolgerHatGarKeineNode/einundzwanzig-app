<?php

use Livewire\Volt\Component;
use App\Models\Meetup;
use App\Models\City;
use Illuminate\Validation\Rule;

new class extends Component {
    public Meetup $meetup;

    // Basic Information
    public string $name = '';
    public ?int $city_id = null;
    public string $slug = '';
    public ?string $intro = null;

    // Links and Social Media
    public ?string $telegram_link = null;
    public ?string $webpage = null;
    public ?string $twitter_username = null;
    public ?string $matrix_group = null;
    public ?string $nostr = null;
    public ?string $nostr_status = null;
    public ?string $simplex = null;
    public ?string $signal = null;

    // Additional Information
    public ?string $community = null;
    public ?string $github_data = null;
    public bool $visible_on_map = false;

    // System fields (read-only)
    public ?int $created_by = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function mount(Meetup $meetup): void
    {
        $this->meetup = $meetup;

        // Basic Information
        $this->name = $meetup->name ?? '';
        $this->city_id = $meetup->city_id;
        $this->slug = $meetup->slug ?? '';
        $this->intro = $meetup->intro;

        // Links and Social Media
        $this->telegram_link = $meetup->telegram_link;
        $this->webpage = $meetup->webpage;
        $this->twitter_username = $meetup->twitter_username;
        $this->matrix_group = $meetup->matrix_group;
        $this->nostr = $meetup->nostr;
        $this->nostr_status = $meetup->nostr_status;
        $this->simplex = $meetup->simplex;
        $this->signal = $meetup->signal;

        // Additional Information
        $this->community = $meetup->community;
        $this->github_data = $meetup->github_data ? json_encode($meetup->github_data, JSON_PRETTY_PRINT) : null;
        $this->visible_on_map = (bool) $meetup->visible_on_map;

        // System fields
        $this->created_by = $meetup->created_by;
        $this->created_at = $meetup->created_at?->format('Y-m-d H:i:s');
        $this->updated_at = $meetup->updated_at?->format('Y-m-d H:i:s');
    }

    public function updateMeetup(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('meetups')->ignore($this->meetup->id)],
            'city_id' => ['nullable', 'exists:cities,id'],
            'intro' => ['nullable', 'string'],
            'telegram_link' => ['nullable', 'url', 'max:255'],
            'webpage' => ['nullable', 'url', 'max:255'],
            'twitter_username' => ['nullable', 'string', 'max:255'],
            'matrix_group' => ['nullable', 'string', 'max:255'],
            'nostr' => ['nullable', 'string', 'max:255'],
            'simplex' => ['nullable', 'string', 'max:255'],
            'signal' => ['nullable', 'string', 'max:255'],
            'community' => ['nullable', 'string', 'max:255'],
        ]);

        // Convert github_data string back to array if provided
        if (!empty($validated['github_data'])) {
            $decoded = json_decode($validated['github_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['github_data'] = $decoded;
            } else {
                $validated['github_data'] = null;
            }
        } else {
            $validated['github_data'] = null;
        }

        $this->meetup->update($validated);

        $this->dispatch('meetup-updated', name: $this->meetup->name);

        session()->flash('status', __('Meetup erfolgreich aktualisiert!'));
    }

    public function with(): array
    {
        return [
            'cities' => City::orderBy('name')->get(),
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto p-6">
    <flux:heading size="xl" class="mb-8">{{ __('Meetup bearbeiten') }}: {{ $meetup->name }}</flux:heading>

    <form wire:submit="updateMeetup" class="space-y-10">

        <!-- Basic Information -->
        <flux:fieldset class="space-y-6">
            <flux:legend>{{ __('Grundlegende Informationen') }}</flux:legend>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('ID') }}</flux:label>
                    <flux:input value="{{ $meetup->id }}" disabled/>
                    <flux:description>{{ __('System-generierte ID (nur lesbar)') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Name') }} <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="name" required/>
                    <flux:description>{{ __('Der Anzeigename für dieses Meetup') }}</flux:description>
                    <flux:error name="name"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Stadt') }}</flux:label>
                    <flux:select variant="listbox" searchable wire:model="city_id" placeholder="{{ __('Stadt auswählen') }}">
                        <x-slot name="search">
                            <flux:select.search class="px-4" placeholder="{{ __('Suche passende Stadt...') }}"/>
                        </x-slot>
                        @foreach($cities as $city)
                            <flux:select.option value="{{ $city->id }}">{{ $city->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>{{ __('Die nächstgrößte Stadt oder Ort') }}</flux:description>
                    <flux:error name="city_id"/>
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Einführung') }}</flux:label>
                <flux:textarea wire:model="intro" rows="4"/>
                <flux:description>{{ __('Kurze Beschreibung des Meetups') }}</flux:description>
                <flux:error name="intro"/>
            </flux:field>
        </flux:fieldset>

        <!-- Links and Social Media -->
        <flux:fieldset class="space-y-6">
            <flux:legend>{{ __('Links & Soziale Medien') }}</flux:legend>

            <!-- Primary Links -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('Webseite') }}</flux:label>
                    <flux:input wire:model="webpage" type="url" placeholder="https://example.com"/>
                    <flux:description>{{ __('Offizielle Webseite oder Landingpage') }}</flux:description>
                    <flux:error name="webpage"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Telegram Link') }}</flux:label>
                    <flux:input wire:model="telegram_link" type="url" placeholder="https://t.me/gruppenname"/>
                    <flux:description>{{ __('Link zur Telegram-Gruppe oder zum Kanal') }}</flux:description>
                    <flux:error name="telegram_link"/>
                </flux:field>
            </div>

            <!-- Social Media -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('Twitter Benutzername') }}</flux:label>
                    <flux:input wire:model="twitter_username" placeholder="benutzername"/>
                    <flux:description>{{ __('Twitter-Handle ohne @ Symbol') }}</flux:description>
                    <flux:error name="twitter_username"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Matrix Gruppe') }}</flux:label>
                    <flux:input wire:model="matrix_group" placeholder="#gruppe:matrix.org"/>
                    <flux:description>{{ __('Matrix-Raum Bezeichner oder Link') }}</flux:description>
                    <flux:error name="matrix_group"/>
                </flux:field>
            </div>

            <!-- Decentralized Platforms -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('Nostr') }}</flux:label>
                    <flux:input wire:model="nostr" placeholder="npub..."/>
                    <flux:description>{{ __('Nostr öffentlicher Schlüssel oder Bezeichner') }}</flux:description>
                    <flux:error name="nostr"/>
                </flux:field>
            </div>

            <!-- Messaging Apps -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('SimpleX') }}</flux:label>
                    <flux:input wire:model="simplex"/>
                    <flux:description>{{ __('SimpleX Chat Kontaktinformationen') }}</flux:description>
                    <flux:error name="simplex"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Signal') }}</flux:label>
                    <flux:input wire:model="signal"/>
                    <flux:description>{{ __('Signal Kontakt- oder Gruppeninformationen') }}</flux:description>
                    <flux:error name="signal"/>
                </flux:field>
            </div>
        </flux:fieldset>

        <!-- Additional Information -->
        <flux:fieldset class="space-y-6">
            <flux:legend>{{ __('Zusätzliche Informationen') }}</flux:legend>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('Gemeinschaft') }}</flux:label>
                    <flux:select wire:model="community">
                        <flux:select.option value="einundzwanzig">einundzwanzig</flux:select.option>
                        <flux:select.option value="bitcoin">bitcoin</flux:select.option>
                    </flux:select>
                    <flux:description>{{ __('Gemeinschafts- oder Organisationsname') }}</flux:description>
                    <flux:error name="community"/>
                </flux:field>
            </div>
        </flux:fieldset>

        <!-- System Information -->
        <flux:fieldset class="space-y-6">
            <flux:legend>{{ __('Systeminformationen') }}</flux:legend>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <flux:field>
                    <flux:label>{{ __('Erstellt von') }}</flux:label>
                    <flux:input value="{{ $meetup->createdBy?->name ?? __('Unbekannt') }}" disabled/>
                    <flux:description>{{ __('Ersteller des Meetups') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Erstellt am') }}</flux:label>
                    <flux:input value="{{ $created_at }}" disabled/>
                    <flux:description>{{ __('Wann dieses Meetup erstellt wurde') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Aktualisiert am') }}</flux:label>
                    <flux:input value="{{ $updated_at }}" disabled/>
                    <flux:description>{{ __('Letzte Änderungszeit') }}</flux:description>
                </flux:field>
            </div>
        </flux:fieldset>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-8 border-t border-gray-200 dark:border-gray-700">
            <flux:button variant="ghost" type="button" onclick="history.back()">
                {{ __('Abbrechen') }}
            </flux:button>

            <div class="flex items-center gap-4">
                @if (session('status'))
                    <flux:text class="text-green-600 dark:text-green-400 font-medium">
                        {{ session('status') }}
                    </flux:text>
                @endif

                <flux:button variant="primary" type="submit">
                    {{ __('Meetup aktualisieren') }}
                </flux:button>
            </div>
        </div>
    </form>
</div>
