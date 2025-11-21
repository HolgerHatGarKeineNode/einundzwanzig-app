<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('country.chooser');

    $component->assertSee('');
});
