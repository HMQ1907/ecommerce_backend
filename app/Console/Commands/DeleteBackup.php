<?php

namespace App\Console\Commands;

use App\Settings\BackupSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DeleteBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete database backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backupSettings = app(BackupSettings::class);

        if (!empty($backupSettings) && $backupSettings->status == 'active') {
            $storage = Storage::disk('backup');
            $files = $storage->files(config('app.name'));

            foreach ($files as $file) {
                $ext = File::extension($file);

                if ($ext == 'zip' && $storage->exists($file)) {
                    $date = Carbon::parse($storage->lastModified($file));
                    $dateDifference = $date->diffInDays(now());

                    // If file is older, remove it
                    if ((int) $backupSettings->delete_backup_after_days > 0 && $dateDifference >= (int) $backupSettings->delete_backup_after_days) {
                        $storage->delete($file);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
