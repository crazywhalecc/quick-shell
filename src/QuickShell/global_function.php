<?php

const QUICK_SHELL_VERSION = '1.0.0';

function cmd($cmd): string
{
    return $cmd . PHP_EOL;
}

function rawtext($text, $execution = true): string
{
    $lines = [];
    foreach (explode(PHP_EOL, $text) as $line) {
        $lines[] = "echo" . ($execution ? ' -e' : '') . " " . escapeshellarg($line);
    }
    return implode(" ;\n", $lines) . PHP_EOL;
}