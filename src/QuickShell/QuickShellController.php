<?php

namespace QuickShell;

use QuickShell\Annotations\Command;
use Swoole\Http\Request;
use ZM\Annotation\Swoole\OnRequestEvent;
use ZM\Annotation\Swoole\OnStart;
use ZM\Event\EventDispatcher;
use ZM\Exception\InterruptException;

class QuickShellController
{
    #[OnStart(-1)]
    public function onStart()
    {
        // 启动后的预处理
        QuickShellProvider::getInstance()->generateCommandList();
    }

    #[OnRequestEvent(rule: "true")]
    public function onRequest(Request $request)
    {
        // 寻找匹配的Command注解函数
        list($cmd, $params) = QuickShellProvider::getInstance()->matchCommand($request->server['request_uri'], ctx()->getRequest()->get ?? []);

        /** @var Command $cmd */
        $dispatcher = new EventDispatcher(Command::class);
        $dispatcher->dispatchEvent($cmd, null, $params);
        ctx()->getResponse()->end($dispatcher->store ?? '');
    }

    /**
     * @throws InterruptException
     */
    #[OnRequestEvent(rule: "ctx()->getRequest()->server['request_uri'] === '/favicon.ico'", level: 200)]
    public function onBanFavicon()
    {
        EventDispatcher::interrupt();
    }
}