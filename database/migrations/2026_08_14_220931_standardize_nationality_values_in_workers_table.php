<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Map of old free-text values (case-insensitive) to the new
     * Nationality enum values.
     */
    protected array $map = [
        'bangladesh' => 'bangladesh',
        'bd' => 'bangladesh',
        'philippines' => 'philippines',
        'philippine' => 'philippines',
        'india' => 'india',
        'pakistan' => 'pakistan',
        'nepal' => 'nepal',
        'sri lanka' => 'sri_lanka',
        'srilanka' => 'sri_lanka',
        'indonesia' => 'indonesia',
        'myanmar' => 'myanmar',
        'vietnam' => 'vietnam',
        'ethiopia' => 'ethiopia',
        'kenya' => 'kenya',
        'uganda' => 'uganda',
    ];

    public function up(): void
    {
        $workers = DB::table('workers')->select('id', 'nationality')->get();

        foreach ($workers as $worker) {
            $normalizedKey = strtolower(trim((string) $worker->nationality));
            $newValue = $this->map[$normalizedKey] ?? 'other';

            DB::table('workers')
                ->where('id', $worker->id)
                ->update(['nationality' => $newValue]);
        }
    }

    public function down(): void
    {
        // Data-only migration; original free-text values are not recoverable.
        // No rollback action performed.
    }
};