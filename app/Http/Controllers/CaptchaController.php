<?php

namespace App\Http\Controllers;

use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    public function refreshCaptcha()
    {
        return Captcha::img('math');
    }
}
