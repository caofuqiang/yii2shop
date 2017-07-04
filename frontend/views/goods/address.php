
<div style="clear:both;"></div>

<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 我的订单</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">我的订单</a></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">账户信息</a></dd>
                <dd><b>.</b><a href="">账户余额</a></dd>
                <dd><b>.</b><a href="">消费记录</a></dd>
                <dd><b>.</b><a href="">我的积分</a></dd>
                <dd><b>.</b><a href="">收货地址</a></dd>
            </dl>

            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">返修/退换货</a></dd>
                <dd><b>.</b><a href="">取消订单记录</a></dd>
                <dd><b>.</b><a href="">我的投诉</a></dd>
            </dl>
        </div>
    </div>
    <!-- 左侧导航菜单 end -->

    <!-- 右侧内容区域 start -->
    <div class="content fl ml10">
        <div class="address_hd">
            <h3>收货地址薄</h3>
            <dl>
                <?php
                $count =1;
                foreach(\frontend\models\Address::find()->asArray()->where(['user_id'=>Yii::$app->user->id])->all() as $address){
                    echo '<dt>'.$count.'. '.$address['name'].' '.$address['province'].' '. $address['city'].' '. $address['area'].' '.$address['detail'].' '.$address['tel'].'</dt>';
                    echo "<dd><a href='eidt-address.html?id={$address["id"]}'>修改</a>&nbsp&nbsp&nbsp";
                    echo  "<a  href='del-address.html?id={$address["id"]}'>删除</a>&nbsp&nbsp";
                    echo  "<a href='set-address.html?id={$address["id"]}'>设为默认地址</a>&nbsp&nbsp";
                    echo  "<a href='set-address.html?id={$address["id"]}'>设为默认地址</a>&nbsp&nbsp";
                    if($address['status'] ==1 ){
                        echo '默认地址';
                    }
                    echo '</dd><br/>';

                    $count++;
                }
                ?>
            </dl>
        </div>

        <div class="address_bd mt10">
            <h4>新增收货地址</h4>
            <?php
                $form =\yii\widgets\ActiveForm::begin(['fieldConfig'=>[
                    'options'=>[
                        'tag'=>'li',
                    ],
                    'errorOptions'=>[
                        'tag'=>'p'
                    ]
                ]]);
                echo '<ul>';
                echo $model->province;
                echo $form->field($model,'name')->textInput(['class'=>'txt']);
                echo '<label for="">* 所在地区：</label>';
                echo $form->field($model,'province',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList(['0'=>'==省=='],['id'=>'province']);
                echo $form->field($model,'city',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList(['0'=>'==市=='],['id'=>'city']);
                echo $form->field($model,'area',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList(['0'=>'==区/县=='],['id'=>'area']);
                echo $form->field($model,'detail')->textInput(['class'=>'txt address']);
                echo $form->field($model,'tel')->textInput(['class'=>'txt']);
                echo $form->field($model,'status')->radioList(['1'=>'是',0=>'否'])->label(' 设为默认：');
                echo '<br/><br/>';
                echo '<li>
                        <label for="">&nbsp;</label>
                        <input type="submit" name="" class="btn" value="保存" />
                    </li>';
                echo '</ul>';
                \yii\widgets\ActiveForm::end();
            ?>
        </div>

    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->

<div style="clear:both;"></div>
<?php
/**
 * @var $this \yii\web\View
 */
$this->registerJsFile('@web/js/address.js');
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
       /* 省份,遍历加入*/
       $(address).each(function(){
           /*console.debug(this.name);*/
           var html = '<option value="'+this.name+'">'+this.name+'</option>';        
        $("#province").append(html);
       })
     /*  切换（选中）省，读取该省对应的市，*/
      $("#province").change(function(){
          var province = $('#province').val();
          $(address).each(function(){
              if(this.name == province){
                    var html = '<option value="">==市==</option>';
                     $(this.city).each(function(){
                        html += '<option value="'+this.name+'">'+this.name+'</option>';        
                    })  
                    $('#city').html(html);
              }
          });
           //将县的下拉框数据清空
           $('#area').html('<option value="">==区/县==</option>');
      })  
      //市改变。添加区县
      $('#city').change(function(){
          var city = $('#city').val();
          $(address).each(function(){
              if(this.name == $('#province').val()){//找到省
                   $(this.city).each(function(){//遍历城市
                        if(this.name == city){//找到城市
                             var html = '<option value="">==区/县==</option>';
                             $(this.area).each(function(i,v){
                                 html += '<option value="'+v+'">'+v+'</option>';        
                             })
                               $('#area').html(html);
                        }
                   })
              }
          })   
      })   
JS
));

//地址回显
$js='';
if($model->province){
    $js .= '$("#province").val("'.$model->province.'");';
}
if($model->city){
    $js .=  '$("#province").change();$("#city").val("'.$model->city.'");';
}
if($model->area){
    $js .= '$("#city").change();$("#area").val("'.$model->area.'");';
}

$this->registerJs($js);

/*
$js = '';
if($model->province){
    $js .= '$("#province").val("'.$model->province.'");';
}
if($model->city){
    $js .= '$("#province").change();$("#city").val("'.$model->city.'");';
}
if($model->county){
    $js .= '$("#address-city").change();$("#address-county").val("'.$model->county.'");';
}
$this->registerJs($js);
*/
