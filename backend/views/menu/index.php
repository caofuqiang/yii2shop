<?=\yii\bootstrap\Html::a('添加菜单',['menu/add-menu'],['class'=>'btn btn-info btn-sm'])?><br/><br/>
<table class="table table-bordered table-hover table-striped" id="myTable">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>路由</th>
        <th>上级菜单</th>
        <th>操作</th>
    </tr>
    <?php  foreach ($menus as $menu): ?>
        <tr>
            <td><?=$menu->id?></td>
            <td><?=$menu->label?></td>
            <td><?=$menu->url?></td>
            <td><?=$menu->parent_id?></td>
            <td>
                <?php if (Yii::$app->user->can('menu/edit-menu')) echo \yii\bootstrap\Html::a('',['menu/edit-menu','id'=>$menu->id],['class'=>'glyphicon glyphicon-pencil btn btn-primary btn-xs'])?>&nbsp&nbsp
                <?php if (Yii::$app->user->can('menu/delete-menu')) echo \yii\bootstrap\Html::a('',['menu/delete-menu','id'=>$menu->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"/>
<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable();
    });

</script>