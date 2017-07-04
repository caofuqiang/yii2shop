<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [//css样式
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/login.css',
        'style/footer.css',
        'style/user.css'
    ];
    public $js = [
        'js/jquery-1.8.3.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
