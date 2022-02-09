<?php

namespace App\View\Components\OffCanvas;

use Illuminate\View\Component;

class OffCanvas extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $title, $crud, $contents;

    public function __construct( $title, $crud, $contents = '' )
    {
        $this->title = $title;
        $this->crud = $crud;
        $this->contents = $contents;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view( 'components.off-canvas.off-canvas' );
    }
}
