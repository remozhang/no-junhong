<?php

require_once "vendor/autoload.php";

use src\SimulateLanding;
use phpspider\core\phpspider;
use phpspider\core\selector;
use phpspider\core\log;
use phpspider\core\requests;

/* Do NOT delete this comment */
/* 不要删除这段注释 */
$cookie = SimulateLanding::landing();

$config = array(
    'name' => 'no-junhong',
    'log_show' => true,
    'log_file' => 'data/log/download.log',
    'input_encoding' => 'GB2312',
    'output_encoding' => 'UTF-8',
    'user_agent' => phpspider::AGENT_PC,
    'domains' => array(
        'kc.zj.com'
    ),
    'scan_urls' => array(
        'http://kc.zj.com/my/user_select_new.php'
    ),
    'content_url_regexes' => array(
        "\?_client=.{110}"
    ),
    'fields' => array(

    ),
    'page_path' => realpath(__DIR__) . '/data/page/',
);

$spider = new phpspider($config);

$spider->on_fetch_url = function ($url, $phpspider) {
    if (preg_match('/http:\/\/kc\.zj\.com\/my\/\?_client=.{110}/', $url)) {
        $url = preg_replace(
            '/(http:\/\/kc\.zj\.com\/my\/)/',
            'http://kc.zj.com/my/user_select_new.php',
            $url
        );

        return $url;
    }

    return false;
};

$spider->on_content_page = function ($page, $content, $phpspider)
{

    // FIXME 失败了 无法得知_client的参数
    $time = preg_match('/<input type="hidden" name="shop(\d+)" value="1833"/U', $content, $match);
    if ($time == '1') {
        preg_match('/<input type="hidden" name="_client" value="(.+)">/U', $content, $client);

        $_client = $client[1];

        $params = array(
            '_client' => $_client,
            'pid' => $match[1],
            'shop'. $match[1] => '1833'
        );


//        file_put_contents('data/log/url.log', SimulateLanding::landing()."\n", 8);

        $response = requests::post('http://kc.zj.com/my/user_select_new.php', $params);

        file_put_contents('data/log/url.log', $response . "\n", 8);

    }

    return false;
};

$spider->start();