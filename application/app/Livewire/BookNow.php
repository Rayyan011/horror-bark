<?php

namespace App\Livewire;

use Livewire\Component;

class BookNow extends Component
{
    public function redirectToLogin()
    {
        return redirect()->to('/user');
    }

    public function render()
    {
        return view('livewire.book-now');
    }
}
