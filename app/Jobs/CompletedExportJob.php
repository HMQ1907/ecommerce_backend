<?php

namespace App\Jobs;

use App\Notifications\NotifyUserOfCompletedExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompletedExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $fileName;

    protected $data;

    public function __construct($user, $fileName)
    {
        $this->user = $user;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new NotifyUserOfCompletedExport($this->fileName));
    }
}
