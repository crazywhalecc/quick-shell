<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

$config = [];

/* bind host */
$config['host'] = '127.0.0.1';

/* bind port */
$config['port'] = 30001;

/* 框架开到公网或外部的HTTP访问链接，通过 DataProvider::getFrameworkLink() 获取 */
$config['http_reverse_link'] = 'http://shell.zhamao.xin';

/* 框架是否启动debug模式，当debug模式为true时，启用热更新（需要安装inotify扩展） */
$config['debug_mode'] = false;

/* 存放框架内文件数据的目录 */
$config['zm_data'] = realpath(WORKING_DIR) . '/zm_data/';

/* 存放各个模块配置文件的目录 */
$config['config_dir'] = $config['zm_data'] . 'config/';

/* 存放崩溃和运行日志的目录 */
$config['crash_dir'] = $config['zm_data'] . 'crash/';

/* 对应swoole的server->set参数 */
$config['swoole'] = [
    'log_file' => $config['crash_dir'] . 'swoole_error.log',
    'worker_num' => 1, //如果你只有一个 OneBot 实例连接到框架并且代码没有复杂的CPU密集计算，则可把这里改为1使用全局变量
    'dispatch_mode' => 2, // 包分配原则，见 https://wiki.swoole.com/#/server/setting?id=dispatch_mode
    'max_coroutine' => 300000,
    'max_wait_time' => 5,
    // 'task_worker_num' => 4,
    // 'task_enable_coroutine' => true
];

/* 一些框架与框架运行时设置的调整 */
$config['runtime'] = [
    'swoole_coroutine_hook_flags' => SWOOLE_HOOK_ALL & (~SWOOLE_HOOK_CURL),
    'swoole_server_mode' => SWOOLE_PROCESS,
    'middleware_error_policy' => 1,
    'reload_delay_time' => 800,
    'global_middleware_binding' => [],
    'save_console_log_file' => false, // 改为目标路径，则将 Console 输出的日志保存到文件
    'annotation_reader_ignore' => [ // 设置注解解析器忽略的注解名或命名空间，防止解析到不该解析的
        'name' => [
            'mixin',
        ],
        'namespace' => [],
    ],
];

/* 轻量字符串缓存，默认开启 */
$config['light_cache'] = [
    'size' => 512,                       // 最多允许储存的条数（需要2的倍数）
    'max_strlen' => 32768,               // 单行字符串最大长度（需要2的倍数）
    'hash_conflict_proportion' => 0.6,   // Hash冲突率（越大越好，但是需要的内存更多）
    'persistence_path' => $config['zm_data'] . '_cache.json',
    'auto_save_interval' => 900,
];

/* 大容量跨进程变量存储（2.2.0可用） */
$config['worker_cache'] = [
    'worker' => 0,
    'transaction_timeout' => 30000,
];

/* MySQL数据库连接信息，host留空则启动时不创建sql连接池 */
$config['mysql_config'] = [
    'host' => '',
    'port' => 3306,
    'unix_socket' => null,
    'username' => 'root',
    'password' => '123456',
    'dbname' => '',
    'charset' => 'utf8mb4',
    'pool_size' => 64,
    'options' => [
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

/* Redis连接信息，host留空则启动时不创建Redis连接池 */
$config['redis_config'] = [
    'host' => '',
    'port' => 6379,
    'timeout' => 1,
    'db_index' => 0,
    'auth' => '',
];

/* onebot连接约定的token */
$config['access_token'] = '';

/* HTTP服务器固定请求头的返回 */
$config['http_header'] = [
    'Server' => 'zhamao-framework',
    'Content-Type' => 'text/html; charset=utf-8',
];

/* HTTP服务器在指定状态码下回复的页面（默认） */
$config['http_default_code_page'] = [
    '404' => '404.html',
];

/* zhamao-framework在框架启动时初始化的atomic们 */
$config['init_atomics'] = [
    // 'custom_atomic_name' => 0, //自定义添加的Atomic
];

/* 终端日志显示等级（0-4） */
$config['info_level'] = 2;

/* 上下文接口类 implemented from ContextInterface */
$config['context_class'] = \ZM\Context\Context::class;

/* 静态文件访问 */
$config['static_file_server'] = [
    'status' => false,
    'document_root' => realpath(__DIR__ . '/../') . '/resources/html',
    'document_index' => [
        'index.html',
    ],
];

/* 机器人解析模块，关闭后无法使用如CQCommand等注解(上面的modules即将废弃) */
$config['onebot'] = [
    'status' => false,
    'single_bot_mode' => false,
    'message_level' => 99,
    'message_convert_string' => true,
    'message_command_policy' => 'interrupt',
];

/* 一个远程简易终端，使用nc直接连接即可，但是不建议开放host为0.0.0.0(远程连接) */
$config['remote_terminal'] = [
    'status' => false,
    'host' => '127.0.0.1',
    'port' => 20002,
    'token' => '',
];

/* 模块(插件)加载器的相关设置 */
$config['module_loader'] = [
    'enable_hotload' => false,
    'load_path' => $config['zm_data'] . 'modules',
];

return $config;
