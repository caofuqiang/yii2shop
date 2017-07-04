<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo $form->field($model2,'content')->textarea();
//百度ueditor
//echo $form->field($model2,'content')->widget('kucha\ueditor\UEditor',[]);
//echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($cate,'id','name'));
echo $form->field($model,'article_category_id')->dropDownList(\backend\models\Article::getCategoryOptions(),['prompt'=>'=请选择分类=']);
echo $form->field($model,'sort');
//echo $form->field($model,'status')->radioList(['0'=>'隐藏','1'=>'正常']);
echo $form->field($model,'status')->radioList(\backend\models\Article::$status_options);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();