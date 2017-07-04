<?php
echo \yii\bootstrap\Html::a('返回文章列表',['article/index'],['class'=>'btn btn-info','id'=>'add']);?>
<div>
    <h3><?=$model3->name?></h3>
    <p><?=$model2->content?></p>
</div>