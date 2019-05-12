<?php

define('S_ROOT', __DIR__);
ini_set('memory_limit', '-1');

use components\Redisdb;

class Chat
{
    const HOST = '0.0.0.0';//ip地址 0.0.0.0代表接受所有ip的访问
    const PART = 81;//端口号
    //private $server = null;//单例存放websocket_server对象
    private $connectList = [];//客户端的id集合
    private $config = [];
    private $main = [];
    private $ip = '';

    public function __construct()
    {
        include_once 'components/Redisdb.class.php';
        $this->config = include_once "config/config.php";
        $this->main = include_once "config/main.php";
        $this->ip = array_values(swoole_get_local_ip())[0];

        //实例化swoole_websocket_server并存储在我们Chat类中的属性上，达到单例的设计
        $http = new swoole_websocket_server(self::HOST, self::PART);

        //监听连接事件
        $http->on('open', function ($server, $request) use ($http) {

            //var_dump($server, 1111, $request, 2222, preg_match('/\w*\.\w*$/', explode(':', $request->header['host'])[0], $_hosts), $_hosts,get_class_methods(get_class($server)),get_class($server));

            if (isset($request->get['token']) && isset($request->get['user'])) {

                preg_match('/\w*\.\w*$/', explode(':', $request->header['host'])[0], $_hosts);
                $redis = $this->sRedis($_hosts[0]);

                if ($redis->hExists("user", $request->get['user'])) {

                    $user = json_decode($redis->hGet('user', $request->get['user']), true);
                    if (isset($user['fd'])){

                        $server->push($request->fd, json_encode(['no' => "系统提示", 'msg' => '真调皮！']));
                        $server->close($request->fd);
                    }

                    if ($user['token'] == $request->get['token']) {

                        $user['fd'] = $request->fd;
                        $redis->hSet('suser', $request->fd, $request->get['user']);
                        $redis->hSet('user', $user['user'], json_encode($user));
                        echo $user['user'] . '连接了' . $_hosts[0] . PHP_EOL;//打印到我们终端
                        $this->connectList[$request->fd] = $_hosts[0];//将请求对象上的fd，也就是客户端的唯一标识，可以把它理解为客户端id，存入集合中

                        foreach ($this->connectList as $ks => $fd) {//遍历客户端的集合，拿到每个在线的客户端id

                            //将客户端发来的消息，推送给所有用户，也可以叫广播给所有在线客户端
                            $server->push($ks, json_encode(['no' => "系统提示", 'msg' => "{$user['user']}上线了！"]));
                        }

                    } else {

                        $server->push($request->fd, json_encode(['no' => "系统提示", 'msg' => 'token错误！']));
                        $server->close($request->fd);
                    }
                } else {

                    $server->push($request->fd, json_encode(['no' => "系统提示", 'msg' => '你有点皮！']));
                    $server->close($request->fd);
                }
            } else {

                $server->push($request->fd, json_encode(['no' => "系统提示", 'msg' => '请去登录！']));
                $server->close($request->fd);
            }
        });

        //监听接收消息事件
        $http->on('message', function ($server, $frame) {

            if (isset($this->connectList[$frame->fd])) {

                $redis = $this->sRedis($this->connectList[$frame->fd]);
                $token = $redis->hGet('suser', $frame->fd);
                $user = json_decode($redis->hGet('user', $token), true);

                echo $user['user'] . '来了，说：' . $frame->data . PHP_EOL;//打印到我们终端
                //将这个用户的信息存入集合
                foreach ($this->connectList as $ks => $fd) {//遍历客户端的集合，拿到每个在线的客户端id

                    //将客户端发来的消息，推送给所有用户，也可以叫广播给所有在线客户端
                    $server->push($ks, json_encode(['no' => $user['user'], 'msg' => $frame->data]));
                }
            } else {
                $server->close($frame->fd);
            }
        });

        //监听关闭事件
        $http->on('close', function ($server, $fd) use ($http) {

            if (isset($this->connectList[$fd])) {
                $redis = $this->sRedis($this->connectList[$fd]);
                $token = $redis->hGet('suser', $fd);
                $user = json_decode($redis->hGet('user', $token), true);
                $redis->hdel('suser', $fd);
                $redis->hdel('user', $token);

                echo $user['user'] . '走了' . PHP_EOL;//打印到我们终端
                unset($this->connectList[$fd]);//将断开了的客户端id，清除出集合

                //将这个用户的信息存入集合
                foreach ($this->connectList as $ks => $fd) {//遍历客户端的集合，拿到每个在线的客户端id

                    //将客户端发来的消息，推送给所有用户，也可以叫广播给所有在线客户端
                    $server->push($ks, json_encode(['no' => '系统提示', 'msg' => "{$user['user']}下线！"]));
                }

            }

            $server->close($fd);

        });

        //开启服务
        $http->start();
    }

    /**
     * 设置redis
     * @param $dom
     * @param array $auth
     * @return Redisdb
     */
    private function sRedis($dom, $auth = ['db_id' => 0])
    {
        $dbname = $dbname ?? $this->config[$this->ip][$dom]['redis'] ?? 'db';
        $conter = $this->main['redis'][$dbname];
        return new Redisdb($conter, $auth);
    }

}

$obj = new Chat();