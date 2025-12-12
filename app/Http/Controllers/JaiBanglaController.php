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
    public function backfromjb()
    {
        $data = $this->jaiBanglaService->backfromjb();
    }
    public function logoutfromjb()
    {
        $data = $this->jaiBanglaService->logoutfromjb();
    }
    public function refreshtokenforjb()
    {
        $data = $this->jaiBanglaService->refreshtokenforjb();
    }
}
