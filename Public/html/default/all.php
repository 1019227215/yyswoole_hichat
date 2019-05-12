<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo " 你好!欢迎来到 {$server->header['host']} 网站！"; ?></title>
    <link rel="stylesheet" type="text/css"  href="http://<?php echo $server->header['host'];?>/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css"  href="http://<?php echo $server->header['host'];?>/css/YYS.css"/>
    <meta name="baidu_union_verify" content="cfdfbc8059ade893283623ba19967cee">
</head>
<body>
<div class="header">
    <?php echo " 你好! <a href='http://www.ip138.com' target='_blank'>{$server->server['remote_addr']}</a> 欢迎来到 <a href='http://{$server->header['host']}/index.html' target='_blank'>{$server->header['host']}</a> 网站！"; ?>
</div>
<div class="body">
    <?php echo $server->textcontent; ?>
</div>
<div class="footer">
    <p>
        备案信息：<a href="http://www.miitbeian.gov.cn" target="_blank"><?php echo "{$server->header['filing']}"; ?></a>
    </p>
</div>
</body>
</html>
