<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'meeting_room' => MeetingRoom::class,
            'vehicle' => Vehicle::class,
            'it_asset' => ItAsset::class,
        ]);

    }
}
