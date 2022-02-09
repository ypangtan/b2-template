<?php

namespace App\View\Components\OffCanvas;

use Illuminate\View\Component;

class FloatingLabelSelect extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $title, $options, $mandatory;

    public function __construct( $title, $options, $mandatory = false )
    {
        $this->title = $title;
        $this->options = $options;
        $this->mandatory = $mandatory;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view( 'components.off-canvas.floating-label-select' );
    }
}
