<?php
/**
 *@var $this \yii\web\View
 */
$this->registerCssFile('@web/style/cart.css');
$this->registerJsFile('@web/js/cart1.js',['depends'=>\yii\web\JqueryAsset::className()]);

?>


<!-- 主体部分 start -->
<div class="mycart w990 mt10 bc" style="overflow-x:visible">
    <h2><span>我的购物车</span></h2>
    <table >
        <thead>
        <tr>
            <th class="col1">商品名称</th>
            <th class="col3">单价</th>
            <th class="col4">数量</th>
            <th class="col5">小计</th>
            <th class="col6">操作</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach($model as $cart): ?>
            <tr data-goods_id="<?=$cart['id']?>" class="goods_cart">
                <td class="col1"><a href=""><?=\yii\helpers\Html::img($cart['logo'])?> </a> <strong><a href="">【1111购物狂欢节】惠JackJones杰克琼斯纯羊毛菱形格</a></strong></td>
                <td class="col3">￥<span><?=$cart['shop_price']?></span></td>
                <td class="col4">
                    <a href="javascript:;" class="reduce_num"></a>
                    <input type="text" name="amount" value="<?=$cart['amount']?>" class="amount"/>
                    <a href="javascript:;" class="add_num"></a>
                </td>
                <td class="col5">￥<span class="money"><?=number_format(($cart['shop_price']*$cart['amount']),2,'.',''); ?></span></td>
                <td class="col6"><a href="javascript:;" class="del_goods">删除</a></td>
            </tr>
        <?php endforeach; ?>

     <!--   <tr>
            <td class="col1"><a href=""><?/*=\yii\helpers\Html::img('@web/images/cart_goods2.jpg')*/?></a> <strong><a href="">九牧王王正品新款时尚休闲中长款茄克EK01357200</a></strong></td>
            <td class="col3">￥<span>1102.00</span></td>
            <td class="col4">
                <a href="javascript:;" class="reduce_num"></a>
                <input type="text" name="amount" value="1" class="amount"/>
                <a href="javascript:;" class="add_num"></a>
            </td>
            <td class="col5">￥<span>1102.00</span></td>
            <td class="col6"><a href="">删除</a></td>
        </tr>
        <tr>
            <td class="col1"><a href=""><?/*=\yii\helpers\Html::img('@web/images/cart_goods3.jpg')*/?></a> <strong><a href="">【1111购物狂欢节】捷王纯手工缝制休闲男鞋大头皮鞋 头层牛</a></strong></td>
            <td class="col3">￥<span>269.00</span></td>
            <td class="col4">
                <a href="javascript:;" class="reduce_num"></a>
                <input type="text" name="amount" value="1" class="amount"/>
                <a href="javascript:;" class="add_num"></a>
            </td>
            <td class="col5">￥<span>269.00</span></td>
            <td class="col6"><a href="">删除</a></td>
        </tr>-->
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6">购物金额总计： <strong>￥ <span id="total"> </span></strong></td>
        </tr>
        </tfoot>
    </table>
    <div class="cart_btn w990 bc mt10" >
        <a href="index.html" class="continue">继续购物</a>
        <?php
        $address = \frontend\models\Address::find()->where(['user_id'=>Yii::$app->user->id])->one();
        ?>
          <button type="submit" style="margin-right:30px;"  class="checkout" <?=$address?'data-address=1':'data-address=0'?>>结 算</button>
    </div>
</div>
<!-- 主体部分 end -->
<?php
$url =\yii\helpers\Url::to('update-cart.html');
$token = Yii::$app->request->csrfToken;
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
 /*--JS计算商品总价--*/
    
    $(function(){
            var total_money = 0;
        $('.money').each(function(){
             total_money += parseInt($(this).text());
        })
        $('#total').html(total_money+'.00');
    });

    /*--点击+-时修改数据--*/
    $('.reduce_num,.add_num').click(function(){
        goods_id = $(this).closest('tr').attr('data-goods_id');
        amount = $(this).closest('tr').find('.amount').val();
        //发送ajax  post请求
        $.post("$url",{'goods_id':goods_id,'amount':amount,'_csrf-frontend':"$token"},function(data){
            console.debug(data);
          });
    });
    
     /*--点击删除时删除数据--*/
     $('.del_goods').click(function(){
         var total_money = 0;
         goods_id = $(this).closest('tr').attr('data-goods_id');
         if(confirm('是否删除该商品')){
             //发送ajax请求，删除cookie或者数据库的商品
            $.post("$url",{'goods_id':goods_id,'amount':0,'_csrf-frontend':"$token"},function(){  });
             //找到该行tr删除
             $(this).closest('tr').remove();
            //重新计算价格
             $('.money').each(function(){
                    total_money += parseInt($(this).text());
             })
              $('#total').html(total_money+'.00');
         }
     });
    
     
     //先判断购物车有没有数据
     
     //点击提交，检测有没有收货地址，有就进行下一步，否则跳转到地址编辑页
     $('.checkout').click(function(){
         size = $('.goods_cart').size();
         console.debug(size);
         if(size==0){
             alert('购物车还没有商品,请到商城选购');
         }else{
             if($(this).attr('data-address')==1){
                window.location.href ="cart2.html";
             }else{
               if(confirm("请填写收货地址")){
                window.location.href ="address.html";
             }
         }
         }
         
     })
JS
))
?>
