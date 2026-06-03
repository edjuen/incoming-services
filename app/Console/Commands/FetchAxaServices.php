<?php

namespace App\Console\Commands;

use App\Jobs\FetchAxaServicesJob;
use Illuminate\Console\Command;

class FetchAxaServices extends Command
{
    protected $signature = 'axa:fetch-services';

    protected $description = 'Consulta servicios nuevos desde AXA';

    public function handle(): int
    {
        FetchAxaServicesJob::dispatchSync();

        $this->info('Consulta AXA ejecutada correctamente.');

        return self::SUCCESS;
    }
}




/*<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:fetch-axa-services')]
#[Description('Command description')]
class FetchAxaServices extends Command
{
    protected $signature = 'app:fetch-axa-services';

    protected $description = 'Consulta servicios nuevos desde AXA';

    public function handle(): int
    {
        FetchAxaServicesJob::dispatchSync();

        $this->info('Consulta AXA ejecutada correctamente.');

        return self::SUCCESS;
    }
}
*/