<?php

namespace App\Services\Family\Handler;

use App\Services\Family\Repositories\FamilyUserRepository;
use Illuminate\Support\Facades\Auth;

class FamilyMemberNameHandler
{
    public function __construct(
        private readonly FamilyUserRepository $familyUserRepository,
    ) {
    }

    public function apply(iterable $members): void
    {
        $members    = collect($members)->filter();
        $aliases    = $this->familyUserRepository->getAliasesByMemberIds(
                        intval(Auth::id()),
                        $members->pluck('id')->all()
                    );

        foreach ($members as $member) {
            $member->name = $aliases[$member->id] ?? $member->name;
        }
    }
}
