<?php

use App\Models\LoginKey;
use App\Models\User;
use App\Notifications\ModelCreatedNotification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use eza\lnurl;

new #[Layout('components.layouts.auth')]
class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public ?string $k1 = null;
    public ?string $url = null;
    public ?string $lnurl = null;
    public ?string $qrCode = null;

    public function mount(): void
    {
        // Nur beim ersten Mount initialisieren
        if ($this->k1 === null) {
            $this->k1 = bin2hex(str()->random(32));
            if (app()->environment('local')) {
                $this->url = 'https://mmy4dp8eab.sharedwithexpose.com/api/lnurl-auth-callback?tag=login&k1='.$this->k1.'&action=login';
            } else {
                $this->url = url('/api/lnurl-auth-callback?tag=login&k1='.$this->k1.'&action=login');
            }
            $this->lnurl = lnurl\encodeUrl($this->url);
            $this->qrCode = base64_encode(QrCode::format('png')
                ->size(300)
                ->merge('/public/android-chrome-192x192.png', .3)
                ->errorCorrection('H')
                ->generate($this->lnurl));
        }
    }

    #[On('nostrLoggedIn')]
    public function loginListener($pubkey): void
    {
        $user = \App\Models\User::query()->where('nostr', $pubkey)->first();
        if ($user) {
            Auth::loginUsingId($user->id);
            Session::regenerate();
            $this->redirectIntended(default: route_with_country('dashboard', absolute: false), navigate: true);
            return;
        }
        return;

        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route_with_country('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function checkAuth()
    {
        $loginKey = LoginKey::query()
            ->where('k1', $this->k1)
            ->whereDate('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($loginKey) {
            $user = User::find($loginKey->user_id);

            \App\Models\User::find(1)
                ->notify(new ModelCreatedNotification($user, 'users'));
            auth()->login($user);

            return to_route('dashboard', ['country' => 'de']);
        }

        return true;
    }
}; ?>

<div class="flex min-h-screen" x-data="nostrLogin">
    <div class="flex-1 flex justify-center items-center">
        <div class="w-80 max-w-80 space-y-6">
            <!-- Logo -->
            <div class="flex justify-center">
                <a href="/" class="group flex items-center gap-3">
                    <div>
                        <flux:avatar class="[:where(&)]:size-32 [:where(&)]:text-base" size="xl"
                                     src="{{ asset('img/einundzwanzig-square.svg') }}"/>
                    </div>
                </a>
            </div>

            <!-- Welcome Heading -->
            <flux:heading class="text-center" size="xl">{{ __('Willkommen zurück') }}</flux:heading>

            <!-- OAuth Buttons -->
            {{--<div class="space-y-4">
                <flux:button class="w-full">
                    <x-slot name="icon">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.06 12.25C23.06 11.47 22.99 10.72 22.86 10H12.5V14.26H18.42C18.16 15.63 17.38 16.79 16.21 17.57V20.34H19.78C21.86 18.42 23.06 15.6 23.06 12.25Z" fill="#4285F4"/>
                            <path d="M12.4997 23C15.4697 23 17.9597 22.02 19.7797 20.34L16.2097 17.57C15.2297 18.23 13.9797 18.63 12.4997 18.63C9.63969 18.63 7.20969 16.7 6.33969 14.1H2.67969V16.94C4.48969 20.53 8.19969 23 12.4997 23Z" fill="#34A853"/>
                            <path d="M6.34 14.0899C6.12 13.4299 5.99 12.7299 5.99 11.9999C5.99 11.2699 6.12 10.5699 6.34 9.90995V7.06995H2.68C1.93 8.54995 1.5 10.2199 1.5 11.9999C1.5 13.7799 1.93 15.4499 2.68 16.9299L5.53 14.7099L6.34 14.0899Z" fill="#FBBC05"/>
                            <path d="M12.4997 5.38C14.1197 5.38 15.5597 5.94 16.7097 7.02L19.8597 3.87C17.9497 2.09 15.4697 1 12.4997 1C8.19969 1 4.48969 3.47 2.67969 7.07L6.33969 9.91C7.20969 7.31 9.63969 5.38 12.4997 5.38Z" fill="#EA4335"/>
                        </svg>
                    </x-slot>
                    Continue with Google
                </flux:button>

                <flux:button class="w-full">
                    <x-slot name="icon">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_614_12799)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4642 0C5.84833 0 0.5 5.5 0.5 12.3043C0.5 17.7433 3.92686 22.3473 8.68082 23.9768C9.27518 24.0993 9.4929 23.712 9.4929 23.3863C9.4929 23.101 9.47331 22.1233 9.47331 21.1045C6.14514 21.838 5.45208 19.6378 5.45208 19.6378C4.91723 18.2118 4.12474 17.8453 4.12474 17.8453C3.03543 17.0915 4.20408 17.0915 4.20408 17.0915C5.41241 17.173 6.04645 18.3545 6.04645 18.3545C7.11592 20.2285 8.83927 19.699 9.53257 19.373C9.63151 18.5785 9.94865 18.0285 10.2854 17.723C7.63094 17.4378 4.83812 16.3785 4.83812 11.6523C4.83812 10.3078 5.31323 9.20775 6.06604 8.35225C5.94727 8.04675 5.53118 6.7835 6.18506 5.09275C6.18506 5.09275 7.19527 4.76675 9.47306 6.35575C10.4483 6.08642 11.454 5.9494 12.4642 5.94825C13.4745 5.94825 14.5042 6.091 15.4552 6.35575C17.7332 4.76675 18.7434 5.09275 18.7434 5.09275C19.3973 6.7835 18.981 8.04675 18.8622 8.35225C19.6349 9.20775 20.0904 10.3078 20.0904 11.6523C20.0904 16.3785 17.2976 17.4173 14.6233 17.723C15.0592 18.11 15.4353 18.8433 15.4353 20.0045C15.4353 21.6545 15.4158 22.9788 15.4158 23.386C15.4158 23.712 15.6337 24.0993 16.2278 23.977C20.9818 22.347 24.4087 17.7433 24.4087 12.3043C24.4282 5.5 19.0603 0 12.4642 0Z" fill="currentColor"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_614_12799">
                                    <rect width="24" height="24" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </x-slot>
                    Continue with GitHub
                </flux:button>
            </div>--}}

            <!-- Separator -->
            {{--<flux:separator text="or" />--}}

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')"/>

            <!-- Login Form -->
            <div class="flex flex-col gap-6">
                <!-- Email Input -->
                {{--<flux:input
                    wire:model="email"
                    label="Email"
                    type="email"
                    placeholder="email@example.com"
                    required
                    autofocus
                    autocomplete="email"
                />--}}

                <!-- Password Input -->
                {{--<flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>
                        @if (Route::has('password.request'))
                            <flux:link href="{{ route('password.request') }}" variant="subtle" class="text-sm" wire:navigate>
                                Forgot password?
                            </flux:link>
                        @endif
                    </div>
                    <flux:input
                        wire:model="password"
                        type="password"
                        placeholder="Your password"
                        required
                        autocomplete="current-password"
                    />
                </flux:field>--}}

                <!-- Remember Me -->
                {{--<flux:checkbox wire:model="remember" label="Remember me for 30 days" />--}}

                <!-- Submit Button -->
                <flux:button variant="primary" @click="openNostrLogin"
                             class="w-full cursor-pointer">{{ __('Log in mit Nostr') }}</flux:button>

                <div class="text-center text-2xl text-gray-80 dark:text-gray-2000 mt-6">
                    Login with lightning ⚡
                </div>

                <div class="flex justify-center" wire:key="qrcode">
                    <a href="lightning:{{ $this->lnurl }}">
                        <div class="bg-white p-4">
                            <img src="{{ 'data:image/png;base64, '. $this->qrCode }}" alt="qrcode">
                        </div>
                    </a>
                </div>

                <div class="flex justify-between w-full">
                    <div x-copy-to-clipboard="'{{ $this->lnurl }}'">
                        <flux:button icon="clipboard" class="cursor-pointer">
                            {{ __('Copy') }}
                        </flux:button>
                    </div>
                    <div>
                        <flux:button
                            primary
                            :href="'lightning:'.$this->lnurl"
                        >
                            {{ __('Click to connect') }}
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                 height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                                      d="M461.81 53.81a4.4 4.4 0 00-3.3-3.39c-54.38-13.3-180 34.09-248.13 102.17a294.9 294.9 0 00-33.09 39.08c-21-1.9-42-.3-59.88 7.5-50.49 22.2-65.18 80.18-69.28 105.07a9 9 0 009.8 10.4l81.07-8.9a180.29 180.29 0 001.1 18.3 18.15 18.15 0 005.3 11.09l31.39 31.39a18.15 18.15 0 0011.1 5.3 179.91 179.91 0 0018.19 1.1l-8.89 81a9 9 0 0010.39 9.79c24.9-4 83-18.69 105.07-69.17 7.8-17.9 9.4-38.79 7.6-59.69a293.91 293.91 0 0039.19-33.09c68.38-68 115.47-190.86 102.37-247.95zM298.66 213.67a42.7 42.7 0 1160.38 0 42.65 42.65 0 01-60.38 0z"></path>
                                <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                                      d="M109.64 352a45.06 45.06 0 00-26.35 12.84C65.67 382.52 64 448 64 448s65.52-1.67 83.15-19.31A44.73 44.73 0 00160 402.32"></path>
                            </svg>
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Sign up Link -->
            {{--@if (Route::has('register'))
                <flux:subheading class="text-center">
                    First time around here? <flux:link href="{{ route('register') }}" wire:navigate>Sign up for free</flux:link>
                </flux:subheading>
            @endif--}}
        </div>
    </div>

    <!-- Right Side Panel -->
    <div class="flex-1 p-4 max-lg:hidden">
        <div class="text-white relative rounded-lg h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16"
             style="background-image: url('https://dergigi.com/assets/images/bitcoin-is-time.jpg'); background-size: cover">

            <!-- Testimonial -->
            <div class="mb-6 italic font-base text-3xl xl:text-4xl">
                Bitcoin, not blockchain. Bitcoin, not crypto.
            </div>

            <!-- Author Info -->
            <div class="flex gap-4">
                <flux:avatar src="https://dergigi.com/assets/images/avatar.jpg" size="xl"/>
                <div class="flex flex-col justify-center font-medium">
                    <div class="text-lg">Gigi</div>
                    <div class="text-zinc-300">bitcoiner and software engineer</div>
                </div>
            </div>
        </div>
    </div>

    <div wire:poll.4s="checkAuth" wire:key="checkAuth"></div>
</div>
