<?php

namespace QuickShell;

use ZM\Config\ZMConfig;
use ZM\Console\Console;
use ZM\Utils\SingletonTrait;

class QuickShellProvider
{
    use SingletonTrait;

    public function getShellList(): array
    {
        $ls = [];
        foreach (ZMConfig::get('shell_list') as $shell_name => $shell_class) {
            $ls[] = Console::setColor($shell_name, 'green') . ":\t" . $shell_class['description'];
        }
        return $ls;
    }

    public function isShellExists($name)
    {
        return array_key_exists($name, ZMConfig::get('shell_list'));
    }

    public function getShellCommand($name)
    {
        $d = ZMConfig::get('shell_list')[$name]['command'] ?? null;
        if ($d === null) {
            return 'echo "command not found"';
        }
        return $d;
    }
}