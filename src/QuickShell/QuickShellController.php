<?php

namespace QuickShell;

use ZM\Annotation\Http\Controller;
use ZM\Annotation\Http\RequestMapping;
use ZM\Annotation\Swoole\OnRequestEvent;
use ZM\Config\ZMConfig;
use ZM\Event\EventDispatcher;
use ZM\Exception\InterruptException;

/**
 * @Controller("/")
 */
class QuickShellController
{
    /**
     * @RequestMapping("/manifest")
     */
    public function manifest()
    {
        return json_encode(ZMConfig::get('shell_list'), 128|256);
    }

    /**
     * @RequestMapping("/")
     * @RequestMapping("/index")
     * @RequestMapping("/list")
     */
    public function index()
    {
        $response = implode("\n", QuickShellProvider::getInstance()->getShellList()) . PHP_EOL;
        $response .= "普通执行:\tcurl -s http://shell.zhamao.xin/run/{name} | bash" . PHP_EOL;
        $response .= "交互执行:\tbash <(curl -s http://shell.zhamao.xin/run/{name})" . PHP_EOL;
        return $response;
    }

    /**
     * @RequestMapping("/test")
     * @return string
     */
    public function test()
    {
        return cmd('bash -c "$(curl -fsSL https://api.zhamao.xin/tools/env.sh)"');
    }

    /**
     * @RequestMapping("/run")
     */
    public function runHelp()
    {
        return cmd('echo ""');
    }

    /**
     * @RequestMapping("/run/{name}")
     *
     * @param $param
     * @return string
     */
    public function run($param): string
    {
        $shell = QuickShellProvider::getInstance()->isShellExists($param['name']);
        if (!$shell) {
            return cmd("echo 'shell \"".$param['name']."\" not found'");
        }
        return cmd(QuickShellProvider::getInstance()->getShellCommand($param['name']));
    }

    /**
     * 阻止 Chrome 自动请求 /favicon.ico 导致的多条请求并发和干扰
     * @OnRequestEvent(rule="ctx()->getRequest()->server['request_uri'] == '/favicon.ico'",level=200)
     * @throws InterruptException
     */
    public function onRequest()
    {
        EventDispatcher::interrupt();
    }
}