<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
    /*    'cache'=>[
           'class' => 'system.caching.CFileCache',
            'directoryLevel' => 2,
        ],*/
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'loginUrl' => ['user/login'],//默认跳转页
            'identityClass' => frontend\models\Member::className(),//实现接口的类
            'enableAutoLogin' => true,//基于cookie自动登录
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [//地址美化
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix'=>'.html',
            'rules' => [
            ],
        ],
        //配置短信组件
        'sms'=>[
                'class' => \frontend\components\Sms::className(),
                'app_key'=>'24480039',
                'app_secret'=>'95361f2efb93678916d71074b4237a05',
                'sign_name'=>'曹富强商品网站',
                'template_code'=>'SMS_71510232',
        ],

    ],
    'params' => $params,
];
