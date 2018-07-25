<?php

namespace src;

use phpspider\core\requests;

class SimulateLanding
{
    public static function landing()
    {
        $login_url = "http://kc.zj.com/my/login.php";

        $params = array(
            'ref' => '',
            'username' => 'zhanglei',
            'password' => '123456',
            'loginsubmit' => '%BD%F8%C8%EB%B6%A9%B2%CD%D6%D0%D0%C4'
        );

        requests::post($login_url, $params);

        $cookies = requests::get_cookies('kc.zj.com');

        return $cookies['dc_auth'];

//        $url = "http://kc.zj.com/my/user_select_new.php";
//        $html = requests::get($url);
//        echo $html;

    }
}
