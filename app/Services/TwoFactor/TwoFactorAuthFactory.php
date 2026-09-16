<?php
namespace App\Services\TwoFactor;

use App\Contracts\TwoFactorGeneratorInterface;
use Exception;

class TwoFactorAuthFactory
{
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function create(int $type): TwoFactorGeneratorInterface
    {
        if ($type == 12) {
            return new OtpGenerator();
        } elseif ($type == 13) {
            return new TotpGenerator();
        }
        
        throw new Exception("Invalid Two-Factor Authentication Type");
    }
}
