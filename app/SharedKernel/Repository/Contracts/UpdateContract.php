<?php

namespace App\SharedKernel\Repository\Contracts;

interface UpdateContract
{
    public function update(array $filters, array $data): void;
}
