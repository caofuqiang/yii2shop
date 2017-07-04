<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'description');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'bin bin-info']);
\yii\bootstrap\ActiveForm::end();