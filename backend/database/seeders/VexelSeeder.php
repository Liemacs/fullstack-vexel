<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\MapPoint;
use App\Models\Member;
use App\Models\TimelineEvent;
use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VexelSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::table('timeline_chapters')->delete();
        TimelineEvent::query()->delete();
        DB::table('vehicles')->delete();
        VehicleCategory::query()->delete();
        MapPoint::query()->delete();
        Contract::query()->delete();
        Member::query()->delete();

        DB::statement('PRAGMA foreign_keys = ON');
    }
}
