<?php

namespace App\Interfaces;

use App\Models\User;

interface UserInterface
{

    public function find(int $id): ?User;
    public function findbyMobile(string $mobile_no): ?User;
    public function isPasswordSet(int $userid): bool;
    public function isPasswordExpired(int $userid): bool;
    public function isDDOLogin(int $userid): bool;
    public function validPassword(int $userid,string $password): bool;

}