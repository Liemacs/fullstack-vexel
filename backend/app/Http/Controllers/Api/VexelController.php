<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\MapPoint;
use App\Models\Member;
use App\Models\Position;
use App\Models\TimelineEvent;
use App\Models\VehicleCategory;
use App\Support\MemberPayload;
use Illuminate\Http\JsonResponse;

class VexelController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'vexel-api',
        ]);
    }

    public function overview(): JsonResponse
    {
        return response()->json([
            'members' => Member::query()->count(),
            'active_contracts' => Contract::query()->where('status', 'Активно')->count(),
            'map_points' => MapPoint::query()->count(),
            'timeline_events' => TimelineEvent::query()->count(),
        ]);
    }

    public function members(): JsonResponse
    {
        return response()->json(
            Member::query()->with('position')->orderBy('id')->get()->map(fn (Member $member): array => MemberPayload::format($member))
        );
    }

    public function member(Member $member): JsonResponse
    {
        $member->load('position');

        return response()->json(MemberPayload::format($member));
    }

    public function positions(): JsonResponse
    {
        return response()->json(
            Position::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'image'])
        );
    }

    public function contracts(): JsonResponse
    {
        return response()->json(
            Contract::query()->orderBy('number')->get(['number', 'title', 'status', 'payment', 'client', 'text'])
        );
    }

    public function vehicles(): JsonResponse
    {
        return response()->json(
            VehicleCategory::query()
                ->with('vehicles')
                ->orderBy('sort_order')
                ->get()
                ->map(fn (VehicleCategory $category): array => [
                    'category' => $category->category,
                    'icon' => $category->icon,
                    'items' => $category->vehicles->map(fn ($vehicle): array => [
                        'name' => $vehicle->name,
                        'status' => $vehicle->status,
                        'text' => $vehicle->text,
                    ])->values(),
                ])
        );
    }

    public function mapPoints(): JsonResponse
    {
        return response()->json(
            MapPoint::query()
                ->orderBy('id')
                ->get()
                ->map(fn (MapPoint $point): array => [
                    'id' => $point->public_id,
                    'name' => $point->name,
                    'type' => $point->type,
                    'x' => $point->x,
                    'y' => $point->y,
                    'risk' => $point->risk,
                    'text' => $point->text,
                ])
        );
    }

    public function timeline(): JsonResponse
    {
        return response()->json(
            TimelineEvent::query()
                ->with('chapters')
                ->orderBy('sort_order')
                ->orderBy('year')
                ->get()
                ->map(fn (TimelineEvent $event): array => [
                    'year' => $event->year,
                    'title' => $event->title,
                    'text' => $event->text,
                    'chapters' => $event->chapters->map(fn ($chapter): array => [
                        'chapter' => $chapter->chapter,
                        'title' => $chapter->title,
                        'text' => $chapter->text,
                    ])->values(),
                ])
        );
    }
}
