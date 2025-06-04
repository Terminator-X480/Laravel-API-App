<?php

namespace App\Events;

class WhatsappNewNumberAdded
{
    public $phone, $message, $device, $phoneId;

    public function __construct($phone, $message, $device, $phoneId)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->device = $device;
        $this->phoneId = $phoneId;
    }
}
