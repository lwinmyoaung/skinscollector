<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function index()
    {
        $logs = ErrorLog::with('user')->latest()->paginate(20);
        return view('admin.error_logs', compact('logs'));
    }

    public function show(ErrorLog $errorLog)
    {
        return response()->json([
            'stack_trace' => $errorLog->stack_trace,
            'message' => $errorLog->message,
        ]);
    }

    public function fetch()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return back()->with('error', 'Log file not found.');
        }

        $content = file_get_contents($logFile);
        
        // Regex to match log entries: [Date Time] Env.Level: Message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)/m';
        
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            $imported = 0;
            // Process last 50 matches (newest are at the end)
            $matches = array_reverse($matches);
            
            foreach ($matches as $match) {
                if ($imported >= 50) break;
                
                $timestamp = $match[1];
                $level = $match[3];
                $message = trim($match[4]);
                
                // Only import ERRORs
                if (strtoupper($level) !== 'ERROR') continue;
                
                // Check for duplicate
                $exists = ErrorLog::where('created_at', $timestamp)
                    ->where('message', 'LIKE', substr($message, 0, 100) . '%')
                    ->exists();
                    
                if (!$exists) {
                    $log = new ErrorLog([
                        'method' => 'LOG',
                        'url' => 'system',
                        'message' => $message,
                        'stack_trace' => 'Imported from laravel.log',
                    ]);
                    $log->created_at = $timestamp;
                    $log->save();
                    $imported++;
                }
            }
            
            if ($imported > 0) {
                return back()->with('success', "Successfully imported $imported new errors from system log.");
            } else {
                return back()->with('info', 'No new errors found in system log.');
            }
        }
        
        return back()->with('info', 'No parseable errors found in log file.');
    }

    public function destroy(ErrorLog $errorLog)
    {
        $errorLog->delete();
        return back()->with('success', 'Error log deleted successfully');
    }

    public function clear()
    {
        ErrorLog::truncate();
        
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
        
        return back()->with('success', 'All database logs and system log file cleared successfully');
    }
}
