<?php /** @noinspection PhpPureAttributeCanBeAddedInspection */

namespace QuickShell\Commands;

use QuickShell\Annotations\Command;

class ExampleCommand
{
    #[Command(name: 'neofetch', description: '在线运行neofetch')]
    public function neofetch(): string
    {
        return cmd("bash <(curl -H \"User-Agent: Chrome\" -s https://gitee.com/mirrors/neofetch/raw/master/neofetch)");
    }

    #[Command('ip', '获取IP', '地址')]
    public function ip(): string
    {
        return cmd("curl -s http://ip.zhamao.xin");
    }
}