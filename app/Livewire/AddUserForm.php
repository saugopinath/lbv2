<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Component;

class AddUserForm extends Component
{
    public $fullname;
    public $fullnameaadhar;
    public $displayname;
    public $email;
    public $mobile;
    public $role;
    public $roles = [];

    protected $rules = [
        'fullname' => 'required|string|max:255',
        'fullnameaadhar' => 'required|string|max:255',
        'displayname' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'mobile' => 'required|string|max:10',
        'role' => 'required|exists:roles,id',
    ];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function submit()
    {
        $this->validate();

        dd([
            'fullname' => $this->fullname,
            'fullnameaadhar' => $this->fullnameaadhar,
            'displayname' => $this->displayname,
            'email' => $this->email,
            'mobile' => $this->mobile,
            // 'role' => $this->role,
        ]);

        /*
        User::create([
            'name' => $this->displayname,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'role' => $this->role,
        ]);
        */

        $this->reset(['fullname', 'fullnameaadhar', 'displayname', 'email', 'mobile']);
    }

    public function render()
    {
        return view('livewire.add-user-form');
    }
}
