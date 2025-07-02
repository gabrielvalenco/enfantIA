<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\SocialMediaHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register blade helper functions
        \Blade::directive('getSocialIcon', function ($expression) {
            return "<?php echo \App\Helpers\SocialMediaHelper::getSocialIcon($expression); ?>";
        });

        \Blade::directive('getSocialName', function ($expression) {
            return "<?php echo \App\Helpers\SocialMediaHelper::getSocialName($expression); ?>";
        });
    }
}
