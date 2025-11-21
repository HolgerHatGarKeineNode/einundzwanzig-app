<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a href="{{ route_with_country('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
       wire:navigate>
        <x-app-logo/>
    </a>

    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('App')" class="grid">
            <flux:navlist.item icon="home" :href="route_with_country('dashboard')"
                               :current="request()->routeIs('dashboard')"
                               wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
        </flux:navlist.group>
        <flux:navlist.group :heading="__('Meetups')" class="grid">
            <flux:navlist.item icon="user-group" :href="route_with_country('meetups.index')"
                               :current="request()->routeIs('meetups.index')"
                               wire:navigate>{{ __('Meetups') }}</flux:navlist.item>
            <flux:navlist.item icon="map" :href="route_with_country('meetups.map')"
                               :current="request()->routeIs('meetups.map')"
                               wire:navigate>{{ __('Karte') }}</flux:navlist.item>
        </flux:navlist.group>
        {{--<flux:navlist.group :heading="__('Wallpaper')" class="grid">

        </flux:navlist.group>--}}
    </flux:navlist>

    <flux:spacer/>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="folder-git-2" href="https://git.affekt.de/einundzwanzig/einundzwanzig-app"
                           target="_blank">
            {{ __('Repository') }}
        </flux:navlist.item>
    </flux:navlist>

    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Land')" class="grid">
            <livewire:country.chooser/>
        </flux:navlist.group>
    </flux:navlist>

    <!-- Desktop User Menu -->
    @auth
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span
                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                            >
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">
                                        @if(strlen(auth()->user()->name) > 12)
                                        {{ Str::substr(auth()->user()->name, 0, 4) }}
                                        ...{{ Str::substr(auth()->user()->name, -4) }}
                                    @else
                                        {{ auth()->user()->name }}
                                    @endif
                                    </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog"
                                    wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    @endauth
</flux:sidebar>

<!-- Mobile User Menu -->
@auth
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

        <flux:spacer/>

        <flux:navlist variant="outline" class="mr-6">
            <flux:navlist.group class="grid">
                <livewire:country.chooser/>
            </flux:navlist.group>
        </flux:navlist>

        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">
                                        @if(strlen(auth()->user()->name) > 12)
                                        {{ Str::substr(auth()->user()->name, 0, 4) }}
                                        ...{{ Str::substr(auth()->user()->name, -4) }}
                                    @else
                                        {{ auth()->user()->name }}
                                    @endif
                                    </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog"
                                    wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>
@endauth

{{ $slot }}

@fluxScripts

<script>
    if (!localStorage.getItem('flux.appearance')) {
        localStorage.setItem('flux.appearance', 'dark');
    }
</script>

</body>
</html>
