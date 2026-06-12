<?php

namespace App\SharedKernel\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CreateContract
{
    public function create(array $data): Model;
}
