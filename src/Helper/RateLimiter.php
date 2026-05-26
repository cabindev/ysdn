<?php
namespace App\Helper;

use PDO;
use App\Database\Db;

class RateLimiter {
    private const MAX_ATTEMPTS  = 5;
    private const WINDOW_MINUTES = 15;

    public static function isBlocked(string $identifier): bool {
        return self::countRecent($identifier) >= self::MAX_ATTEMPTS;
    }

    public static function recordFailure(string $identifier): void {
        Db::pdo()
            ->prepare("INSERT INTO login_attempts (identifier) VALUES (?)")
            ->execute([$identifier]);
    }

    public static function clear(string $identifier): void {
        Db::pdo()
            ->prepare("DELETE FROM login_attempts WHERE identifier = ?")
            ->execute([$identifier]);
    }

    /** Minutes remaining until block expires. */
    public static function retryAfterMinutes(string $identifier): int {
        $stmt = Db::pdo()->prepare("
            SELECT MIN(attempted_at) AS oldest
            FROM login_attempts
            WHERE identifier = ?
              AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$identifier, self::WINDOW_MINUTES]);
        $oldest = $stmt->fetchColumn();

        if (!$oldest) return 0;

        $unlockAt = strtotime($oldest) + (self::WINDOW_MINUTES * 60);
        return (int) ceil(max(0, $unlockAt - time()) / 60);
    }

    private static function countRecent(string $identifier): int {
        $pdo = Db::pdo();

        $pdo->prepare("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)")
            ->execute([self::WINDOW_MINUTES]);

        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM login_attempts
            WHERE identifier = ?
              AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$identifier, self::WINDOW_MINUTES]);
        return (int) $stmt->fetchColumn();
    }
}
