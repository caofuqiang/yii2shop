<div style="clear:both;"></div>

<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 我的订单</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">我的订单</a></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd><b>.</b><a href="">账户信息</a></dd>
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
        <div class="order_hd">
            <h3>我的订单</h3>
            <dl>
                <dt>便利提醒：</dt>
                <dd>待付款   （<span <?=$status['ready']>0? 'style="color:red"' : '' ;?>><?=$status['ready']?></span>）</dd>
                <dd>待确认收货（<span <?=$status['confirm']>0? 'style="color:red"' : '' ;?>><?=$status['confirm']?></span>）</dd>
                <dd>待自提   （<span <?=$status['ziti']>0? 'style="color:red"' : '' ;?>><?=$status['ziti']?></span>）</dd>
            </dl>

            <dl>
                <dt>特色服务：</dt>
                <dd><a href="">我的预约</a></dd>
                <dd><a href="">夺宝箱</a></dd>
            </dl>
        </div>

        <div class="order_bd mt10">
            <table class="orders">
                <thead>
                <tr>
                    <th width="10%">订单号</th>
                    <th width="20%">订单商品</th>
                    <th width="10%">收货人</th>
                    <th width="20%">订单金额</th>
                    <th width="20%">下单时间</th>
                    <th width="10%">订单状态</th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($goods as $good):?>
                    <tr>
                        <td><a href=""> <?=$good['trade_no']?></a></td>
                        <td><a href=""><?=\yii\helpers\Html::img(Yii::getAlias('http://admin.shop.com'.$good['logo']))?></a></td>
                        <td><?=$good['name']?></td>
                        <td>￥<?=number_format($good['price'],2,'.','')?> <?=$good['payment_name']?></td>
                        <td><?=date('Y-m-d h:i:s',$good['create_time'])?></td>
                        <td class="status"><?=\frontend\models\Order::$status[$good['status']]?></td>
                        <!--（0已取消1待付款2待发货3待收货4完成）-->
                        <td><a href="">查看</a> | <a href="">删除</a></td>
                    </tr>
                <?php endforeach;?>

                </tbody>
            </table>
        </div>
    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->

<div style="clear:both;"></div>

<!-- 分页信息 start -->
<div class="page mt20">
           <span>
               <a href="order.html?page=<?=$page['page']<=1?1:$page['page']-1?>">上一页</a>

               <?php for($i=1;$i<=$page['yema'];$i++){?>
                   <a  href="order.html?page=<?=$i?>"><?=$i?></a>
                   <!--class ="cur"-->
               <?php }?>

               <a href="order.html?page=<?=$page['page']>=$page['total']?$page['total']:$page['page']+1?>">下一页</a>
           </span>
</div>
<!-- 分页信息 end -->

