<div class="mt-8">
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading>
                <div class="flex items-center gap-2">
                    <flux:icon.language class="w-5 h-5" />
                    {{ __('Sprache wählen') }}
                </div>
            </flux:accordion.heading>

            <flux:accordion.content>
                @php
                    $languages = [
                        'de' => ['name' => 'Deutsch', 'countries' => ['de-DE', 'de-AT', 'de-CH']],
                        'en' => ['name' => 'English', 'countries' => ['en-GB', 'en-US', 'en-AU', 'en-CA']],
                        'es' => ['name' => 'Español', 'countries' => ['es-ES', 'es-CL', 'es-CO']],
                    ];
                    $currentLangCountry = session('lang_country', config('lang-country.fallback'));
                @endphp

                <div class="grid grid-cols-2 gap-3 mt-2">
                    @foreach($languages as $langCode => $langData)
                        @foreach($langData['countries'] as $langCountry)
                            @php
                                [$lang, $countryCode] = explode('-', $langCountry);
                                $isActive = $currentLangCountry === $langCountry;
                            @endphp
                            <a href="{{ route('lang_country.switch', ['lang_country' => $langCountry]) }}"
                               class="flex flex-col items-center justify-center p-3 border rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors {{ $isActive ? 'border-blue-500 bg-blue-50 dark:bg-blue-950' : 'border-zinc-200 dark:border-zinc-700' }}">
                                <img
                                    alt="{{ strtolower($countryCode) }}"
                                    src="{{ asset('vendor/blade-flags/country-'.strtolower($countryCode).'.svg') }}"
                                    class="w-10 h-7 mb-1 object-cover"
                                />
                                <span class="text-xs font-medium">{{ $langData['name'] }}</span>
                                <span class="text-[10px] text-zinc-500">{{ strtoupper($countryCode) }}</span>
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
