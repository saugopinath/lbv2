<?php
namespace App\Contracts;

interface TwoFactorGeneratorInterface
{
    public function generate(): string;
    public function getLabel(): string;
    public function requiresSms(): bool;
    public function getDisplayCode(string $secretOrOtp): string;
    public function validate(string $secretOrOtp, string $input): bool;
    public function getExpirationMinutes(): int;
}
