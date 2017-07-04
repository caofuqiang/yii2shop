<?php echo \yii\bootstrap\Html::a('添加文章',['article/add'],['class'=>'btn btn-info','id'=>'add']);?>
    <div>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>简介</th>
                <th>文章类别</th>
                <th>排序</th>
                <th>状态</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            <?php foreach($model as $article_list)://if(($article_list->status)>-1){
                ?>
                <tr>
                    <td><?=$article_list->id?></td>
                    <td><?=$article_list->name?></td>
                    <td><?=$article_list->intro?></td>
                    <td><?=$article_list->articleCategory->name?></td>
                    <td><?=$article_list->sort?></td>
                    <td><?=($article_list->status)?'正常':'隐藏'?></td>
                    <td><?=date('Y-m-d H:i:s',$article_list->create_time)?></td>
                    <td><?php if (Yii::$app->user->can('article/edit')) echo \yii\bootstrap\Html::a('修改',['article/edit','id'=>$article_list->id],['class'=>'btn btn-warning btn-xs'])?>
                        <?php if (Yii::$app->user->can('article/list')) echo \yii\bootstrap\Html::a('查看文章',['article/list','id'=>$article_list->id],['class'=>'btn btn-success btn-xs'])?>
                        <?php if (Yii::$app->user->can('article/del')) echo \yii\bootstrap\Html::a('删除',['article/del','id'=>$article_list->id],['class'=>'btn btn-danger btn-xs'])?>
                    </td>
                </tr>
            <?php // }?>
            <?php endforeach;?>
        </table>
    </div>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);
?>