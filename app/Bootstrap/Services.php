<?php


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;


// 配置
$config = Yaml::parse(file_get_contents(APP_PATH . '/Config/app.yml'));
switch ($config['setting']['sandbox']) {
    case true:
        error_reporting(E_ALL);
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        break;
    default:
        header_remove('X-Powered-By');
        error_reporting(0);
}
ini_set("date.timezone", $config['setting']['timezone']);
list($game_id, $app, $version) = explode('|', $config['setting']['default']);


// 路由
unset($_GET['_url']);
$controller = $action = '';
$uri_arr = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
if (count($uri_arr) == 2) {
    list($controller, $action) = $uri_arr;
    if (in_array($controller, ['yar', 'soap'])) {
        $protocol = $controller;
        $controller = $action;
        $action = '';
    }
    else {
        $protocol = 'http';
        if (strpos($action, '?')) {
            $action = substr($action, 0, strpos($action, '?'));
        }
    }
}
elseif (count($uri_arr) >= 4) {
    list($app, $version, $controller, $action) = $uri_arr;
    if (in_array($controller, ['yar', 'soap'])) {
        $protocol = $controller;
        $controller = $action;
        $action = '';
    }
    else {
        $protocol = 'http';
        if (strpos($action, '?')) {
            $action = substr($action, 0, strpos($action, '?'));
        }
    }
}
if (empty($protocol)) {
    $protocol = 'http';
    $controller = 'Default';
    $action = 'index';
}


// 容器
$di = new Container();

$di['route'] = function () use ($game_id, $app, $version, $protocol) {
    return ['game_id' => $game_id, 'app' => $app, 'version' => $version, 'protocol' => $protocol];
};

$di['request'] = function () {
    return new Request($_GET, $_POST);
};

$di['response'] = function () {
    return new Response('', Response::HTTP_OK, array('content-type' => 'text/html'));
};

$di['logger'] = function ($di) {
    $formatter = new LineFormatter(null, "Y-m-d H:i:sO");
    $stream = new StreamHandler(
        APP_PATH . '/Logs/' . $di['route']['app'] . '_' . $di['route']['version'] . date('Ymd'),
        Logger::DEBUG
    );
    $stream->setFormatter($formatter);
    $logger = new Logger('logger');
    $logger->pushHandler($stream);
    return $logger;
};

$di['config'] = function () use ($config) {
    return $config;
};

$di['db_cfg'] = function () use ($app, $version) {
    $configDb = Yaml::parse(file_get_contents(APP_PATH . '/Config/' . $app . '/' . $version . '.yml'));
    return $configDb;
};

// @link http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html
$di['db_data'] = function ($di) {
    $params = array(
        'driver'   => 'pdo_' . $di['config']['db_data']['adapter'],
        'host'     => $di['config']['db_data']['host'],
        'port'     => $di['config']['db_data']['port'],
        'user'     => $di['config']['db_data']['user'],
        'password' => $di['config']['db_data']['pass'],
        'dbname'   => $di['config']['db_data']['db'],
        'charset'  => $di['config']['db_data']['charset'],
    );
    $conn = DriverManager::getConnection($params);
    return $conn;
};

$di['db_logs'] = function ($di) {
    $params = array(
        'driver'   => 'pdo_' . $di['config']['db_logs']['adapter'],
        'host'     => $di['config']['db_logs']['host'],
        'port'     => $di['config']['db_logs']['port'],
        'user'     => $di['config']['db_logs']['user'],
        'password' => $di['config']['db_logs']['pass'],
        'dbname'   => $di['config']['db_logs']['db'],
        'charset'  => $di['config']['db_logs']['charset'],
    );
    $conn = DriverManager::getConnection($params);
    return $conn;
};

$di['db_trade'] = function ($di) {
    $params = array(
        'driver'   => 'pdo_' . $di['config']['db_trade']['adapter'],
        'host'     => $di['config']['db_trade']['host'],
        'port'     => $di['config']['db_trade']['port'],
        'user'     => $di['config']['db_trade']['user'],
        'password' => $di['config']['db_trade']['pass'],
        'dbname'   => $di['config']['db_trade']['db'],
        'charset'  => $di['config']['db_trade']['charset'],
    );
    $conn = DriverManager::getConnection($params);
    return $conn;
};

$di['db_account'] = function ($di) {
    $params = array(
        'driver'   => 'pdo_' . $di['config']['db_account']['adapter'],
        'host'     => $di['config']['db_account']['host'],
        'port'     => $di['config']['db_account']['port'],
        'user'     => $di['config']['db_account']['user'],
        'password' => $di['config']['db_account']['pass'],
        'dbname'   => $di['config']['db_account']['db'],
        'charset'  => $di['config']['db_account']['charset'],
    );
    $conn = DriverManager::getConnection($params);
    return $conn;
};

$di['redis'] = function ($di) {
    $redis = new \Redis();
    $redis->connect($di['config']['redis']['host'], $di['config']['redis']['port']);
    return $redis;
};


// IP 过滤
if (!in_array(getIpAddress(), explode(',', $di['config']['ip_allow']))) {
    die('ip is not allowed');
}


// 协议分发
switch ($protocol) {
    case 'soap':
        $controller_class = '\\Xt\\Rpc\\Services\\' . $app . '\\' . ucfirst($controller) . 'Service';
        $service = new SoapServer(
            null,
            array('uri' => 'http://' . $_SERVER['HTTP_HOST'] . "/$app/$version/soap/" . $controller . 'Service')
        );
        $service->setClass($controller_class, $di);
        $service->handle();
        break;

    case 'yar':
        $controller_class = '\\Xt\\Rpc\\Services\\' . $app . '\\' . ucfirst($controller) . 'Service';
        $service = new Yar_Server(new $controller_class($di));
        $service->handle();
        break;

    default:
        $controller_class = '\\Xt\\Rpc\\Controllers\\' . ucfirst($controller) . 'Controller';
        $controller = new $controller_class($di);
        $response = $controller->$action();
        $di['response']->setContent(json_encode($response, JSON_UNESCAPED_UNICODE))->send();
}