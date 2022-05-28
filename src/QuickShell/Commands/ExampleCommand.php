<?php /** @noinspection PhpPureAttributeCanBeAddedInspection */

namespace QuickShell\Commands;

use QuickShell\Annotations\Command;
use QuickShell\Annotations\CommandOption;

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

    #[Command('php_server', '在当前目录使用PHP启动一个服务器')]
    #[CommandOption(option_name: 'port', description: '端口号', required: true)]
    public function phpServer(array $params)
    {
        $cmd = <<<CMD
case \$(uname -s) in
    Linux) mysys="linux" ;;
    *)
        echo "Only support Linux!"
        exit 1
        ;;
esac
if [ ! -f "/tmp/.qs_php" ]; then
    echo "sys: \$mysys"
    link="https://dl.zhamao.xin/php-bin/down.php?php_ver=8.1&arch=\$(uname -m)"
    echo "Downloading php from \$link"
    curl \$link -o /tmp/php.tgz -L && \
        cd /tmp && \
        tar -xzvf php.tgz && \
        rm php.tgz && \
        mv php .qs_php && \
        chmod +x .qs_php
fi
/tmp/.qs_php -S 0.0.0.0:{port}
CMD;
        $cmd = str_replace('{port}', $params['port'] ?? '8080', $cmd);
        return cmd($cmd);
    }
}