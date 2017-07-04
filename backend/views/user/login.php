<?php
//用户登录

$form = \yii\bootstrap\ActiveForm::begin();

echo $form->field($model,'username')->textInput(['value'=>isset($_GET['username'])? $_GET['username']: '' ]);
echo $form->field($model,'password_m')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),['captchaAction'=>'user/captcha','captchaAction'=>'user/captcha','template'=>'<div class="row"><div >{image}</div></div><div >{input}</div>']);
echo $form->field($model,'rememberme')->checkbox();
echo \yii\bootstrap\Html::submitButton('登 录',['class'=>'btn btn-info','style'=>'margin-left:10px;']);
//echo \yii\bootstrap\Html::a('管理员注册','register',['style'=>'margin-left:10px;','class'=>'btn btn-warning']);
\yii\bootstrap\ActiveForm::end();

?>