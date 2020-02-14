<?php

namespace App\Providers;

use App\Models\ProjectInput;
use App\Observers\ProjectInputObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Validator;
use App\Helpers\AuthHelper;
use View;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param Dispatcher $events
     */
    public function boot(Dispatcher $events)
    {
        Relation::morphMap([
            \App\Models\Post::MORPH_TYPE    => \App\Models\Post::class,
            \App\Models\Video::MORPH_TYPE   => \App\Models\Video::class,
            \App\Models\Comment::MORPH_TYPE => \App\Models\Comment::class,
            \App\Models\User::MORPH_TYPE    => \App\Models\User::class,
            \App\Models\Star::MORPH_TYPE    => \App\Models\Star::class,
            \App\Models\Circle::MORPH_TYPE  => \App\Models\Circle::class,
            \App\Models\Image::MORPH_TYPE   => \App\Models\Image::class,
            \App\Models\Project::MORPH_TYPE => \App\Models\Project::class,
            \App\Models\Audio::MORPH_TYPE   => \App\Models\Audio::class,
            \App\Models\Text::MORPH_TYPE    => \App\Models\Text::class,
            \App\Models\Asset::MORPH_TYPE   => \App\Models\Asset::class,
            \App\Models\Slide::MORPH_TYPE   => \App\Models\Slide::class,
        ]);

        // Models observers
        ProjectInput::observe(ProjectInputObserver::class);

        Validator::extend('has_video', function ($attribute, $value) {
            if ($value) {
                return AuthHelper::me()->videos->contains($value);
            }
            return true;
        });

        Collection::macro('without', function ($keys) {
            return $this->map(function ($item) use ($keys) {
                if (is_array($keys)) {
                    foreach ($keys as $key) {
                        if (is_array($item) && array_key_exists($key, $item)) {
                            unset($item[$key]);
                        }
                    }
                } elseif (is_string($keys)) {
                    if (is_array($item) && array_key_exists($keys, $item)) {
                        unset($item[$keys]);
                    }
                }

                return $item;
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(DuskServiceProvider::class);
        }
        View::share('env', $this->app->environment());
    }
}
