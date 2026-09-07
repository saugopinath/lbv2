<?php

namespace App\Contracts;

interface AadhaarEncryptionServiceInterface
{
    public function generateEncryptedAadhaar(string $aadhaarNumber): string;
    public function detokenize(string $referenceKey): string;
}
