<?php

namespace App\Console\Commands;

use App\Settings\BackupSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backupSettings = app(BackupSettings::class);

        if (!empty($backupSettings) && $backupSettings->status == 'active') {
            $data = $this->getBackupFiles();

            if (count($data) == 0) {
                Artisan::call('backup:run --only-db');
            } else {
                $date = $data[0]['last_modified'];
                $dateDifference = $date->diffInDays(now());

                if ($dateDifference >= $backupSettings->backup_after_days && Carbon::createFromFormat('H:i', $backupSettings->hour_of_day)->lessThan(now())) {
                    Artisan::call('backup:run --only-db');
                }
            }
        }

        return Command::SUCCESS;
    }

    public function getBackupFiles()
    {
        try {
            $storage = Storage::disk('backup');
            $files = $storage->files(config('app.name'));
            $data = [];

            foreach ($files as $file) {
                $ext = File::extension($file);

                if ($ext == 'zip' && $storage->exists($file)) {
                    $data[] = [
                        'file_path' => $file,
                        'file_name' => File::name($file).'.'.$ext,
                        'last_modified' => Carbon::parse($storage->lastModified($file)),
                    ];
                }
            }

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
