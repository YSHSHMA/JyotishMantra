<?php

namespace App\Jobs;

use App\Utils\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $messageData;
    public string $modelName;
    public string $type;
    public int $tries = 8;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(string $modelName, string $type, array $messageData)
    {
        $this->messageData = $messageData;
        $this->modelName = $modelName;
        $this->type = $type;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use the Helpers class to send the WhatsApp message
        Helpers::whatsappMessage($this->modelName, $this->type, $this->messageData);
    }
}