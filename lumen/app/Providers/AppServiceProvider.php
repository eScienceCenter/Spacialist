<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot() {
        // Validator::extend('', function ($attribute, $value, $parameters, $validator) {
        //     return $value == '';
        // });
        Validator::extend('geom_type', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, \App\Geodata::availableGeometryTypes);
        });

        Validator::extend('color', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^#[a-fA-F0-9]{6}$/', $value, $matches) === 1;
        });

        Validator::extend('boolean_string', function ($attribute, $value, $parameters, $validator) {
            $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false', 'TRUE', 'FALSE'];
            return in_array($value, $acceptable, true);
        });

        Validator::extend('between_float', function ($attribute, $value, $parameters, $validator) {
            return $value >= $parameters[0] && $value <= $parameters[1];
        });
    }
}
