<?php

use App\Models\MeetupEvent;
use Livewire\Volt\Component;

new class extends Component {
    public MeetupEvent $event;
    public $country = 'de';

    public function mount(): void
    {
        $this->country = request()->route('country');
    }

    public function with(): array
    {
        return [
            'event' => $this->event->load('meetup'),
        ];
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
            <a href="{{ route('meetups.landingpage', ['meetup' => $event->meetup->slug, 'country' => $country]) }}" class="hover:underline">
                {{ $event->meetup->name }}
            </a>
            <span class="mx-2">/</span>
            <span>{{ $event->start->format('d.m.Y') }}</span>
        </flux:text>
    </div>

    <!-- Event Details -->
    <flux:card class="max-w-3xl">
        <flux:heading size="xl" class="mb-4">
            <flux:icon.calendar class="inline w-6 h-6 mr-2" />
            {{ $event->start->format('d.m.Y') }}
        </flux:heading>

        <div class="space-y-4">
            <!-- Date and Time -->
            <div class="flex items-center text-zinc-700 dark:text-zinc-300">
                <flux:icon.clock class="w-5 h-5 mr-3" />
                <div>
                    <div class="font-semibold">{{ $event->start->format('H:i') }} Uhr</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $event->start->isoFormat('dddd, D. MMMM YYYY') }}</div>
                </div>
            </div>

            <!-- Location -->
            @if($event->location)
                <div class="flex items-center text-zinc-700 dark:text-zinc-300">
                    <flux:icon.map-pin class="w-5 h-5 mr-3" />
                    <div>
                        <div class="font-semibold">{{ __('Ort') }}</div>
                        <div class="text-sm">{{ $event->location }}</div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            @if($event->description)
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-2">{{ __('Beschreibung') }}</flux:heading>
                    <flux:text class="whitespace-pre-wrap">{{ $event->description }}</flux:text>
                </div>
            @endif

            <!-- Link -->
            @if($event->link)
                <div class="pt-4">
                    <flux:button href="{{ $event->link }}" target="_blank" variant="primary">
                        <flux:icon.arrow-top-right-on-square class="w-5 h-5 mr-2" />
                        {{ __('Mehr Informationen') }}
                    </flux:button>
                </div>
            @endif

            <!-- Attendees -->
            @if($event->attendees && count($event->attendees) > 0)
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-2">
                        {{ __('Zusagen') }} ({{ count($event->attendees) }})
                    </flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @foreach($event->attendees as $attendee)
                            <flux:badge>{{ $attendee }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Might Attend -->
            @if($event->might_attendees && count($event->might_attendees) > 0)
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-2">
                        {{ __('Vielleicht') }} ({{ count($event->might_attendees) }})
                    </flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @foreach($event->might_attendees as $attendee)
                            <flux:badge variant="outline">{{ $attendee }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Back Button -->
    <div class="mt-6">
        <flux:button href="{{ route('meetups.landingpage', ['meetup' => $event->meetup->slug, 'country' => $country]) }}" variant="ghost">
            <flux:icon.arrow-left class="w-5 h-5 mr-2" />
            {{ __('Zurück zum Meetup') }}
        </flux:button>
    </div>
</div>
