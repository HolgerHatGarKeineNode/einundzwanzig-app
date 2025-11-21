<?php

use App\Models\Course;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $country = 'de';
    public $search = '';

    public function mount(): void
    {
        $this->country = request()->route('country');
    }

    public function with(): array
    {
        return [
            'courses' => Course::with(['lecturer', 'createdBy'])
                ->withExists([
                    'courseEvents as has_future_events' => fn($query) => $query->where('from', '>=', now())
                ])
                ->when($this->search, fn($query)
                    => $query
                    ->where('name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('description', 'ilike', '%'.$this->search.'%'),
                )
                ->orderByDesc('has_future_events')
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Kurse') }}</flux:heading>
        <div class="flex items-center gap-4">
            <div>
                <flux:input
                    wire:model.live="search"
                    :placeholder="__('Suche nach Kursen...')"
                    clearable
                />
            </div>
            <flux:button variant="primary" icon="plus-circle" :href="route_with_country('courses.create')"
                         wire:navigate>{{ __('Neuer Kurs') }}</flux:button>
        </div>
    </div>

    <flux:table :paginate="$courses" class="mt-6">
        <flux:table.columns>
            <flux:table.column>
                {{ __('Name') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Dozent') }}
            </flux:table.column>
            <flux:table.column>{{ __('Nächster Termin') }}</flux:table.column>
            <flux:table.column>{{ __('Aktionen') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($courses as $course)
                <flux:table.row :key="$course->id">
                    <flux:table.cell variant="strong" class="flex items-center gap-3">
                        <flux:avatar :href="route('courses.landingpage', ['course' => $course, 'country' => $country])"
                                     src="{{ $course->getFirstMedia('logo') ? $course->getFirstMediaUrl('logo', 'thumb') : asset('android-chrome-512x512.png') }}"/>
                        <div>
                            <a href="{{ route('courses.landingpage', ['course' => $course, 'country' => $country]) }}">
                                <span>{{ $course->name }}</span>
                                @if($course->description)
                                    <div class="text-xs text-zinc-500">
                                        {{ Str::limit($course->description, 60) }}
                                    </div>
                                @endif
                            </a>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($course->lecturer)
                            <div class="flex items-center gap-2">
                                <flux:avatar size="xs"
                                             src="{{ $course->lecturer->getFirstMedia('avatar') ? $course->lecturer->getFirstMediaUrl('avatar', 'thumb') : asset('img/einundzwanzig.png') }}"/>
                                <span>{{ $course->lecturer->name }}</span>
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @php
                            $nextEvent = $course->courseEvents()
                                ->where('from', '>=', now())
                                ->orderBy('from', 'asc')
                                ->first();
                        @endphp
                        @if($nextEvent)
                            <flux:badge color="green" size="sm">
                                {{ $nextEvent->from->format('d.m.Y H:i') }}
                            </flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:button
                            :disabled="$course->created_by !== auth()->id()"
                            :href="$course->created_by === auth()->id() ? route_with_country('courses.edit', ['course' => $course]) : null"
                            size="xs"
                            variant="filled"
                            icon="pencil">
                            {{ __('Bearbeiten') }}
                        </flux:button>
                        <flux:button
                            :disabled="$course->created_by !== auth()->id()"
                            :href="$course->created_by === auth()->id() ? route_with_country('courses.events.create', ['course' => $course]) : null"
                            size="xs"
                            variant="filled"
                            icon="calendar">
                            {{ __('Neues Event erstellen') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
