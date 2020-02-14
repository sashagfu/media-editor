<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        Broadcast::channel(
            'App.Models.User.{userID}',
            function ($user, $userId) {
                return (int) $user->id === (int) $userId;
            }
        );

        Broadcast::channel(
            'ChatBroadcaster.{userId}',
            function ($user, $userId) {
                return (int) $user->id === (int) $userId;
            }
        );
    }
}
