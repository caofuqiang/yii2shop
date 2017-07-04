<?php
//用户登录

$form = \yii\bootstrap\ActiveForm::begin();

echo $form->field($model,'username')->textInput(['value'=>isset($_GET['username'])? $_GET['username']: '' ]);
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'rememberme')->checkbox(['1'=>'自动登录']);

echo \yii\bootstrap\Html::submitButton('登 录',['class'=>'btn btn-info','style'=>'margin-left:10px;']);
echo \yii\bootstrap\Html::a('注 册','register',['style'=>'margin-left:10px;','class'=>'btn btn-warning']);
\yii\bootstrap\ActiveForm::end();

?>