<?php
//用户注册

$form = \yii\bootstrap\ActiveForm::begin();

echo $form->field($model,'username')->textInput(['disabled'=>true]);
echo $form->field($model,'password')->passwordInput();//旧密码
echo $form->field($model,'password1')->passwordInput();//新密码
echo $form->field($model,'password2')->passwordInput();//确认密码
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList(['1'=>'正常','0'=>'禁用']);
echo $form->field($model,'roles')->checkboxList(\backend\models\User::getRolelist());

echo \yii\bootstrap\Html::submitButton('提 交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();
?>