<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MultiLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locales = ['en', 'zh'];

        foreach ( $locales as $locale ) {
            $path = resource_path( "lang/{$locale}" );
            $this->loadTranslationsFromPath( $path, $locale );
        }
    }

    private function loadTranslationsFromPath( $path, $locale, $module = null ) {
        foreach ( File::allFiles($path) as $file ) {
            $group = pathinfo( $file->getFilename(), PATHINFO_FILENAME );

            $translations = File::getRequire( $file->getPathname() );

            $flatTranslations = $this->flattenArray( $translations );

            foreach ( $flatTranslations as $key => $text ) {
                $exists = \DB::table( 'multi_language_messages' )->where( [
                    'module' => $group,
                    'message_key' => $key,
                    'language' => $locale,
                ] )->exists();

                if (!$exists) {
                    DB::table( 'multi_language_messages' )->insert( [
                        'module' => $group,
                        'message_key' => $key,
                        'language' => $locale,
                        'text' => $text,
                        'last_update_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ] );
                }
            }
        }
    }

    private function flattenArray( array $array, string $prefix = '' ): array {
        $result = [];

        foreach ( $array as $key => $value ) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if ( is_array( $value ) ) {
                $result += $this->flattenArray( $value, $newKey );
            } else {
                $result[ $newKey ] = $value;
            }
        }

        return $result;
    }
}
