<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\JaiBanglaInterface;
class JaiBanglaController extends Controller
{
    protected $jaiBanglaService;
    public function __construct(JaiBanglaInterface $jaiBanglaService)
    {
        $this->jaiBanglaService = $jaiBanglaService;
    }
    public function jaibangla()
    {
        $data = $this->jaiBanglaService->athentication();
        dd($data);
    }
}
