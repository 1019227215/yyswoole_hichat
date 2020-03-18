<?php

namespace YYs\controller\temp;

use components\Controller;

class IndexController extends Controller
{

    /**
     * 模版页面
     * @return string
     */
    public function actionIndex()
    {
        return self::renderView("temp/report/index.php", ['data' => [1, 2, 3, 4], 'test' => 'adqwasd']);
        //return json_encode(self::$server);
    }

}