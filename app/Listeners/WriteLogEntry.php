<?php

namespace App\Listeners;

use App\Events\LogEntryCreated;
use Illuminate\Support\Facades\DB;

class WriteLogEntry
{
    public function handle(LogEntryCreated $event)
    {
        DB::table('wp_mt_lead_logs')->insert([
            'lead_id' => $event->lead_id,
            'action_id' => $event->action_id,
            'action_data' => $event->action_data ? json_encode($event->action_data) : null,
            'user_id' => $event->userId ? $event->userId : null,
            'created_on' => now(),
        ]);
    }
}
