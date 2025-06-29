<?php

namespace Database\Seeders;

use App\Models\PointsHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointsHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'TPA001',
            'action' => 'Tukar Poin',
            'points' => -50,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'SM001',
            'action' => 'Story Instagram',
            'points' => 5,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'RG001',
            'action' => 'Rating Gmaps',
            'points' => 10,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'TPA002',
            'action' => 'Tukar Poin',
            'points' => -100,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'TPA003',
            'action' => 'Tukar Poin',
            'points' => -200,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'SM002',
            'action' => 'Story Instagram',
            'points' => 5,
        ]);
        PointsHistory::create([
            'phone' => '089508256626',
            'action_id' => 'RG002',
            'action' => 'Rating Gmaps',
            'points' => 10,
        ]);
    }
}
