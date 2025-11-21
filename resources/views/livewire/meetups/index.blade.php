<?php

use App\Models\Meetup;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $country = 'de';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $search = '';

    public function mount(): void
    {
        $this->country = request()->route('country');
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function with(): array
    {
        return [
            'meetups' => Meetup::with(['city.country', 'createdBy'])
                ->whereHas('city.country', fn($query) => $query->where('countries.code', $this->country))
                ->when($this->search, fn($query)
                    => $query->where('name', 'ilike', '%'.$this->search.'%'),
                )
                ->when($this->sortBy === 'city',
                    fn($query)
                        => $query
                        ->orderBy('cities.name', $this->sortDirection)
                        ->join('cities', 'meetups.city_id', '=', 'cities.id'),
                    fn($query) => $query->orderBy($this->sortBy, $this->sortDirection),
                )
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <flux:heading size="xl">{{ __('Meetups') }}</flux:heading>
    <div class="mt-4">
        <flux:input
            wire:model.live="search"
            :placeholder="__('Suche nach Meetups...')"
            clearable
        />
    </div>

    <flux:table :paginate="$meetups" class="mt-6">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                               wire:click="sort('name')">{{ __('Name') }}
            </flux:table.column>
            <flux:table.column>{{ __('Nächster Termin') }}</flux:table.column>
            <flux:table.column>{{ __('Links') }}</flux:table.column>
            <flux:table.column>{{ __('Aktionen') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($meetups as $meetup)
                <flux:table.row :key="$meetup->id">
                    <flux:table.cell variant="strong" class="flex items-center gap-3">
                            <flux:avatar :href="route('meetups.landingpage', ['meetup' => $meetup, 'country' => $country])" src="{{ $meetup->getFirstMedia('logo') ? $meetup->getFirstMediaUrl('logo', 'thumb') : asset('android-chrome-512x512.png') }}"/>
                        <div>
                            @if($meetup->city)
                                <a href="{{ route('meetups.landingpage', ['meetup' => $meetup, 'country' => $country]) }}">
                                    <span>{{ $meetup->name }}</span>
                                    <div class="text-xs text-zinc-500 flex items-center space-x-2">
                                        <div>{{ $meetup->city->name }}</div>
                                        @if($meetup->city->country)
                                            <flux:separator vertical/>
                                            <div>{{ $meetup->city->country->name }}</div>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($meetup->nextEvent && $meetup->nextEvent['start']->isFuture())
                            <div class="flex flex-col gap-1">
                                <flux:badge color="green" size="sm">
                                    {{ $meetup->nextEvent['start']->format('d.m.Y H:i') }}
                                </flux:badge>
                                <div class="text-xs text-zinc-500 flex items-center gap-2">
                                    <span>{{ $meetup->nextEvent['attendees'] }} Zusagen</span>
                                    <flux:separator vertical/>
                                    <span>{{ $meetup->nextEvent['might_attendees'] }} Vielleicht</span>
                                </div>
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex gap-2">
                            @if($meetup->telegram_link)
                                <flux:link :href="$meetup->telegram_link" external variant="subtle" title="Telegram">
                                    <flux:icon.paper-airplane variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->webpage)
                                <flux:link :href="$meetup->webpage" external variant="subtle" title="Website">
                                    <flux:icon.globe-alt variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->twitter_username)
                                <flux:link :href="'https://twitter.com/' . $meetup->twitter_username" external
                                           variant="subtle" title="Twitter">
                                    <flux:icon.x-mark variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->matrix_group)
                                <flux:link :href="$meetup->matrix_group" external variant="subtle" title="Matrix">
                                    <flux:icon.chat-bubble-left variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->nostr)
                                <flux:link :href="'https://njump.me/'.$meetup->nostr" external variant="subtle"
                                           title="Nostr">
                                    <flux:icon.bolt variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->simplex)
                                <flux:link :href="$meetup->simplex" external variant="subtle" title="Simplex">
                                    <flux:icon.chat-bubble-bottom-center-text variant="mini"/>
                                </flux:link>
                            @endif
                            @if($meetup->signal)
                                <flux:link :href="$meetup->signal" external variant="subtle" title="Signal">
                                    <flux:icon.shield-check variant="mini"/>
                                </flux:link>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:button
                            :disabled="!$meetup->belongsToMe"
                            :href="$meetup->belongsToMe ? route_with_country('meetups.edit', ['meetup' => $meetup]) : null" size="xs"
                                     variant="filled" icon="pencil">
                            {{ __('Bearbeiten') }}
                        </flux:button>
                        <flux:button :href="route_with_country('meetups.events.create', ['meetup' => $meetup])" size="xs" variant="filled" icon="calendar">
                            {{ __('Neues Event erstellen') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
