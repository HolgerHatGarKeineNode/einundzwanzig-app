<?php

use App\Models\Lecturer;
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
            'lecturers' => Lecturer::with(['createdBy'])
                ->when($this->search, fn($query)
                    => $query->where('name', 'ilike', '%'.$this->search.'%')
                        ->orWhere('description', 'ilike', '%'.$this->search.'%')
                        ->orWhere('subtitle', 'ilike', '%'.$this->search.'%'),
                )
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Dozenten') }}</flux:heading>
        <div class="flex items-center gap-4">
            <flux:input
                wire:model.live="search"
                :placeholder="__('Suche nach Dozenten...')"
                clearable
            />
            @auth
                <flux:button class="cursor-pointer" :href="route_with_country('lecturers.create')" icon="plus" variant="primary">
                    {{ __('Dozenten anlegen') }}
                </flux:button>
            @endauth
        </div>
    </div>

    <flux:table :paginate="$lecturers" class="mt-6">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                               wire:click="sort('name')">{{ __('Name') }}
            </flux:table.column>
            <flux:table.column>{{ __('Untertitel') }}</flux:table.column>
            <flux:table.column>{{ __('Kurse') }}</flux:table.column>
            <flux:table.column>{{ __('Links') }}</flux:table.column>
            <flux:table.column>{{ __('Aktionen') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($lecturers as $lecturer)
                <flux:table.row :key="$lecturer->id">
                    <flux:table.cell variant="strong" class="flex items-center gap-3">
                        <flux:avatar size="lg" src="{{ $lecturer->getFirstMedia('avatar') ? $lecturer->getFirstMediaUrl('avatar', 'thumb') : asset('img/einundzwanzig.png') }}"/>
                        <div>
                            <div class="font-semibold">{{ $lecturer->name }}</div>
                            @if($lecturer->active)
                                <flux:badge size="sm" color="green">{{ __('Aktiv') }}</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc">{{ __('Inaktiv') }}</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($lecturer->subtitle)
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ Str::limit($lecturer->subtitle, 50) }}
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm">{{ $lecturer->courses()->count() }} {{ __('Kurse') }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex gap-2">
                            @if($lecturer->website)
                                <flux:link :href="$lecturer->website" external variant="subtle" title="{{ __('Website') }}">
                                    <flux:icon.globe-alt variant="mini"/>
                                </flux:link>
                            @endif
                            @if($lecturer->twitter_username)
                                <flux:link :href="'https://twitter.com/' . $lecturer->twitter_username" external
                                           variant="subtle" title="{{ __('Twitter') }}">
                                    <flux:icon.x-mark variant="mini"/>
                                </flux:link>
                            @endif
                            @if($lecturer->nostr)
                                <flux:link :href="'https://njump.me/'.$lecturer->nostr" external variant="subtle"
                                           title="{{ __('Nostr') }}">
                                    <flux:icon.bolt variant="mini"/>
                                </flux:link>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:button
                            :disabled="$lecturer->created_by !== auth()->id()"
                            :href="$lecturer->created_by === auth()->id() ? route_with_country('lecturers.edit', ['lecturer' => $lecturer]) : null"
                            size="xs"
                            variant="filled"
                            icon="pencil">
                            {{ __('Bearbeiten') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
