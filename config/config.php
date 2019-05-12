<?php
/**
 * Created by PhpStorm.
 * User: yyswoole
 * Date: 2018/11/19
 * Time: 18:34
 */

return array(

    '127.0.0.1' => [
        'aaa.com' => [
            'mysql' => 'db',
            'redis' => 'db',
            'itemdir' => S_ROOT . '/YYS/',
            'filing' => '备案号',
        ],
    ],

    'render' => [

        //静态文件目录名，位于public下
        'static_url' => 'html',

        //静态文件格式
        'static' => ['html', 'txt', 'ico', 'js', 'map', 'css', 'png', 'jpg', 'gif', 'otf', 'fon', 'font', 'ttc', 'eot', 'svg', 'ttf', 'woff', 'woff2'],

        //动态文件格式
        'dynamic' => ['html', 'php', 'htm'],

        //目录权限配置
        'safety' => ['chroot' => S_ROOT, 'group' => 'www', 'user' => 'www',],

        //干扰码
        'ucode' => '5@YlcP^P*B3#XY@6',

    ],

    'chat' => [
        'domain' => 'ws://www.aaa.com:81',
    ],

);