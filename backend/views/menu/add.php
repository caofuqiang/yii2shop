<?php
//添加菜单
$form = \yii\bootstrap\ActiveForm::begin();//表单开始

echo $form->field($model,'label')->textInput()->label('自定义一级菜单名');//菜单名称
echo $form->field($model,'url')->dropDownList($permissions,['prompt'=>'<---请选择菜单路由--->']);//菜单名称
echo $form->field($model,'parent_id')->dropDownList($menus,['prompt'=>'<---请选择父菜单--->']);//菜单父级名称
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();//表单结束