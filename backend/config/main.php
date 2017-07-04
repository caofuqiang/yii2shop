<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
    'modules' => [
        'admin' => [
        'class' => 'app\modules\admin\Admin',
            ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'ywfdsa7829347898fLXfsCfdKjfO-fss0',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',

        ],

        'user' => [
            'identityClass' => \backend\models\User::className(),
            'enableAutoLogin' => true,
           // 'idParam' => '__check',
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl'=>['user/login'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],

        ],
        'qiniu' => [
            'class'=>\backend\components\Qiniu::className(),
            'up_host'=>'http://up-z2.qiniu.com',
            'accessKey'=>'FMhQxlxnpsZIwdsiURFtiswmxKRxw6AXruxdmzFe',
            'secretKey'=>'TcZSp3v058c4HEgY6S8VngtrQZa_eOKi9deZ9esS',
            'bucket'=>'cfqphp0217',
            'domain'=>'http://or9t6ktsf.bkt.clouddn.com/',
        ]

    ],
    'params' => $params,
];
