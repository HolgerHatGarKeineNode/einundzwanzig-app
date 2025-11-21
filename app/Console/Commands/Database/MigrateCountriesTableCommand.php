<?php

namespace App\Console\Commands\Database;

use App\Models\Country;
use Illuminate\Console\Command;

class MigrateCountriesTableCommand extends Command
{
    protected $signature = 'database:migrate-countries-table';

    protected $description = 'Migrate countries table by creating new entries from iso codes';

    public function handle(): void
    {
        $this->output->progressStart(\WW\Countries\Models\Country::count());

        foreach (\WW\Countries\Models\Country::all() as $country) {
            Country::query()
                ->updateOrCreate(
                    ['code' => str($country->iso_code)->lower()],
                    [
                        'name' => $country->name,
                        'english_name' => $country->name,
                    ],
                );
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('🌍 Countries migrated successfully! 🎉');
    }
}
