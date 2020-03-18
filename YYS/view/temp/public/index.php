<?php
include_once(__DIR__."/head.php");
?>

<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<!-- preloader area start -->
<div id="preloader">
    <div class="loader"></div>
</div>
<!-- preloader area end -->
<!-- page container area start -->
<div class="page-container">
    <!-- sidebar menu area start -->
    <?php
    include_once(__DIR__."/left.php");
    ?>
    <!-- sidebar menu area end -->
    <!-- main content area start -->
    <div class="main-content">
        <!-- header area start -->
        <?php
        include_once(__DIR__."/top.php");
        ?>
        <!-- page title area end -->
        <?php
        include_once($url);
        ?>
    </div>
    <!-- main content area end -->
    <!-- footer area start-->
    <?php
    include_once(__DIR__."/footer.php");
    ?>
    <!-- footer area end-->
</div>
<!-- page container area end -->
<!-- offset area start -->
<?php
include_once(__DIR__."/bottom.php");
?>
<!-- offset area end -->
<!-- jquery latest version -->
<?php
include_once(__DIR__."/tail.php");
?>
</body>

</html>
