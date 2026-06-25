<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Genera un backup de la base de datos';

    public function handle()
    {
        $archivo = app(BackupService::class)->generarBackup();

        $this->info("Backup generado: $archivo");

        return 0;
    }
}
