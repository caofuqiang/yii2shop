<?=\yii\bootstrap\Html::a('文章添加',['article_category/add'],['class'=>'btn btn-info btn-sm'])?><br/><br/>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>类型</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::$sexOptions[$model->status]?></td>
            <td><?=$model->is_help ? '帮助' : '其他'?></td>
            <td>
                <?php if (Yii::$app->user->can('article_category/edit')) echo \yii\bootstrap\Html::a('修改',['article_category/edit','id'=>$model->id],['class'=>'btn btn-xs btn-warning'])?>
                <?php if (Yii::$app->user->can('article_category/del')) echo \yii\bootstrap\Html::a('删除',['article_category/del','id'=>$model->id],['class'=>'btn btn-xs btn-danger'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);?>


