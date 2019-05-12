<?php
/**
 * Created by PhpStorm.
 * User: zhangyu
 * Date: 2018/11/19
 * Time: 17:26
 */

namespace YYS\model;

use components\Pdodb;
use components\Redisdb;

class Model
{

    public $server = [];
    public $dom = '';
    public $tims = '';


    public function __construct($server)
    {
        $this->tims = time();
        $this->server = $server;
        $this->dom = preg_match('/\w*\.\w*$/', $server->header['host'], $_hosts)[0];
    }

    /**
     * 初始化mysqlpdo
     * @param string $type
     * @param string $dbname
     * @return Pdodb
     */
    public static function iniPdo($dom, $dbname = '', $type = 'mysql')
    {
        $dbname = $dbname ?? config[Ips][$dom][$type] ?? 'db';
        $conter = main[$type][$dbname];

        return new Pdodb($conter['hosts'], $conter['username'], $conter['password'], $conter['options']);
    }

    /**
     * 初始化redis
     * @param string $type
     * @param string $dbname
     * @param array $attr
     * @return Redisdb
     */
    public static function iniRedis($dom, $dbname = '', $type = 'redis', $attr = [])
    {
        $dbname = $dbname ?? config[Ips][$dom][$type] ?? 'db';
        $conter = main[$type][$dbname];

        return new Redisdb($conter, $attr);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

}