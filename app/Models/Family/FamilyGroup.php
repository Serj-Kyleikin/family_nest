<?php

namespace App\Models\Family;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}