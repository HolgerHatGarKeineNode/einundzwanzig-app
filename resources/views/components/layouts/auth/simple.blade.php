<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
<flux:toast.group>
    <flux:toast/>
</flux:toast.group>
{{ $slot }}
@fluxScripts
<script>
    if (!localStorage.getItem('flux.appearance')) {
        localStorage.setItem('flux.appearance', 'dark');
    }
    document.addEventListener('alpine:init', () => {
        Alpine.directive('copy-to-clipboard', (el, {expression}, {evaluate}) => {
            el.addEventListener('click', () => {
                const text = evaluate(expression);
                console.log(text);

                navigator.clipboard.writeText(text).then(() => {
                    Flux.toast({
                        heading: '{{ __('Success!') }}',
                        text: '{{ __('Copied into clipboard') }}',
                        variant: 'success',
                        duration: 3000
                    });
                }).catch(err => console.error(err));
            });
        });
    });
</script>
</body>
</html>
