<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class TestLogger implements LoggerInterface
{
    use LoggerTrait;

    private const ERROR_COLOR = "\033[31m";

    private const ALERT_COLOR = "\033[35m";

    private const SHOW_COLOR = "\033[34m";

    private const SELECT_COLOR = "\033[32m";

    private const INSERT_COLOR = "\033[36m";

    private const OTHER_COLOR = "\033[33m";

    private const SYSTEM_QUERY_COLOR = "\033[90m";

    private bool $display = false;

    private int $countWrites = 0;

    private int $countReads = 0;

    public function countWriteQueries(): int
    {
        return $this->countWrites;
    }

    public function countReadQueries(): int
    {
        return $this->countReads;
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $sql = \strtolower((string) $message);
        if (\in_array($sql, ['insert', 'update', 'delete'], true)) {
            ++$this->countWrites;
        } elseif (! $this->isPostgresSystemQuery($sql)) {
            ++$this->countReads;
        }

        if (! $this->display) {
            return;
        }

        echo match ($level) {
            LogLevel::ERROR => " \n! " . self::ERROR_COLOR . $message . "\033[0m",
            LogLevel::ALERT => " \n! " . self::ALERT_COLOR . $message . "\033[0m",
            default => $this->formatMessage($message),
        };
    }

    public function display(): void
    {
        $this->display = true;
    }

    public function hide(): void
    {
        $this->display = false;
    }

    protected function isPostgresSystemQuery(string $query): bool
    {
        return \str_contains($query, 'constraint_name') || \str_contains($query, 'pg_') || \str_contains($query, 'information_schema');
    }

    private function formatMessage(string $message): string
    {
        if ($this->isPostgresSystemQuery($message)) {
            return " \n> " . self::SYSTEM_QUERY_COLOR . $message . "\033[0m";
        }

        return " \n> " . $this->outputColor($message) . $message . "\033[0m";
    }

    private function outputColor(string $message): string
    {
        if (\str_starts_with($message, 'SHOW')) {
            return self::SHOW_COLOR;
        } elseif (\str_starts_with($message, 'SELECT')) {
            return self::SELECT_COLOR;
        } elseif (\str_starts_with($message, 'INSERT')) {
            return self::INSERT_COLOR;
        }

        return self::OTHER_COLOR;
    }
}
