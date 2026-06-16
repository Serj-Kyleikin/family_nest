<?php

namespace App\SharedKernel\Repository\Contracts;

interface DeleteContract
{
    public function delete(array $filters): void;
}
