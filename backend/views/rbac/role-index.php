<?=\yii\bootstrap\Html::a('添加角色',['rbac/add-role'],['class'=>'btn btn-info'])?>
<?=\yii\bootstrap\Html::a('返回列表',['rbac/role-index'],['class'=>'btn btn-info'])?>
<table class="table table-responsive table-bordered">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>权限</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td><?php
                foreach (Yii::$app->authManager->getPermissionsByRole($model->name) as $permission){
                    echo $permission->description;
                    echo ',';
                }
                ?></td>
            <td><?php if (Yii::$app->user->can('rbac/edit-role')) echo \yii\bootstrap\Html::a('修改',['rbac/edit-role','name'=>$model->name],['class'=>'btn btn-warning btn-xs'])?>
                <?php if (Yii::$app->user->can('rbac/data-role')) echo \yii\bootstrap\Html::a('删除',['rbac/data-role','name'=>$model->name],['class'=>'btn btn-danger btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>