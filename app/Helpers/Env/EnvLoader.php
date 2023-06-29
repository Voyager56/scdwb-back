<?php

namespace App\Helpers\Env;
class EnvLoader
{
    private array $envVariables = [];
    public function load(): void
    {
        $filePath = dirname(__DIR__) . "/../../.env";

        if (!file_exists($filePath)) {
            throw new \RuntimeException("Unable to load environment file: $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if ($this->isComment($line)) {
                continue;
            }

            [$key, $value] = $this->parseLine($line);
            $this->envVariables[$key] = $value;
        }
    }

    public function get(string $key): string
    {
        return $this->envVariables[$key] ?? '';
    }
    private function isComment($line) : bool
    {
        return str_starts_with(trim($line), '#');
    }

    private function parseLine($line): array
    {
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($this->isQuoted($value)) {
            $value = $this->removeQuotes($value);
        }

        return [$key, $value];
    }

    private function isQuoted($value): bool
    {
        return (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1)
            || (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1);
    }

    private function removeQuotes($value): string
    {
        return trim($value, '\'"');
    }
}
