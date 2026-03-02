<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemManagementController extends Controller
{
    public function index()
    {
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment'     => app()->environment(),
            'debug_mode'      => config('app.debug'),
            'timezone'        => config('app.timezone'),
            'cache_driver'    => config('cache.default'),
            'queue_driver'    => config('queue.default'),
            'db_connection'   => config('database.default'),
        ];

        $logFiles = $this->getLogFiles();

        return view('admin.system-management.index', compact('info', 'logFiles'));
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            return response()->json(['success' => true, 'message' => 'Application cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear cache: {$e->getMessage()}"], 500);
        }
    }

    public function clearConfig()
    {
        try {
            Artisan::call('config:clear');
            return response()->json(['success' => true, 'message' => 'Config cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear config cache: {$e->getMessage()}"], 500);
        }
    }

    public function clearView()
    {
        try {
            Artisan::call('view:clear');
            return response()->json(['success' => true, 'message' => 'View cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear view cache: {$e->getMessage()}"], 500);
        }
    }

    public function clearRoute()
    {
        try {
            Artisan::call('route:clear');
            return response()->json(['success' => true, 'message' => 'Route cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear route cache: {$e->getMessage()}"], 500);
        }
    }

    public function clearAll()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            return response()->json(['success' => true, 'message' => 'All caches cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear all caches: {$e->getMessage()}"], 500);
        }
    }

    public function optimize()
    {
        try {
            Artisan::call('optimize');
            return response()->json(['success' => true, 'message' => 'Application optimized successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Optimization failed: {$e->getMessage()}"], 500);
        }
    }

    public function readLog(Request $request)
    {
        $request->validate([
            'file'  => 'required|string',
            'lines' => 'integer|min:10|max:2000',
        ]);

        $filename = basename($request->input('file'));
        $logPath  = storage_path("logs/{$filename}");

        if (!file_exists($logPath)) {
            return response()->json(['success' => false, 'message' => 'Log file not found.'], 404);
        }

        $maxLines = (int) $request->input('lines', 200);
        $content  = $this->tailFile($logPath, $maxLines);

        return response()->json([
            'success'  => true,
            'file'     => $filename,
            'content'  => $content ?: '(empty)',
            'size'     => $this->formatBytes(filesize($logPath)),
            'modified' => date('Y-m-d H:i:s', filemtime($logPath)),
        ]);
    }

    public function clearLog(Request $request)
    {
        $request->validate(['file' => 'required|string']);

        $filename = basename($request->input('file'));
        $logPath  = storage_path("logs/{$filename}");

        if (!file_exists($logPath)) {
            return response()->json(['success' => false, 'message' => 'Log file not found.'], 404);
        }

        try {
            file_put_contents($logPath, '');
            return response()->json(['success' => true, 'message' => 'Log file cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Failed to clear log: {$e->getMessage()}"], 500);
        }
    }

    private function tailFile(string $path, int $lines): string
    {
        $fp = fopen($path, 'rb');
        if (!$fp) {
            return '';
        }

        fseek($fp, 0, SEEK_END);
        $size = ftell($fp);

        if ($size === 0) {
            fclose($fp);
            return '';
        }

        $chunk    = 8192;
        $buffer   = '';
        $found    = 0;
        $position = $size;

        while ($position > 0 && $found <= $lines) {
            $read      = min($chunk, $position);
            $position -= $read;
            fseek($fp, $position);
            $buffer = fread($fp, $read) . $buffer;
            $found  = substr_count($buffer, "\n");
        }

        fclose($fp);

        $all = explode("\n", $buffer);

        return implode("\n", array_slice($all, max(0, count($all) - $lines)));
    }

    private function getLogFiles(): array
    {
        $logDir = storage_path('logs');
        $files  = [];

        if (!is_dir($logDir)) {
            return $files;
        }

        foreach (glob("{$logDir}/*.log") as $path) {
            $files[] = [
                'name'     => basename($path),
                'size'     => $this->formatBytes(filesize($path)),
                'modified' => date('Y-m-d H:i:s', filemtime($path)),
            ];
        }

        usort($files, fn($a, $b) => strcmp($b['modified'], $a['modified']));

        return $files;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return "{$bytes} B";
    }
}
