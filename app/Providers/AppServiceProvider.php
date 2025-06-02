<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Validator;

require_once( 'BrowserDetection.php' );

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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $browser = new \Wolfcast\BrowserDetection();

        Activity::saving( function( Activity $activity ) use ( $browser ) {

            $activity->properties = $activity->properties->put( 'agent', [
                'ip' => request()->ip(),
                'user_agent' => $browser->getUserAgent(),
                'browserName' => $browser->getName() . ' ' . $browser->getVersion(),
                'os' => $browser->getPlatformVersion() . ' ' . $browser->getPlatformVersion( true ),
            ] );
        } );
        
        // These rules have subkeys (numeric, string, etc.)
        $typedRules = ['min', 'max', 'between', 'gt', 'gte', 'lt', 'lte', 'size'];

        foreach ($typedRules as $rule) {
            Validator::replacer($rule, function ($message, $attribute, $ruleName, $parameters, $validator) use ($rule) {
                $data = $validator->getData();
                $value = $data[$attribute] ?? null;

                // Determine type suffix
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $type = 'file';
                } elseif (is_array($value)) {
                    $type = 'array';
                } elseif (is_numeric($value)) {
                    $type = 'numeric';
                } else {
                    $type = 'string';
                }

                // Build translation key like: validation.custom.gt.numeric
                $key = "validation.custom.validation.{$rule}.{$type}";

                return trans($key, array_merge([
                    'attribute' => $attribute
                ], $this->formatParameterMap($parameters, $rule)));
            });
        }
    }
    
    protected function formatParameterMap( array $parameters, string $rule ) {
        switch ($rule) {
            case 'between':
                return [
                    'min' => $parameters[0] ?? '',
                    'max' => $parameters[1] ?? '',
                ];
            case 'size':
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':
            case 'min':
            case 'max':
                return [
                    'value' => $parameters[0] ?? '',
                    'min' => $parameters[0] ?? '',
                    'max' => $parameters[0] ?? '',
                ];
            default:
                return [];
        }
    }
}
