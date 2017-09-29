<?php
$partyHistory = $_SERVER['HTTP_HOST'];
return array(
    /* 网站配置 */
    'SITE_INFO' => array(
        'name' => '中共党史经典文献资料库',
        'version' => 'V1.0',
        'USERS_LOGIN_ON' => '1'

    ),
    /*url模式*/
    'URL_MODEL' => '2',

    'DEFAULT_MODULE' => 'Home', //默认,访问模块（可以不出现在URL地址
    'MODULE_DENY_LIST' => array('Runtime', 'Common'), // 设置禁止访问的模块列
    'URL_CASE_INSENSITIVE' => true, //url不区分大小写

    /*分页的配置*/
    'PAGESIZE' => 20,
    'PAGENUM' => 10,

    /*xunsearch项目名配置*/
    'SEARCH' => array(
        'PARTYLITERATURE' => 'partyliterature',
    ),
    'RESOURCE' => array(
        'PDF' =>$_SERVER['DOCUMENT_ROOT'] . '/Resource/GUID/',
        'READPDF' =>$_SERVER['HTTP_HOST'] . '/Resource/GUID/',
    ),

    'TMPL_PARSE_STRING'  =>array(
        '__RESOURCE__'=>'/Resource/GUID/', // 更改默认的/Public 替换规则
        '__ROOT__'=>$_SERVER['HTTP_HOST']
    ),

    /*数据过滤*/
    'DEFAULT_FILTER' => 'htmlspecialchars',

    /*url优化*/


    'LISTNUM' => array('DEFAULT_THEME' => 'default'),
    'TOKEN' => array(
        'member_marked' => 'xbtsg',
        'member_timeout' => '86400'
    ),
    'AUTH_CODE' => 'o1qnYB',

    /* 数据库配置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'partyhistory', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'lhh2012', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'tb_', // 数据库表前缀

);

