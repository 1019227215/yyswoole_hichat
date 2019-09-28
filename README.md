# yyswoole_hichat
### 基于swoole的及时聊天

#### 演示地址：http://yyshou.com/html/hichat.html 
账号：YYS、test、ces、test2、ces2
密码：123456

#### config里修改外网ip、域名及域名对应项目的默认数据库/redis/项目根目录、聊天地址
```php
'127.0.0.1' => [
     'aaa.com' => [
         'mysql' => 'db',
         'redis' => 'db',
         'itemdir' => S_ROOT . '/YYS/',
         'filing' => '备案号',
     ],
 ],
 'chat' => [
         'domain' => 'ws://www.aaa.com:81',
     ],
```

#### main里修改数据库和redis配置信息
```php
 'mysql' => [
     'db' => [
         'hosts' => 'mysql:host=127.0.0.1;dbname=mysql;port=3306',
         'username' => 'root',
         'password' => 'root',
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

```



#### 启动两个守护进程
```shell
sh swoole-manages restart hichat
sh swoole-manages restart
```

访问网站：http://域名/html/hichat.html
![yys](https://github.com/1019227215/yyswoole_hichat/blob/master/Public/image/l1.png)  
![yys](https://github.com/1019227215/yyswoole_hichat/blob/master/Public/image/l2.png)  
