<?php
/**
 * Created by PhpStorm.
 * User: zhangyu
 * Date: 2018/11/19
 * Time: 18:34
 */

return array(

    'mysql' => [
        'db' => [
            'hosts' => 'mysql:host=127.0.0.1;dbname=timo;port=3306',
            'username' => 'root',
            'password' => 'Thyme123',
            'options' => [
                'charset' => 'utf8',
            ]
        ]
    ],

    'redis' => [
        'db' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => ''
        ]
    ],

);