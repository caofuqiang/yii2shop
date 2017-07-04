<?=\yii\bootstrap\Html::a('商品添加',['brand/add'],['class'=>'btn btn-info btn-sm'])?><br/><br/>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->logo?\yii\bootstrap\Html::img($model->logo,['height'=>20]):''?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::$sexOptions[$model->status]?></td>
            <td>
                <?php if (Yii::$app->user->can('brand/edit')) echo \yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-xs btn-warning'])?>
                <?php if (Yii::$app->user->can('brand/del')) echo \yii\bootstrap\Html::a('删除',['brand/del','id'=>$model->id],['class'=>'btn btn-xs btn-danger'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);?>

