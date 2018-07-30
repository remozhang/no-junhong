<?php

require_once "vendor/autoload.php";

use src\SimulateLanding;
use phpspider\core\phpspider;
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

    $time = preg_match('/<input type="hidden" name="shop(\d+)" value="1833"/U', $content, $match);
    if ($time == '1') {
        preg_match('/<input type="hidden" name="_client" value="(.+)">/U', $content, $client);

        $_client = $client[1];

        $params = array(
            '_client' => $_client,
            'pid' => $match[1],
            'shop'. $match[1] => '1833'
        );

        requests::post('http://kc.zj.com/my/user_select_new.php', $params);
    }

    return false;
};

$spider->start();