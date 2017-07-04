<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle{


    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [//css样式
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/bottomnav.css',
        'style/footer.css',
        'style/index.css',
    ];

    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/header.js',
        'js/index.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
