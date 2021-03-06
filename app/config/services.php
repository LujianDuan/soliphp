<?php

use Soli\Db;
use Soli\View;
use Soli\View\Engine\Twig as TwigEngine;
use Soli\View\Engine\Smarty as SmartyEngine;
use Soli\Logger;
use Soli\Session;
use Soli\Session\Flash;

// 将配置信息扔进容器
$di->setShared('config', $config);

// 配置数据库信息, Model中默认获取的数据库连接标志为"db"
// 可使用不同的服务名称设置不同的数据库连接信息，供 Model 中做多库的选择
$di->setShared('db', function () use ($config) {
    return new Db($config['database']);
});

// TwigEngine
$di->setShared('view', function () use ($config) {
    $view = new View();
    $view->setViewsDir($config['application']['viewsDir']);
    $view->setViewExtension('.twig');

    // 通过匿名函数来设置模版引擎，延迟对模版引擎的实例化
    $view->setEngine(function () use ($config, $view) {
        $engine = new TwigEngine($view);
        // 开启 debug 不进行缓存
        //$engine->setDebug(true);
        $engine->setCacheDir($config['application']['cacheDir'] . 'twig');
        return $engine;
    });

    return $view;
});

// 日志记录器
$di->setShared('logger', function () use ($config) {
    $logFile = $config['application']['logsDir']  . date('Ym') . '.log';
    return new Logger($logFile);
});

// 闪存消息
$di->setShared('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

// Session
$di->setShared('session', function () {
    $session = new Session();
    $session->start();

    return $session;
});
