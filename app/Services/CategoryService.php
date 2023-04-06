<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    Category,
    CategoryStructure,
};

use Helper;

use Carbon\Carbon;

class CategoryService {

    public function allCategories( $request ) {

        $category = Category::select( 'categories.*' );

        $filterObject = self::filter( $request, $category );
        $category = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 2:
                    $category->orderBy( 'created_at', $dir );
                    break;
                case 3:
                    $category->orderBy( 'title', $dir );
                    break;
                case 4:
                    $category->orderBy( 'email', $dir );
                    break;
            }
        }

        $categoryCount = $category->count();

        $limit = $request->length;
        $offset = $request->start;

        $categories = $category->skip( $offset )->take( $limit )->get();

        if ( $categories ) {
            $categories->append( [
                'path',
                'encrypted_id'
            ] );
        }

        $totalRecord = Category::count();

        $data = [
            'categories' => $categories,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $categoryCount : $totalRecord,
            'recordsTotal' => $totalRecord,
        ];

        return response()->json( $data );
    }

    private function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->created_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $request->title ) ) {
            $model->where( 'title', $request->title );
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'status', $request->status );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public function oneCategory( $request ) {

        $category = Category::find( Helper::decode( $request->id ) );

        if ( $category ) {
            $category->append( [
                'path',
                'encrypted_id'
            ] );
        }

        return response()->json( $category );
    }

    public function createCategory( $request ) {

        $request->validate( [
            'title' => [ 'required', 'string' ],
            'description' => [ 'required', 'string' ],
            'thumbnail' => [ 'nullable', 'image' ],
            'enabled' => [ 'required' ],
            'category_type' => [ 'required' ],
            'parent_category' => [ function( $attribute, $value, $fail ) {
                if ( request( 'category_type' ) == 2 ) {
                    if ( $value == 'null' ) {
                        $fail( __( 'validation.required' ) );
                    }
                }
            } ]
        ] );

        $basicAttribute = [
            'title' => $request->title,
            'description' => $request->description,
            'url_slug' => \Str::slug( $request->title ),
            'sort' => 1,
            'status' => $request->enabled,
            'type' => $request->category_type,
        ];

        if ( $request->parent_category != 'null' ) {
            $parentCategory = Category::find( $request->parent_category );
            $basicAttribute['parent_id'] = $parentCategory->id;
            $basicAttribute['structure'] = $parentCategory->structure . '|' . $parentCategory->id;
        } else {
            $basicAttribute['parent_id'] = null;
            $basicAttribute['structure'] = '-';
        }

        if ( $request->hasFile( 'thumbnail' ) ) {
            $basicAttribute['thumbnail'] = $request->file( 'thumbnail' )->store( 'category', [ 'disk' => 'public' ] );
        }

        $createCategory = Category::create( $basicAttribute );

        if ( $request->parent_category ) {

            $structureArray = explode( '|', $basicAttribute['structure'] );
            $structureLevel = count( $structureArray );
            for ( $i = $structureLevel - 1; $i >= 0; $i-- ) {
                if ( $structureArray[$i] != '-' ) {
                    CategoryStructure::create( [
                        'parent_id' => $structureArray[$i],
                        'child_id' => $createCategory->id,
                        'level' => $structureLevel - $i,
                        'status' => 10,
                    ] );
                }
            }
        }
    }

    public function updateCategory( $request ) {

        $request->validate( [
            'title' => [ 'required' ],
            'description' => [ 'required', 'string' ],
            'thumbnail' => [ 'nullable', 'image' ],
            'enabled' => [ 'required' ],
            'category_type' => [ 'required', function( $attribute, $value, $fail ) {

            } ],
        ] );

        $updateCategory = Category::find( Helper::decode( $request->id ) );
        $updateCategory->title = $request->title;
        if ( $request->hasFile( 'thumbnail' ) ) {
            Storage::disk( 'public' )->delete( $updateCategory->thumbnail );
            $updateCategory->thumbnail = $request->file( 'thumbnail' )->store( 'category', [ 'disk' => 'public' ] );
        } else {
            if ( $request->thumbnail_removed ) {
                Storage::disk( 'public' )->delete( $updateCategory->thumbnail );
                $updateCategory->thumbnail = null;
            }
        }
        $updateCategory->url_slug = \Str::slug( $request->title );
        $updateCategory->status = $request->enabled;
        $updateCategory->save();
    }

    public function updateCategoryStatus ( $request ) {

    }

    public function getCategoryStructure( $request ) {

        $categories = Category::where( 'type', 1 )
            ->get()->toArray();

        foreach( $categories as $key => $category ) {
            $categories[$key]['level'] = 0;
            $categories[$key]['childrens'] = self::traverseDown( $category['id'], 0 );
        }

        return $categories;
    }

    // This block 80% was written by ChatGPT, I feel I am jobless soon 
    public function traverseDown( $id, $level = 0 ) {

        $categories = Category::where( 'parent_id', $id )->get();

        $newCategories = [];

        foreach( $categories as $key => $category ) {

            $childrens = self::traverseDown( $category->id, $level + 1 );

            $category['level'] = $level + 1;
            $category['childrens'] = $childrens;

            $newCategories[] = $category;
        }

        return $newCategories;
    }
    // End
}