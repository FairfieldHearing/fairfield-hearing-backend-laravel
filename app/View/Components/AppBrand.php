<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="/" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center w-fit p-1">
                            <img src="/logo.jpeg" alt="Fairfield Hearing Logo" class="h-10 w-auto rounded-md object-contain" />
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed hidden mx-3 mt-4 mb-1">
                        <img src="/logo.jpeg" alt="Logo" class="h-8 w-8 rounded-md object-contain" />
                    </div>
                </a>
            HTML;
    }
}
