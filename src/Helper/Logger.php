<?php
namespace App\Helper;

class Logger {
    private static string $logFile = '';

    private static function path(): string {
        if (self::$logFile === '') {
            self::$logFile = dirname(__DIR__, 2) . '/logs/app.log';
        }
        return self::$logFile;
    }

    public static function error(string $message, array $context = []): void {
        self::write('ERROR', $message, $context);
    }

    public static function info(string $message, array $context = []): void {
        self::write('INFO', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void {
        $dir = dirname(self::path());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );

        error_log($line, 3, self::path());
    }
}
