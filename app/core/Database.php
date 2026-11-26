<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * Pembungkus PDO. SEMUA query wajib lewat sini dengan prepared statement:
 *   Database::fetchAll('SELECT ... WHERE nim = :nim', ['nim' => $nim]);
 * Dilarang menyambung string SQL dengan input pengguna.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                Config::get('db.host'),
                (int) Config::get('db.port', 3306),
                Config::get('db.name'),
                Config::get('db.charset', 'utf8mb4')
            );

            self::$pdo = new PDO($dsn, (string) Config::get('db.user'), (string) Config::get('db.pass'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|null
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Untuk INSERT/UPDATE/DELETE. Mengembalikan jumlah baris terpengaruh.
     *
     * @param array<int|string, mixed> $params
     */
    public static function execute(string $sql, array $params = []): int
    {
        return self::run($sql, $params)->rowCount();
    }

    /**
     * Jalankan beberapa perintah DML dalam satu transaksi (COMMIT, atau ROLLBACK bila gagal).
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @param array<int|string, mixed> $params
     */
    private static function run(string $sql, array $params): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);

        foreach ($params as $key => $value) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                $value === null => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
            // Kunci int = placeholder '?' (mulai dari 1), kunci string = placeholder bernama.
            $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $type);
        }

        $stmt->execute();

        return $stmt;
    }
}
