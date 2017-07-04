
<div style="clear:both;"></div>

<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 账户信息</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">我的订单</a></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">账户信息</a></dd>
                <dd><b>.</b><a href="">账户余额</a></dd>
                <dd><b>.</b><a href="">消费记录</a></dd>
                <dd><b>.</b><a href="">我的积分</a></dd>
                <dd><b>.</b><a href="">收货地址</a></dd>
            </dl>

            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">返修/退换货</a></dd>
                <dd><b>.</b><a href="">取消订单记录</a></dd>
                <dd><b>.</b><a href="">我的投诉</a></dd>
            </dl>
        </div>
    </div>
    <!-- 左侧导航菜单 end -->

    <!-- 右侧内容区域 start -->
    <div class="content fl ml10">
        <div class="user_hd">
            <h3>账户信息</h3>
        </div>

        <div class="user_bd mt10">
            <form action="" method="post">
                <ul>
                    <li>
                        <label for="">用户名：</label>
                        <strong><?=Yii::$app->user->identity->username?></strong>
                    </li>
                    <li>
                        <label for="">昵称：</label>
                        <input type="text" class="txt" value="<?=Yii::$app->user->identity->username?>"/>
                    </li>
                    <li>
                        <label for="">邮箱：</label>
                        <strong><?=Yii::$app->user->identity->email?></strong>
                    </li>
                    <li>
                        <label for="">手机号码：</label>
                        <strong><?=Yii::$app->user->identity->tel?></strong>
                    </li>
                    <li>
                        <label for="">&nbsp;</label>
                        <input type="submit" value="提交" class="sbtn" />
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->

<div style="clear:both;"></div>