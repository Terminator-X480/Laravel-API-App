<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogEntryCreated
{
    use Dispatchable, SerializesModels;

    public $lead_id;
    public $action_id;
    public $action_data;
    public $userId; 

    public function __construct($lead_id, $action_id, $action_data = [], $user_id = null)
    {
        $this->lead_id = $lead_id;
        $this->action_id = $action_id;
        $this->action_data = $action_data;
        $this->userId = $user_id ?? session('leads_user_id');
    }
}
