<?php

namespace QuickShell\Commands;

use QuickShell\Annotations\Command;
use QuickShell\Annotations\CommandArgument;
use QuickShell\QuickShellProvider;
use ReflectionClass;
use ReflectionException;
use ZM\Config\ZMConfig;
use ZM\Console\Console;

class HelpCommand
{
    public static function getHelpTemplate(): string
    {
        $response = "QuickShell ".Console::setColor(QUICK_SHELL_VERSION, 'green')."\n\n";
        $response .= "支持的命令:\n";
        $response .= "\t" . implode("\n\t", QuickShellProvider::getInstance()->getShellList());
        $response .= "\n\n\t" . Console::setColor('help/{命令名}', "yellow") . ":\t查看对应的命令详情";
        $response .= "\n使用方法:\n\t在路径右方填入要使用的名称即可.";
        $response .= "\n\t输入右侧命令\tbash <(curl -s " . ZMConfig::get('global')['http_reverse_link'] . "/{name})";
        $response .= "\n\t使用例子\tbash <(curl -s " . ZMConfig::get('global')['http_reverse_link'] . "/neofetch)";
        return $response;
    }

    /**
     * @throws ReflectionException
     */
    #[Command('help', '查看帮助', alias: 'h')]
    #[CommandArgument('command', description: '查看指定命令的帮助信息', one_argument: true, allow_empty: true)]
    public function defaultCommand(array $params): string
    {
        zm_dump(QuickShellProvider::$shells);
        $name = trim($params['command'], '/');
        if ($name === '') {
            return rawtext(self::getHelpTemplate());
        }
        if (isset(QuickShellProvider::$shells[$name])) {
            $event = QuickShellProvider::$shells[$name]['command'];
            $reflection = new ReflectionClass($event->class);
            $method = $reflection->getMethod($event->method);
            $cmd = $method->getNumberOfRequiredParameters() === 0 ? $method->invoke($reflection->newInstance()) : '(* 此命令需要参数，如需查看源码，使用/showcode/'.$name.' *)';
            $reply = Console::setColor($event->name, 'green') . ":";
            $reply .= "\n\t要执行的命令:\t" . $cmd;
            return rawtext($reply);
        }
        return rawtext('命令不存在: ' . $name);
    }

    /**
     * @throws ReflectionException
     */
    #[Command('showcode')]
    #[CommandArgument('command', description: '查看指定命令的源码', one_argument: true, allow_empty: true)]
    public function helpCommandCode(array $params): string
    {
        zm_dump(QuickShellProvider::$shells);
        $name = trim($params['command'], '/');
        if ($name === '') {
            return rawtext('请在后方输入命令名称再试！');
        }
        if (isset(QuickShellProvider::$shells[$name])) {
            $event = QuickShellProvider::$shells[$name]['command'];
            $reflection = new ReflectionClass($event->class);
            $method = $reflection->getMethod($event->method);
            $file = file_get_contents($method->getFileName());
            $file = str_replace("\r", '', $file);
            $file = explode("\n", $file);
            $fileline = [];
            for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
                $fileline[] = $file[$i];
            }
            return rawtext(implode("\n", $fileline), false);
        }
        return rawtext('命令不存在: ' . $name);
    }
}