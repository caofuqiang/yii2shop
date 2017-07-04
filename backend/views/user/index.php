<?=\yii\bootstrap\Html::a('添加管理员',['user/register'],['class'=>'btn btn-info btn-sm','style'=>'float:left;'])?><br/><br/><br/>
<!--商品列表展示-->
<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>Email</th>
        <th>status</th>
        <th>注册时间</th>
        <th>最后登录时间</th>
        <th>最后登录Ip</th>
        <th>用户角色</th>
        <th>操作</th>
    </tr>
    <?php  foreach($users as $user){ ?>
        <tr>
            <td><?=$user->id?></td>
            <td><?=$user->username?></td>
            <td><?=$user->email?></td>
            <td><?=\backend\models\User::$status[$user->status]?></td>
            <td><?=date('Y-m-d h:i:s',$user->created_at)?></td>
            <td><?=$user->last_login_time ? date('Y-m-d h:i:s',$user->last_login_time) :''?></td>
            <td><?=$user->last_login_ip?></td>
            <td><?php
                foreach (Yii::$app->authManager->getRolesByUser($user->id) as $role){
                   echo $role->name;
                   echo '&nbsp&nbsp&nbsp&nbsp';
                }
                ?>
            </td>
            <td>
                <?php if (Yii::$app->user->can('user/edit')) echo \yii\bootstrap\Html::a('',['user/edit','id'=>$user->id],['class'=>'glyphicon glyphicon-pencil '])?>&nbsp&nbsp
                <?php if (Yii::$app->user->can('user/delete')) echo \yii\bootstrap\Html::a('',['user/delete','id'=>$user->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs'])?>&nbsp&nbsp
            </td>
        </tr>
    <?php };?>
</table>



<?php
/*echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);*/

