<?php

namespace App\Providers;

use App\Repositories\FinnoTech\FinnoTechTransferRepository;
use App\Repositories\Interfaces\FinnoTechTransferInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FinnoTechTransferInterface::class, FinnoTechTransferRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
