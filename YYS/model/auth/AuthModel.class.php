<?php


namespace YYS\model\auth;

use YYS\model\Model;

class AuthModel extends Model
{

    //默认用户
    private $userinfo = [
        'YYS' => ['pwd' => '6a608576a0575326a0c40b30b83cec09', 'alias' => '管理员', 'register_time' => '1557219723', 'friend_list ' => ['weige', 'nana']],
        'weige' => ['pwd' => '3b2bbb1c59c6a4915f64c5f0efc15084', 'alias' => '老板', 'register_time' => '1557218733', 'friend_list ' => ['YYS', 'nana']],
        'nana' => ['pwd' => '27fd73349a5dd82e71c62928c366ba49', 'alias' => '后勤', 'register_time' => '1557217713', 'friend_list ' => ['weige', 'YYS']],
    ];

    /**
     * 退出用戶
     * @param $rq
     * @return array
     */
    public function loginOut($rq)
    {

        $redis = Model::iniRedis($this->dom, $dbname = 'db', $type = 'redis', array('db_id' => 0));
        if ($redis->hdel("user", $rq['user'])) {
            $rus = self::getResult(4, '已退出：' . $rq['user']);
        } else {
            $rus = self::getResult(4, '退出失敗：' . $rq['user']);
        }

        return $rus;
    }

    /**
     * 登录
     * @param $rq
     * @param $ws
     * @return array
     */
    public function loginUser($rq)
    {
        $rus = self::checkUser($rq);
        if ($rus === true) {

            $redis = Model::iniRedis($this->dom, $dbname = 'db', $type = 'redis', array('db_id' => 0));
            if (!$redis->hExists("user", $rq['user'])) {

                $token = md5(md5($rq['user'] . $this->tims) . md5($rq['pwd'] . $this->userinfo[$rq['user']]['register_time']));

                $redis->hSet('user', $rq['user'], json_encode(['user' => $rq['user'], 'token' => $token]));
                $rus = self::getResult(1, '登录成功！', ['url' => config['chat']['domain'] . "?user={$rq['user']}&token=" . $token]);
            } else {

                $user = json_decode($redis->hGet("user", $rq['user']), true);
                var_dump($user);
                if (isset($user['fd'])) {

                    $rus = self::getResult(4, '调皮哦！');
                } else {

                    $rus = self::getResult(1, '登录成功！', ['url' => config['chat']['domain'] . "?user={$rq['user']}&token=" . $user['token']]);
                }
            }
        }

        return $rus;
    }

    /**
     * 返回用户名
     * @param $u
     * @return array
     */
    public function getUserName($u)
    {

        return ['initial' => $u, 'static' => md5(config['render']['ucode'] . $u), 'dynamic' => md5(config['render']['ucode'] . $u . $this->tims)];
    }

    /**
     * 掌门信息验证
     * @param $u
     * @param $p
     * @return bool|string
     */
    public function checkUser($u)
    {

        if (isset($this->userinfo[$u['user']])) {

            $pwd = md5($u['pwd'] . $this->userinfo[$u['user']]['register_time']);
            if ($this->userinfo[$u['user']]['pwd'] == $pwd) {

                /*$checkPwd = self::checkPwdStrength($p['pwd']);
                if ($checkPwd === true){

                    return true;
                }else{

                    return $checkPwd;
                }*/
                return true;
            } else {

                //设置黑名单
                self::operationBlackList(self::getUserName($u['user'])['static']);
                return '密码错误！';
            }
        } else {

            return '掌门不存在！';
        }
    }

    /**
     * 黑名单操作
     * @param $k
     * @param $v
     * @param string $i
     * @return mixed
     */
    private function operationBlackList($k, $v = 1, $i = 'BlackList')
    {

        $redis = Model::iniRedis($this->dom, $dbname = 'db', $type = 'redis', array('db_id' => 0));
        if ($v != 0) {

            if ($redis->HEXISTS($i, $k)) {

                $rust = $redis->HINCRBY($i, $k, $v);
            } else {

                $rust = $redis->HSET($i, $k, $v);
            }

        } else {

            $rust = $redis->HDEL($i, $k);
        }

        return $rust;
    }

    /**
     * 密码强度判断
     * @param $pwd
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    private function checkPwdStrength($pwd)
    {
        //密码强度判断
        if (preg_match_all("/^(?:([a-z])|([A-Z])|([0-9])|(.)){6,16}$/", $pwd, $arr)) {

            $pwds = [];
            array_walk_recursive($arr, function ($value) use (&$pwds) {

                array_push($pwds, $value);
            });

            if (count(array_filter($pwds)) != 5) {

                return "密码请包含：大小写字母、阿拉伯数字、英文符号！";
            } else {

                return true;
            }
        } else {

            return "密码长度请在6-16位！";
        }

    }

    /**
     * 返回结果
     * @param $c
     * @param $m
     * @param array $d
     * @return array
     */
    private function getResult($c, $m, $d = [])
    {

        return ['c' => $c, 'm' => $m, 'data' => $d];
    }

}
