<?=\yii\bootstrap\Html::a('商品添加',['goods/add'],['class'=>'btn btn-info btn-sm'])?>
<?=\yii\bootstrap\Html::a('返回列表',['goods/index'],['class'=>'btn btn-info btn-sm'])?>
<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    //get方式提交,需要显式指定action
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'options'=>['class'=>'form-inline']
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-info btn-sm']);
\yii\bootstrap\ActiveForm::end();
?><br/><br/>

    <table class="table tab-bordered table-responsive">
        <tr>
            <th>ID</th>
            <th>商品名称</th>
            <th>货号</th>
            <th>LOGO图片</th>
            <th>商品分类ID</th>
            <th>品牌分类</th>
            <th>市场价格</th>
            <th>商品价格</th>
            <th>库存</th>
            <th>是否在售</th>
            <th>状态</th>
            <th>排序</th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods as $good):?>
            <tr>
                <td><?=$good->id?></td>
                <td><?=$good->name?></td>
                <td><?=$good->sn?></td>
                <td><?=$good->logo?\yii\bootstrap\Html::img($good->logo,['height'=>20]):''?></td>
                <td><?=$good->goodsCategory->name?></td>
                <td><?=$good->brand->name?></td>
                <td><?=$good->market_price?></td>
                <td><?=$good->shop_price?></td>
                <td><?=$good->stock?></td>
                <td><?=\backend\models\Goods::$sexOptions[$good->is_on_sale]?></td>
                <td><?=\backend\models\Goods::$sexOption[$good->status]?></td>
                <td><?=$good->sort?></td>
                <td><?=$good->create_time ? date('Y-m-d h:i:s',$good->create_time) : '发表文章时间' ; ?></td>
                <td>
                    <?php if (Yii::$app->user->can('goods/edit')) echo \yii\bootstrap\Html::a('修改',['goods/edit','id'=>$good->id],['class'=>'btn btn-xs btn-warning'])?>
                    <?php if(Yii::$app->user->can('goods/gallery')) echo \yii\bootstrap\Html::a('查看相册',['goods/gallery','id'=>$good->id],['class'=>'btn btn-success btn-xs'])?>
                    <?php if(Yii::$app->user->can('goods/del')) echo \yii\bootstrap\Html::a('删除',['goods/del','id'=>$good->id],['class'=>'btn btn-xs btn-danger'])?></td>
            </tr>
        <?php endforeach;?>
    </table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);?>