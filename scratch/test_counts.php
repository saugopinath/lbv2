<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Livewire\AnnapurnaYojanaForm;

$form = new AnnapurnaYojanaForm();
$form->mount();

// 1. Initial State for HOF DBT Benefits
echo "Initial HOF DBT benefits count: " . count($form->formData['hof_dbt_benefits']) . "\n";

// Add HOF DBT benefits
$form->addHofDbtBenefit();
echo "After adding 1: " . count($form->formData['hof_dbt_benefits']) . "\n";

$form->addHofDbtBenefit();
echo "After adding another: " . count($form->formData['hof_dbt_benefits']) . "\n";

// Remove second HOF DBT benefit
$form->removeHofDbtBenefit(1);
echo "After removing second: " . count($form->formData['hof_dbt_benefits']) . "\n";

// 2. Initial State for Member DBT Benefits
$form->addMember(); // Active index 1, index 0 in members
echo "Initial Member 1 DBT benefits count: " . count($form->members[0]['dbt_benefits']) . "\n";

// Add Member DBT benefits
$form->addMemberDbtBenefit(0);
echo "After adding 1 to Member 1: " . count($form->members[0]['dbt_benefits']) . "\n";

// Remove Member DBT benefit
$form->removeMemberDbtBenefit(0, 0);
echo "After removing first from Member 1: " . count($form->members[0]['dbt_benefits']) . "\n";
