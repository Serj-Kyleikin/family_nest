<?php

namespace App\Services\Family;

use App\Models\Family\FamilyGroupLink;
use App\Services\{
    Family\Repositories\FamilyGroupLinkRepository,
    Family\Util\LinksRulesUtil,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FamilyLinkService
{
    public function __construct(
        private readonly FamilyGroupLinkRepository $familyGroupLinkRepository,
    ) {
    }

    public function create(): FamilyGroupLink
    {
        $group      = Auth::user()->getGroup();

                    $this->familyGroupLinkRepository->update(
                        ['group_id' => $group->id, 'is_active' => true],
                        ['is_active' => false]
                    );

        return $this->familyGroupLinkRepository->create([
                        'group_id'  => $group->id,
                        'token'     => Str::random(40),
                        'is_active' => true,
                    ]);
    }

    public function get(): FamilyGroupLink
    {
        $group      = Auth::user()->getGroup();

        $link       = $this->familyGroupLinkRepository->firstWhere(
                        ['group_id' => $group->id, 'is_active' => true]
                    );
                    LinksRulesUtil::isLinkExists($link);

        return $link;
    }
}
