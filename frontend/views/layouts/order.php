<?php
use yii\helpers\Html;
\frontend\assets\GoodsAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <?php
                if(Yii::$app->user->isGuest){
                    echo ' <li>您好，欢迎来到京西！[<a href="http://www.yii2shop.com/user/login.html">登录</a>] [<a href="http://www.yii2shop.com/user/register.html">免费注册</a>] </li>';
                }else{
                    echo ' <li>您好，欢迎来到京西！';
                    echo Yii::$app->user->identity->username;
                    echo ' || [<a href="http://www.yii2shop.com/user/logout.html"> 注销 </a> ] </li>';
                }
                ?>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><?=Html::img('@web/images/logo.png')?></a></h2>
        <div class="flow fr flow2">
            <ul>
                <li>1.我的购物车</li>
                <li class="cur">2.填写核对订单信息</li>
                <li>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->
<!-- 导航条部分 start -->
<div class="nav w1210 bc mt10">
    <!--  商品分类部分 start-->
    <div class="category fl cat1"> <!-- 非首页，需要添加cat1类 -->
        <div class="cat_hd off">  <!-- 注意，首页在此div上只需要添加cat_hd类，非首页，默认收缩分类时添加上off类，鼠标滑过时展开菜单则将off类换成on类 -->
            <h2>全部商品分类</h2>
            <em></em>
        </div>

        <div class="cat_bd none" >
            <!--      我的分类-->
            <?php
            //一级分类，遍历
            foreach (\frontend\models\GoodsCategory::find()->asArray()->where(['parent_id'=>0])->all() as $item1) {
                echo '<div class="cat" >';
                echo "<h3><a href=''>".$item1['name']."</a> <b></b></h3>";
                echo '<div class="cat_detail">';
                //二级分类，遍历
                if(\frontend\models\GoodsCategory::find()->asArray()->where(['parent_id'=>$item1['id']])){
                    foreach(\frontend\models\GoodsCategory::find()->asArray()->where(['parent_id'=>$item1['id']])->all() as $item2){
                        echo ' <dl >';
                        echo '<dt><a href="">'.$item2['name']."</a></dt>";
                        echo '<dd>';
                        //此处添加三级分类
                        foreach(\frontend\models\GoodsCategory::find()->asArray()->where(['parent_id'=>$item2['id']])->all() as $item3){
                            echo '<a href="">'.$item3['name'].'</a>';
                        }
                        echo '</dd>';
                        echo '</dl>';
                    }
                }
                echo '</div></div>';
            }
            ?>
        </div>
    </div>
    <!--  商品分类部分 end-->


    <div class="navitems fl">
        <ul class="fl">
            <li class="current"><a href="http://www.yii2shop.com/goods/index.html">首页</a></li>
            <li><a href="">电脑频道</a></li>
            <li><a href="">家用电器</a></li>
            <li><a href="">品牌大全</a></li>
            <li><a href="">团购</a></li>
            <li><a href="">积分商城</a></li>
            <li><a href="">夺宝奇兵</a></li>
        </ul>
        <div class="right_corner fl"></div>
    </div>
</div>
<!-- 导航条部分 end -->
</div>
<!-- 头部 end-->

<!--内容详情-->

<div style="clear:both;"></div>
<?=$content?>

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><?=Html::img('@web/images/xin.png')?></a>
        <a href=""><?=Html::img('@web/images/kexin.jpg')?></a>
        <a href=""><?=Html::img('@web/images/police.jpg')?></a>
        <a href=""><?=Html::img('@web/images/beian.gif')?></a>
    </p>
</div>
<!-- 底部版权 end -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
