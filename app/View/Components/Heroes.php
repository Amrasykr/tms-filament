<?php

namespace App\View\Components;

use App\Models\Hero;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Heroes extends Component
{

    public $heroes;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->heroes = Hero::orderBy('order', 'asc')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.heroes', [
            'heroes' => $this->heroes
        ]);    }
}
