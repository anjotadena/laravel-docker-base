<?php

namespace App\Http\Livewire;

use App\Models\Subscriber;
use Livewire\Component;

class LandingPage extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|email:filter|unique:subscribers,email'
    ];

    public function render()
    {
        return view('livewire.landing-page');
    }

    public function subscribe()
    {
        $data = $this->validate();

        $subscriber = Subscriber::create($data);

        $this->reset('email');
    }
}
