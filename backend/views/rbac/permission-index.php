<?=\yii\bootstrap\Html::a('添加权限',['rbac/add-permission'],['class'=>'btn btn-info'])?>
<?=\yii\bootstrap\Html::a('返回列表',['rbac/permission-index'],['class'=>'btn btn-info'])?>
<table class="table table-responsive table-bordered">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td><?php if (Yii::$app->user->can('rbac/edit-permission')) echo \yii\bootstrap\Html::a('修改',['rbac/edit-permission','name'=>$model->name],['class'=>'btn btn-warning btn-xs'])?>
                <?php if (Yii::$app->user->can('rbac/del-permission')) echo \yii\bootstrap\Html::a('删除',['rbac/del-permission','name'=>$model->name],['class'=>'btn btn-danger btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
/*$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({

});');*/