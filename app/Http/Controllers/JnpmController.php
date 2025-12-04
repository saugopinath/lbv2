<?php

namespace App\Http\Controllers;

use App\Interfaces\JNMPAuthenticationInterface;
use Illuminate\Http\Request;

class JnpmController extends Controller
{

    protected $jnmpAuthenticationService;

    public function __construct(JnmpAuthenticationInterface $jnmpAuthenticationService)
    {
        $this->jnmpAuthenticationService = $jnmpAuthenticationService;
    }
}
