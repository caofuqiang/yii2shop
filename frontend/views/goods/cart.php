<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/style/cart.css');
$this->registerJsFile('@web/js/cart1.js',['depends'=>\yii\web\JqueryAsset::className()]);
?>
    <!-- 主体部分 start -->
    <div class="mycart w990 mt10 bc">
        <h2><span>我的购物车</span></h2>
        <table>
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
            <?php  foreach($new as $model):?>

                <tr data-goods_id="<?=$model['id']?>">
                    <td class="col1"><a href=""><?=\yii\helpers\Html::img($model['logo'])?></a>  <strong><a href=""><?=$model['name']?></a></strong></td>
                    <td class="col3">￥<span><?=$model['shop_price']?></span></td>
                    <td class="col4">
                        <a href="javascript:;" class="reduce_num"></a>
                        <input type="text" name="amount" value="<?=$model['amount']?>" class="amount"/>
                        <a href="javascript:;" class="add_num"></a>
                    </td>
                    <td class="col5">￥<span class="money"><?=($model['shop_price']*$model['amount'])?></span></td>
                    <td class="col6"><a href="javascript:;" class="del_goods">删除</a></td>
                </tr>
            <?php endforeach;?>


            </tbody>
            <tfoot>
            <tr>
                <td colspan="6">购物金额总计： <strong>￥ <span id="total"></span></strong></td>
            </tr>
            </tfoot>
        </table>
        <div class="cart_btn w990 bc mt10">
            <a href="http://www.yii2shop.com/goods/index.html" class="continue">继续购物</a>
            <a href="http://www.yii2shop.com/goods/order.html" class="checkout">结 算</a>
        </div>
    </div>
    <!-- 主体部分 end -->
<?php
$url = \yii\helpers\Url::to(['goods/update-cart']);
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
JS
));
