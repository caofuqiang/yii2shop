<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($models,'name');
echo $form->field($models,'description')->textarea();
//角色的权限
echo $form->field($models,'permission')->checkboxList(\backend\models\RoleForm::getPermissionOptions());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();