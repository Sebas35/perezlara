<?php

namespace App\Traits\Controllers;

use App\Models\Insurance;
use App\Models\Insurer;

trait InsurerInsuranceActive
{
    public function productsActive(): array
    {
        return ['insurers' => (new Insurer()) -> index(), 'insurances' => (new Insurance()) -> index()];
    }
}
