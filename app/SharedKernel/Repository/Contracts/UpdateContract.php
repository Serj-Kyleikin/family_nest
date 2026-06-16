<?php

namespace App\SharedKernel\Repository\Contracts;

interface UpdateContract
{
    public function update(array $filters, array $data): void;
    
    public function updateWhereIn(string $row, array $values, array $data): void;
}
