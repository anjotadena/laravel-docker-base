<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LandingPage extends Component
{
    public $email;

    public function render()
    {
        return view('livewire.landing-page');
    }

    public function subscribe()
    {
        Log::debug($this->email);
    }
}
