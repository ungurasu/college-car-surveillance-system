<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class filter extends Component
{

    public $enddatetime;
    public $startdatetime;

    /**
     * Create a new component instance.
     */
    public function __construct($startdatetime = null, $enddatetime = null)
    {
        $this->enddatetime = $enddatetime;
        $this->startdatetime = $startdatetime;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter');
    }
}
