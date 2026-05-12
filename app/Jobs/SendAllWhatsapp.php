<?php

namespace App\Jobs;

use App\Traits\Whatsapp; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAllWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Whatsapp; // Use the Whatsapp trait

    protected $body;
    protected $deviceName;
    protected $recipient;
    protected $type;
    protected $isGroup;

    /**
     * Create a new job instance.
     *
     * @param  array  $body
     * @param  string  $deviceName
     * @param  string  $recipient
     * @param  string  $type
     * @param  bool  $isGroup
     * @return void
     */
    public function __construct(array $body, string $deviceName, string $recipient, string $type, bool $isGroup)
    {
        $this->body = $body;
        $this->deviceName = $deviceName;
        $this->recipient = $recipient;
        $this->type = $type;
        $this->isGroup = $isGroup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('Sending message to: ' . $this->recipient);
        \Log::info('Message body: ' . json_encode($this->body));
        \Log::info('Device: ' . $this->deviceName);
        \Log::info('Type: ' . $this->type);
        \Log::info('Is Group: ' . $this->isGroup);
    
        $response = self::messageSend(
            $this->body,
            $this->deviceName,
            $this->recipient,
            $this->type,
            $this->isGroup
        );

    }
}