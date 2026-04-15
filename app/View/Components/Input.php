<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $type;
    public $name;
    public $value;

    public function __construct($type = 'text', $name = null, $value = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.input');
    }
}
