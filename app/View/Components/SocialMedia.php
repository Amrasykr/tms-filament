<?php

namespace App\View\Components;

use App\Models\SocialMedia as ModelsSocialMedia;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SocialMedia extends Component
{

    public $socialMedias;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->socialMedias = ModelsSocialMedia::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.social-media',
            [
                'socialMedias' => $this->socialMedias
            ]);
    }
}
