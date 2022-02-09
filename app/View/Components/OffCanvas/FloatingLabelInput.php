<?php

namespace App\View\Components\OffCanvas;

use Illuminate\View\Component;

class FloatingLabelInput extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $title, $placeholder, $type, $content, $mandatory, $smalltext;

    public function __construct( $title, $type, $content = '', $mandatory = false, $smalltext = '' )
    {
        $this->title = $title;
        $this->type = $type;
        $this->content = $content;
        $this->mandatory = $mandatory;
        $this->smalltext = $smalltext;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view( 'components.off-canvas.floating-label-input' );
    }
}
