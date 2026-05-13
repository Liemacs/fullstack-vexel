<?php

namespace App\Support;

use App\Models\Member;

class MemberPayload
{
    public static function format(Member $member, bool $includeSensitive = true): array
    {
        $payload = [
            'slug' => $member->slug,
            'nickname' => $member->nickname,
            'name' => $member->name,
            'callSign' => $member->call_sign,
            'age' => $member->age,
            'status' => $member->status,
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

        if (! $includeSensitive) {
            return $payload;
        }

        return [
            ...$payload,
            'role' => $member->position?->name ?? $member->role,
            'position' => $member->position ? [
                'id' => $member->position->id,
                'name' => $member->position->name,
                'image' => $member->position->image,
            ] : null,
        ];
    }
}
