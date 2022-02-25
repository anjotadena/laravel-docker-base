<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    private Kernel $artisan;
    protected string $baseURL = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $this->artisan = $app->make(Kernel::class);
        
        $this->artisan->bootstrap();

        return $app;
    }

    private function prepareForTests(): void
    {
        // $this->artisan->call('migrate');

        // if (!User::count()) {
        //     $this->artisan->call('db:seed');
        // }
    }
}
