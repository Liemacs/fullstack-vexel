<?php

namespace App\Support;

use App\Models\Member;

class MemberPayload
{
    public static function format(Member $member): array
    {
        return [
            'slug' => $member->slug,
            'nickname' => $member->nickname,
            'name' => $member->name,
            'callSign' => $member->call_sign,
            'age' => $member->age,
            'status' => $member->status,
            'role' => $member->position?->name ?? $member->role,
            'position' => $member->position ? [
                'id' => $member->position->id,
                'name' => $member->position->name,
                'image' => $member->position->image,
            ] : null,
            'specialization' => $member->specialization,
            'image' => $member->image,
            'quote' => $member->quote,
            'bio' => $member->bio,
            'skills' => $member->skills,
            'gear' => $member->gear,
            'character' => $member->character,
            'vexelHistory' => $member->vexel_history,
            'connections' => $member->connections,
        ];
    }
}
