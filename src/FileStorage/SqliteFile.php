<?php

namespace Apie\Core\FileStorage;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Exceptions\InvalidTypeException;

#[FakeMethod('createRandom')]
class SqliteFile extends StoredFile
{
    protected function validateState(): void
    {
        $serverMime = $this->getServerMimeType();
        if (!in_array($serverMime, ['application/vnd.sqlite3', 'application/x-empty', 'application/octet-stream'])) {
            throw new InvalidTypeException($serverMime, 'Sqlite3 database');
        }
    }

    public function getOrSetOnServerPath(): string
    {
        if ($this->serverPath !== null) {
            return $this->serverPath;
        }
        $tempFile = tempnam(sys_get_temp_dir(), 'sqlite');
        if ($tempFile === false) {
            throw new \RuntimeException('Unable to create a temporary SQLite file');
        }

        $source = null;
        $target = null;
        try {
            $source = $this->getStream()->detach();
            if (!is_resource($source)) {
                throw new \RuntimeException('Unable to open the SQLite file stream');
            }
            $target = fopen($tempFile, 'wb');
            if ($target === false) {
                throw new \RuntimeException('Unable to open the temporary SQLite file');
            }
            if (stream_copy_to_stream($source, $target) === false) {
                throw new \RuntimeException('Unable to copy the SQLite file to a temporary file');
            }

            $this->serverPath = $tempFile;
            $this->removeOnDestruct = true;
            return $this->serverPath;
        } catch (\Throwable $exception) {
            @unlink($tempFile);
            throw $exception;
        } finally {
            if (is_resource($source)) {
                fclose($source);
            }
            if (is_resource($target)) {
                fclose($target);
            }
        }
    }

    public static function createRandom(): static
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sqlite');
        if ($tempFile === false) {
            throw new \RuntimeException('Unable to create a temporary SQLite file');
        }
        $result = null;
        try {
            $conn = new \PDO(
                'sqlite:' . $tempFile,
                options: [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );
            $conn->exec('PRAGMA user_version = 0');
            unset($conn);
            $result = static::createFromLocalFile($tempFile, removeOnDestruct: true);
            $result->indexing = [];
        } finally {
            // $tempFile ownership is not moved to the created file class.
            if ($result === null) {
                @unlink($tempFile);
            }
        }
        
        return $result;
    }

    /**
     * @param array<array-key, string|null|int|float> $params
     */
    public function executeQuery(string $query, array $params = []): bool
    {
        $serverPath = $this->getOrSetOnServerPath();
        $conn = new \PDO(
            'sqlite:' . $serverPath,
            options: [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]
        );
        $statement = $conn->prepare($query);
        if ($statement === false) {
            return false;
        }
        $result = $statement->execute($params);
        unset($conn, $statement);

        return $result;
    }
    /**
     * @param array<array-key, string|null|int|float> $params
     * @return list<array<string, mixed>>
     */
    public function fetchAll(string $query, array $params = []): array
    {
        $serverPath = $this->getOrSetOnServerPath();
        $conn = new \PDO(
            'sqlite:' . $serverPath,
            options: [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );

        $statement = $conn->prepare($query);
        if ($statement === false) {
            return [];
        }

        $statement->execute($params);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


}
