<?php

namespace QuickShell;

use QuickShell\Annotations\Command;
use QuickShell\Annotations\CommandArgument;
use QuickShell\Annotations\CommandCategory;
use QuickShell\Annotations\CommandOption;
use QuickShell\Commands\HelpCommand;
use ZM\Console\Console;
use ZM\Event\EventDispatcher;
use ZM\Event\EventManager;
use ZM\Event\EventMapIterator;
use ZM\Exception\InterruptException;
use ZM\Utils\SingletonTrait;

class QuickShellProvider
{
    use SingletonTrait;

    const RESERVED_COMMANDS = ['help'];

    public static array $shells = [];

    public static array $shell_alias = [];

    /**
     * 返回所有命令的帮助列表
     * @return array
     */
    public function getShellList(): array
    {
        $ls = [];
        $max_len = 0;
        foreach (self::$shells as $shell => $v) {
            $line = Console::setColor($shell, 'green') . ": ";
            if ($max_len < mb_strwidth($shell . ": ")) $max_len = mb_strwidth($shell . ": ");
            $description = $v['command']->description ?: '暂无描述';
            $len = mb_strwidth($shell . ": ");
            $ls[] = [$line, $len, $description];
        }
        foreach ($ls as $k => $v) {
            $ls[$k][0] = $v[0] . str_repeat(' ', $max_len - $v[1]);
        }

        return array_map(function ($x) {
            return $x[0] . $x[2];
        }, $ls);
    }

    /**
     * 输入uri，输出匹配的command注解事件
     * @param string $uri
     * @param array $get
     * @return array
     * @throws InterruptException
     */
    public function matchCommand(string $uri, array $get): array
    {
        $has_right_slash = mb_substr($uri, -1, 1) === '/';
        // 去除两端的斜杠
        $origin_uri = $uri = trim($uri, '/');
        $input_params = $get;
        $cmd = null;

        foreach (self::$shells as $k => $v) {
            if (mb_strpos($uri . '/', $k . '/') === 0) { // 右侧加盖防止匹配到短名称误匹配
                $cmd = $k;
                $uri = trim(mb_substr($uri, mb_strlen($cmd)), '/');
                break;

            }
        }

        foreach (self::$shell_alias as $k => $v) {
            if (mb_strpos($uri . '/', $k . '/') === 0) { // 右侧加盖防止匹配到短名称误匹配
                $cmd = $v;
                $uri = trim(mb_substr($uri, mb_strlen($k)), '/');
                break;
            }
        }

        if ($cmd !== null) {
            // 接下来解析参数
            $args = [];
            foreach (self::$shells[$cmd]['arguments'] as $arg) {
                /** @var CommandArgument $arg */
                if ($arg->one_argument) { // 如果后面的作为统一参数，则直接返回结果，无视CommandOption和后面的所有CommandArgument
                    if ($arg->allow_empty || $uri !== '') {
                        return [self::$shells[$cmd]['command'], [$arg->argument_name => urldecode($uri) . ($has_right_slash ? '/' : '')]];
                    } else {
                        ctx()->getResponse()->end(rawtext('命令 ' . $cmd . ' 参数 ' . $arg->argument_name . ' 为必需参数，不可为空！' . PHP_EOL . $this->generateHelpArgument($cmd)));
                        throw new InterruptException();
                    }
                } else { // 如果必需但参数单一，则shift一个参数
                    if ($uri === '') {
                        ctx()->getResponse()->end(rawtext('命令 ' . $cmd . ' 参数 ' . $arg->argument_name . ' 为必需参数，不可为空！' . PHP_EOL . $this->generateHelpArgument($cmd)));
                        throw new InterruptException();
                    }
                    $uri .= '/';
                    $arg_value = mb_substr($uri, 0, mb_strpos($uri, '/')); // 右侧加盖防止匹配不到或匹配出现错误
                    $uri = rtrim(mb_substr($uri, mb_strpos($uri, '/') + 1), '/'); // 下一个参数
                    $args[$arg->argument_name] = $arg_value;
                }
            }
            $divide = explode('/', $uri);
            foreach ($divide as $vs) {
                if ($vs === '') continue;
                $ss = explode("=", $vs);
                if ($ss[0] === '') continue;
                $input_params[$ss[0]] = $ss[1] ?? '';
            }
            foreach (self::$shells[$cmd]['options'] as $obj) {
                if ($obj->required === false) {
                    $args[$obj->option_name] = isset($input_params[$obj->option_name]);
                } else {
                    if (isset($input_params[$obj->option_name])) {
                        if ($input_params[$obj->option_name] === '') {
                            ctx()->getResponse()->end(rawtext('命令 ' . $cmd . ' 参数 ' . $obj->option_name . ' 不能为空'));
                            EventDispatcher::interrupt();
                        } else {
                            $args[$obj->option_name] = urldecode($input_params[$obj->option_name]);
                        }
                    } else {
                        $args[$obj->option_name] = null;
                    }
                }
            }
            return [self::$shells[$cmd]['command'], $args];
        }
        ctx()->getResponse()->end(rawtext($origin_uri !== '' ? ('无法匹配此快捷命令: ' . $origin_uri) : HelpCommand::getHelpTemplate()));
        throw new InterruptException;
    }
    /*
    ctf/asd/ihui/
    ctf/asd/
    ctf/asdasdasd/
    help/isi/
     * */
    /**
     * 启动前生成每个进程下的命令缓存，避免每次请求都要遍历所有的注解来找命令
     */
    public function generateCommandList()
    {
        foreach ((EventManager::$events[Command::class] ?? []) as $command) {
            /** @var Command $command */
            // 将category和命令名称结合，组成真正的名称
            $name = trim($command->name, '/');
            $category_store = null;
            foreach ((new EventMapIterator($command->class, $command->method, CommandCategory::class)) as $category) {
                /** @var CommandCategory $category */
                if ($category->category !== '') {
                    $category_store = $category->category;
                    $name = trim($category->category, '/') . '/' . $name;
                    break;
                }
            }
            // 缓存arguments
            $arguments = [];
            foreach ((new EventMapIterator($command->class, $command->method, CommandArgument::class)) as $argument) {
                /** @var CommandArgument $argument */
                $arguments[] = $argument;
            }
            // 缓存options
            $options = [];
            foreach ((new EventMapIterator($command->class, $command->method, CommandOption::class)) as $option) {
                /** @var CommandOption $option */
                $options[] = $option;
            }
            // 缓存command
            self::$shells[$name] = [
                'category' => $category_store,
                'command' => $command,
                'arguments' => $arguments,
                'options' => $options,
            ];
            if ($command->alias !== '') {
                $alias_name = $category_store !== null ? trim($category_store, '/') . '/' . $command->alias : $command->alias;
                self::$shell_alias[$alias_name] = $name;
            }
        }
    }

    private function generateHelpArgument(string $cmd)
    {
        $arg = Console::setColor($cmd . ' 命令参数指引', 'yellow') . ': ' . PHP_EOL . "\t$cmd";
        foreach (self::$shells[$cmd]['arguments'] as $argument) {
            /** @var CommandArgument $argument */;
            $arg .= '/' . Console::setColor('{' . $argument->argument_name . '}', 'green');
        }
        return $arg;
    }
}