<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Backup;

class DatabaseBackup extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create a database backup';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql.gz';
            $backupPath = 'backups/' . $filename;
            $fullPath = storage_path('app/' . $backupPath);

            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s | gzip > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );

            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($fullPath)) {
                $this->error('Backup failed: ' . implode("\n", $output));
                return Command::FAILURE;
            }

            // Record backup in database
            Backup::create([
                'filename' => $filename,
                'path' => $backupPath,
                'size' => filesize($fullPath),
            ]);

            // Clean old backups (keep 30 days)
            $this->cleanOldBackups();

            $this->info('Backup created successfully: ' . $filename);
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function cleanOldBackups(): void
    {
        $threshold = now()->subDays(30);
        
        $oldBackups = Backup::where('created_at', '<', $threshold)->get();
        
        foreach ($oldBackups as $backup) {
            $fullPath = storage_path('app/' . $backup->path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $backup->delete();
            $this->info('Deleted old backup: ' . $backup->filename);
        }
    }
}
