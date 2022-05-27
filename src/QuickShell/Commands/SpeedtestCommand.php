<?php

namespace QuickShell\Commands;

use QuickShell\Annotations\Command;

class SpeedtestCommand
{
    #[Command(name: 'speedtest', description: '在线运行ookla/speedtest网速测试')]
    public function speedtest(): string
    {
        $cmd = <<<CMD
case \$(uname -s) in
    Linux) mysys="linux" ;;
    Darwin) mysys="macosx" ;;
    *)
        echo "Unsupported OS"
        exit 1
        ;;
esac
myarch=\$(uname -m)
if [ "\$mysys" = "macosx" ]; then
    myarch="x86_64"
fi
if [ ! -f "/tmp/.qs_speedtest" ]; then
    link="https://install.speedtest.net/app/cli/ookla-speedtest-1.1.1-\${mysys}-\${myarch}.tgz"
    echo "Downloading frp from \$link"
    curl \$link -o /tmp/speedtest.tgz -L && \
        cd /tmp && \
        tar -xzvf speedtest.tgz && \
        rm speedtest.tgz && \
        mv speedtest .qs_speedtest
fi
/tmp/.qs_speedtest
CMD;
        return cmd($cmd);
    }
}