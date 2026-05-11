<?php

namespace App\Interfaces;


interface DuplicatecheckInterface
{
    public function authentication();
    public function duplicatecheck($checkWith, $schemeId, $inputValue, $otherSchemes);
    public function logout();
    public function refreshtoken();
}