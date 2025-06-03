<?php

namespace App\Services;

use App\Jobs\SendOTP;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Hash,
    Http,
    Validator,
};
use App\Models\{
    MultiLanguageMessage,
    MultiLanguage,
};

use Illuminate\Validation\Rules\Password;

use App\Rules\CheckASCIICharacter;

use Helper;

use Carbon\Carbon;

class MultiLanguageService {

    public static function allMultiLanguages( $request ) {

        $multi_language = MultiLanguageMessage::with( [
            'last_update_by'
        ] )->select( 'multi_language_messages.*' );

        $filterObject = self::filter( $request, $multi_language );
        $multi_language = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $multi_language->orderBy( 'created_at', $dir );
                    break;
            }
        }

        $multi_languageCount = $multi_language->count();

        $limit = $request->length;
        $offset = $request->start;

        $multi_languages = $multi_language->skip( $offset )->take( $limit )->get();

        $multi_language = MultiLanguageMessage::select(
            DB::raw( 'COUNT(multi_language_messages.id) as total'
        ) );

        $filterObject = self::filter( $request, $multi_language );
        $multi_language = $filterObject['model'];
        $filter = $filterObject['filter'];

        $multi_language = $multi_language->first();

        $data = [
            'multi_languages' => $multi_languages,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $multi_languageCount : $multi_language->total,
            'recordsTotal' => $filter ? MultiLanguageMessage::count() : $multi_languageCount,
        ];

        return $data;
    }

    private static function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->module ) ) {
            $model->where( 'multi_language_messages.module', 'LIKE', '%' . $request->module . '%' );
            $filter = true;
        }
        
        if ( !empty( $request->message_key ) ) {
            $model->where( 'multi_language_messages.message_key', 'LIKE', '%' . $request->message_key . '%' );
            $filter = true;
        }
        
        if ( !empty( $request->language ) ) {
            switch ( $request->language ) {
                case '10':
                    $language = 'en';
                    break;
                case '20':
                    $language = 'ch';
                    break;
                default:
                    $language = 'en';
                    break;
            }
            $model->where( 'multi_language_messages.language', $language );
            $filter = true;
        }
        
        if ( !empty( $request->text ) ) {
            $model->where( 'multi_language_messages.text', 'LIKE', '%' . $request->text . '%' );
            $filter = true;
        }
        
        if ( !empty( $request->last_update_by ) ) {
            if( $request->last_update_by == 'system' ) {
                $model->whereNull( 'multi_language_messages.last_update_by' );
            } else {
                $model->whereHas( 'last_update_by', function( $query ) use ( $request ) {
                    $query->where( 'name', 'Like', '%' . $request->last_update_by . '%' )
                        ->orWhere( 'email', 'Like', '%' . $request->last_update_by . '%' );
                } );
            }
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneMultiLanguage( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );
        
        $multi_language = MultiLanguageMessage::with( [
            'last_update_by'
        ] )->find( $request->id );

        return $multi_language;
    }

    public static function createMultiLanguageAdmin( $request ) {

        DB::beginTransaction();

        $validator = Validator::make( $request->all(), [
            'module' => [ 'required' ],
            'message_key' => [ 'required' ],
            'text' => [ 'required' ],
            'language' => [ 'required' ],
        ] );

        $attributeName = [
            'module' => __( 'multi_language.module' ),
            'message_key' => __( 'multi_language.message_key' ),
            'text' => __( 'multi_language.text' ),
            'language' => __( 'multi_language.language' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $createMultiLanguage = MultiLanguageMessage::create( [
                'module' => $request->module,
                'message_key' => $request->message_key,
                'text' => $request->text,
                'language' => $request->language,
                'last_update_by' => auth()->user()->id
            ] );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.new_x_created', [ 'title' => Str::singular( __( 'template.multi_languages' ) ) ] ),
        ] );
    }

    public static function updateMultiLanguageAdmin( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'text' => [ 'required' ],
        ] );

        $attributeName = [
            'text' => __( 'multi_language.text' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();    

        try {

            $updateMultiLanguage = MultiLanguageMessage::lockForUpdate()
                ->find( $request->id );

            $updateMultiLanguage->text = $request->text;    
            $updateMultiLanguage->last_update_by = auth()->user()->id;
            $updateMultiLanguage->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.multi_languages' ) ) ] ),
        ] );
    }

    public static function getText( $module, $message_key, $language, $attribute = [] ) {

        $rendered = '';

        $message = MultiLanguageMessage::where( 'module', $module )
            ->where( 'message_key', $message_key )
            ->where( 'language', $language )
            ->first();

        if( !$message ) {
            $defaultText = Str::title( str_replace( '_', ' ', $message_key ) );

            if( $language != 'en' ) {
                $defaultText = self::translateText( $defaultText, $language );

                $message = MultiLanguageMessage::create( [
                    'module' => $module,
                    'message_key' => $message_key,
                    'text' => $defaultText,
                    'language' => $language,
                ] );
            }
            
            $message = MultiLanguageMessage::create( [
                'module' => $module,
                'message_key' => $message_key,
                'text' => $defaultText,
                'language' => $language,
            ] );
        }

        $rendered = $message->text;
        foreach ($attribute as $key => $val) {
            $rendered = str_replace( ":$key", $val, $rendered );
        }

        return $rendered;
    }
    protected static function translateText( $text, $lang ) {
        $apiKey = config('service.google.api_key');

        try {
            $response = Http::post("https://translation.googleapis.com/language/translate/v2", [
                'q' => $text,
                'target' => $lang,
                'format' => 'text',
                'source' => 'en',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['data']['translations'][0]['translatedText'];
            }
        } catch (\Exception $e) {
            \Log::error("Translation failed: " . $e->getMessage());
        }

        return $text;
    }
}