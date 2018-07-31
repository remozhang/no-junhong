<?php

require_once "vendor/autoload.php";

use src\SimulateLanding;
use phpspider\core\phpspider;
use phpspider\core\requests;

/* Do NOT delete this comment */
/* 不要删除这段注释 */
$cookie = SimulateLanding::landing();

$config = array(
    'name'                => 'no-junhong',
    'log_show'            => true,
    'log_file'            => 'data/log/download.log',
    'input_encoding'      => 'GB2312',
    'output_encoding'     => 'UTF-8',
    'user_agent'          => phpspider::AGENT_PC,
    'domains'             => array(
        'kc.zj.com'
    ),
    'scan_urls'           => array(
        'http://kc.zj.com/my/user_select_new.php'
    ),
    'content_url_regexes' => array(
        "\?_client=.{110}"
    ),
    'fields'              => array(),
    'page_path'           => realpath(__DIR__).'/data/page/',
);

$spider = new phpspider($config);

$spider->on_scan_page = function($page, $content, $phpspider) {
    // 当前订餐页面月份
    preg_match('/(\d{1,2})月份个人订餐/', $page['raw'], $month);
    $month = $month['1'];

    // 当前订餐页面月份为当前月份时， 将下个月的订餐页面作为抓取对象
    if ($month == date('m')) {
        if (preg_match('/href="user_select_new\.php\?_client=(.{98})">下月/U', $page['raw'], $matches)) {
            $nextUrl = 'http://kc.zj.com/my/user_select_new.php?_client=' . $matches['1'];
            file_put_contents('data/log/url.log', $nextUrl, FILE_APPEND);
            $phpspider->add_scan_url($nextUrl);
        }
    }

};

$spider->on_fetch_url = function ($url, $phpspider) {
    if (preg_match('/http:\/\/kc\.zj\.com\/my\/\?_client=.{110}/', $url)) {
        $url = preg_replace('/(http:\/\/kc\.zj\.com\/my\/)/', 'http://kc.zj.com/my/user_select_new.php', $url);

        return $url;
    }

    return false;
};

$spider->on_content_page = function ($page, $content, $phpspider) {
    $time = preg_match('/<input type="hidden" name="shop(\d+)" value="1833"/U', $content, $match);
    if ($time == '1') {
        preg_match('/<input type="hidden" name="_client" value="(.+)">/U', $content, $client);

        $_client = $client[1];

        $params = array(
            '_client'        => $_client,
            'pid'            => $match[1],
            'shop'.$match[1] => '1833'
        );

        requests::post('http://kc.zj.com/my/user_select_new.php', $params);
    }

    return false;
};

$spider->start();