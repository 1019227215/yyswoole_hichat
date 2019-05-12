<?php
/**
 * Created by PhpStorm.
 * User: zhangyu
 * Date: 2018/11/16
 * Time: 10:55
 */

namespace components;

use components\Tool;

class Run
{

    /**
     * 初始化控制器
     * 根据路径斜杠拆分取得路径
     */
    public static function run($server, $response, $swoole)
    {
        Tool::setFile(LogDir . "Run.log", json_encode($server));//记录每次请求日志

        Tool::getFiling($server);//获取备案信息
        $url = trim($server->server['request_uri'], '/');//去掉前后斜杠
        $url = explode('?', $url);//以问号拆分找地址
        $suffix = explode('.', $url[0]);//以点拆分找后缀
        $fixs = end($suffix);

        //静态文件检查输出
        if (isset($fixs) && in_array($fixs, config['render']['static'])) {

            return self::render(Puc . $url[0], ['server' => $server], false);

        }

        //动态文件检查输出
        if (isset($fixs) && in_array($fixs, config['render']['dynamic'])) {

            return self::dynamic($suffix, $server, $response, $swoole);
        } else {

            $server->textcontent = "您好！正在全力开放中！";//"Error! Suffix not found!";
            return self::render(Puc . 'html/default/all.php', ['server' => $server]);
        }
    }

    /**
     * 渲染动态文件
     * @param $suffix
     * @param $server
     * @param $response
     * @param $swoole
     * @return string
     */
    private static function dynamic($suffix, $server, $response, $swoole)
    {
        $route = Tool::handle(explode('/', $suffix[0]));//以斜杠拆分找路径,同时去除特殊符号
        $route = array_filter($route);//去除空值
        $dom = explode('.', $server->header['host']);
        $dom = $dom[count($dom) - 2];
        $dom = is_dir($dom) ? $dom : "YYS";

        if (empty($route)) {

            $server->textcontent = "您好！解释器正在全力开放中！";//"Error! The interpreter does not exist!";
            return self::render(Puc . 'html/default/all.php', ['server' => $server]);
        }

        $ide = (count($route) > 1) ? array_pop($route) : end($route);//得到最后一个元素
        $index = Di . ucfirst($ide);//得到方法名
        $filename = array_pop($route);//得到最后一个元素
        $url = empty($route) ? '' : implode('\\', $route) . '\\';//拼装地址
        $controller = ucfirst($filename) . ucfirst(Cl);//控制器名称
        $conter = '\\' . $dom . '\\' . Cl . '\\' . $url . $controller;//拼装文件地址
        $file = str_replace('\\', '/', $conter) . '.class.php';//控制地址

        if (file_exists($file)) {

            $ado = new $conter($server, $response, $swoole);//初始化
            $server->textcontent = "您好！{$ide}正在全力开放中！";//"Error! {$ide} not found!";
            return method_exists($ado, $index) ? $ado->$index() : self::render(Puc . 'html/default/all.php', ['server' => $server]);//"Error! {$ide} not found!";执行方法
        } else {

            $server->textcontent = "您好！{$controller}正在全力开放中！";//"Error! {$controller} not found!";
            return self::render(Puc . 'html/default/all.php', ['server' => $server]);
        }

    }

    /**
     * 渲染视图
     * @param $_viewFile_
     * @param null $_data_
     * @param bool $_return_
     * @return string
     */
    public static function render($_viewFile_, $_data_ = null, $_return_ = true)
    {
        if (file_exists($_viewFile_)) {

            //将我们render的参数数组extract为本地变量
            if ($_data_ = &$_data_ && is_array($_data_)) {

                extract($_data_, EXTR_OVERWRITE);
            }

            if ($_return_) {

                //开启缓存输出
                ob_start();
                ob_implicit_flush(false);
                require($_viewFile_);

                return ob_get_clean();
            } else {

                //直接输出
                return file_get_contents($_viewFile_);
            }
        } else {

            $_data_['server']->textcontent = "您好！{$_viewFile_}正在全力开放中！";
            return self::render(Puc . 'html/default/all.php', $_data_);//"Error! {$_viewFile_} not found!";
        }
    }

    /**
     * 获取默认配置
     * @return string
     */
    public static function getDefault()
    {
        Tool::setConfig('main', true);
        Tool::setConfig('config', true);
        self::setDefine();//加载默认常量
    }

    /**
     * 自动加载类
     */
    public static function loaders()
    {
        spl_autoload_register(function ($className) {

            $file = str_replace('\\', '/', $className) . '.class.php';

            if (file_exists($file)) {

                include_once $file;
            } else {

                return "Error! {$className} Content cannot be processed!";
            }
        });
    }

    /**
     * set 默认常量
     */
    public static function setDefine()
    {

        $ip = array_values(swoole_get_local_ip())[0];
        defined('Ips') ? "" : define('Ips', $ip);
        define('Cl', 'controller');
        define('Di', 'action');
        define('LogDir', '/log/');
        define('UpDir', '/UpDir/');
        define('Puc', '/Public/');
        define('View', '/view/');
        define('Model', '/model/');
        define('Controller', '/controller/');

        //权限设置
        define('chroot', config['render']['safety']['chroot'] ?? S_ROOT);
        define('user', config['render']['safety']['user'] ?? 'root');
        define('group', config['render']['safety']['group'] ?? 'root');
    }
}