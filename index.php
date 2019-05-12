<?php

/**
 * 入口文件
 * Created by PhpStorm.
 * User: zhangyu
 * Date: 2018/11/12
 * Time: 11:11
 */
//date_default_timezone_set('Asia/Shanghai');//('America/Los_Angeles');
define('S_ROOT', __DIR__);
ini_set('memory_limit', '-1');
declare (ticks=1);

use components\Run;

try {

    include_once S_ROOT . "/components/Run.class.php";

    Run::loaders();//自动加载类
    Run::getDefault();//加载main为常量

    $http = new swoole_http_server('0.0.0.0', 80);

    $http->on("start", function ($server) {

        echo "Swoole http server is started at http://" . $server->host . ":" . $server->port . PHP_EOL;
    });

    $http->set(array(

        //'log_file' => LogDir . 'swoole.log',//错误日志
        //'daemonize' => true,//开启后普通日志写入错误日志里
        //'document_root' => S_ROOT . Puc,//静态html文件目录
        //'enable_static_handler' => true,//开启静态优先访问
        //'SW_AIO_MAX_FILESIZE' => 10,//最大读写数据

        'upload_tmp_dir' => S_ROOT . UpDir,//上传文件存储目录
        'http_compression' => true,//开启压缩输出
        'chroot' => chroot,
        'user' => user,
        'group' => group,

    ));

    $http->on("request", function ($request, $response) use ($http) {

        if (isset($request->get['reload']) && $request->get['reload'] == "yes") {

            if (user != 'root') {
                swoole_async::exec("kill -USR2 {$http->worker_pid}", function ($result, $status) {

                    exit;
                });
            } else {
                $http->reload(true);
            }
        }

        $data = Run::run($request, $response, $http);
        $data = is_string($data) ? $data : json_encode($data);
        $response->end($data);

    });

    $http->start();

} catch (Exception $e) {

    echo $e->getMessage() . PHP_EOL;

    exit;
}


