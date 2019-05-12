<?php

namespace YYs\controller\auth;

use components\Controller;
use YYS\model\auth\AuthModel;

class IndexController extends Controller
{

    /**
     * 登錄用戶
     * @return false|string
     */
    public function actionCheckuser()
    {
        $amodel = new  AuthModel(self::$server);
        $rmodel = $amodel->loginUser($this->request);
        return json_encode($rmodel);
    }

    /**
     * 退出用戶
     * @return false|string
     */
    public function actionOutuser()
    {
        $amodel = new  AuthModel(self::$server);
        $rmodel = $amodel->loginOut($this->request);
        return json_encode($rmodel);
    }

}