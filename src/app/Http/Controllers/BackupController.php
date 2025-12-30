<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index(): View
    {
        $backups = Backup::orderBy('created_at', 'desc')->paginate(20);
        return view('backups.index', compact('backups'));
    }

    public function create(): RedirectResponse
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql.gz';
            $backupPath = 'backups/' . $filename;
            $fullPath = storage_path('app/' . $backupPath);

            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Get database credentials from config
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
                return redirect()->route('backups.index')
                    ->with('error', 'Gagal membuat backup: ' . implode("\n", $output));
            }

            // Record backup in database
            Backup::create([
                'filename' => $filename,
                'path' => $backupPath,
                'size' => filesize($fullPath),
            ]);

            // Clean old backups (keep 30 days)
            $this->cleanOldBackups();

            return redirect()->route('backups.index')
                ->with('success', 'Backup berhasil dibuat.');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup): BinaryFileResponse|RedirectResponse
    {
        $fullPath = storage_path('app/' . $backup->path);

        if (!file_exists($fullPath)) {
            return redirect()->route('backups.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($fullPath, $backup->filename);
    }

    public function destroy(Backup $backup): RedirectResponse
    {
        $fullPath = storage_path('app/' . $backup->path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $backup->delete();

        return redirect()->route('backups.index')
            ->with('success', 'Backup berhasil dihapus.');
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
        }
    }
}
