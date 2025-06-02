<?php

namespace App\Translation;

use Illuminate\Translation\Translator;
use App\Services\MultiLanguageService;

class TranslationLoader extends Translator
{
    public function load($locale, $group, $namespace = null)
    {
        return [];
    }

    public function addNamespace($namespace, $hint) {}
    public function addJsonPath($path) {}
    public function namespaces() { return []; }

    // Custom method to get translations from the database using MultiLanguageService
    public function get( $key, array $replace = [], $locale = null, $fallback = true )
    {
        
        $language = $locale ? $locale : app()->getLocale();

        if ( str_contains( $key, '.' ) ) {
            if (str_contains( $key, 'validation.custom' ) ) {
                $parts = explode( '.', $key );
                $module = $parts[0];
                $message_key = implode( '.', array_slice( $parts, 3 ) );
            } else {
                [$module, $message_key] = explode( '.', $key, 2 );
            }

            $text = MultiLanguageService::getText( $module, $message_key, $language, $replace );
            if ( $text ) {
                return $text;
            }
        }

        return $key;
    }
}
