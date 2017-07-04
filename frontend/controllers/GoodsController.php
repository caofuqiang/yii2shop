<?php
namespace frontend\controllers;
use Yii;
use frontend\components\SphinxClient;
use backend\models\GoodsGallery;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\data\Pagination;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;

header('Content-type:text/html ; charset=utf-8');
//商品
class GoodsController extends Controller{
    public $layout ='goods';

    //1.用户地址页,新增地址
    public function actionAddress(){
        $model = new Address();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //保存地址
                if($model->saveaddress()){
                    echo '<script>alert("保存成功");</script>';
                }
        }
        //$province = $model->getProvince();

        return $this->render('address',['model'=>$model]);
    }

    //2.获取省份
    public function actionGetLocation($pid){
        return ( json_encode(\frontend\models\Locations::find()->asArray()->where(['parent_id'=>$pid])->all()));
    }

    //3.用户删除地址
    public function  actionDelAddress($id){
        Address::findOne(['id'=>$id])->delete();
        $model = new Address();
        $this->redirect(['address','model'=>$model]);
    }

    //4.设置为默认地址
    public function actionSetAddress($id){
            $addressall = Address::findAll(['user_id'=>\Yii::$app->user->id]);//找到所有
            foreach ($addressall as $address){
                $address->status = 0 ;//取消默认地址
                $address->save(false);
            }
            $address = Address::findOne(['id'=>$id]);
            $address->status = 1 ;//设置新的默认地址
            $address->save(false);
            //echo '<script>alert("设置成功");</script>';
            $this->redirect(['address'/*,'model'=>$model,'province'=>$province]*/]);
    }

    //5.用户修改,收货地址
    public function actionEditAddress($id){
        $model = Address::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //保存地址
            if($model->saveaddress()){
                echo '<script>alert("保存成功");</script>';
            }
        }
        return $this->render('address',['model'=>$model]);
    }

    //6.商品list列表
    public function actionList($cate=156){
        $query = Goods::find();
        //分页,按条件询查所有商品
        $query->andWhere(['goods_category_id'=>$cate]);
        $count = $query->count();
        $page=new Pagination([
            'totalCount'=>$count,
            'defaultPageSize'=>4,// 每页显示3条
        ]);
        $goods  = $query->offset($page->offset)->limit($page->limit)->all();
        //var_dump($goods);exit;
        return $this->render('list',['cate'=>$cate,'goods'=>$goods,'page'=>$page]);
    }

    //商品分词收索
    public function actionSearch(){
        $query = Goods::find();
        //判断是否有搜索条件
        if(\Yii::$app->request->get('keywords') && \Yii::$app->request->get('keywords') != '请输入商品关键字'){
            $keyword = \Yii::$app->request->get('keywords');
            $cl = new SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $info = $keyword;
            $res = $cl->Query($info, 'goods');//shopstore_search
            //判断搜索结果
            if(!isset($res['matches'])){
                $query->where(['id'=>0]);//按id查询
            }else{
                $ids =ArrayHelper::map($res['matches'],'id','id');//返回查询出的id数组
                $query->where(['in','id',$ids]);//按id查询
            }
        }else{
            $keyword ='';
        }
        //分页
        $count = $query->count();
        $page=new Pagination([
            'totalCount'=>$count,
            'defaultPageSize'=>4,// 每页显示3条
        ]);
        $goods  = $query->offset($page->offset)->limit($page->limit)->all();
        //关键字显示红色
        $keywords = array_keys($res['words']);

        $options = [
            'before_match'=>'<span style="color:red">',
            'after_match'=>'</span>',
            'chunk_separator' => '...',
            'limit' => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
        ];
        foreach ($goods as $k=>$v){
                /* 可以有四个参数，前三个为必须
                array $docs    ：     即从数据库取出来的结果数组(fetch_assoc)；
                string $Index   ：    即我们在csft_mysql.conf 配置的索引名
                string $words  ：     搜索的关键词*/
            $name = $cl->BuildExcerpts([$v['name']],'goods',implode(',',$keywords),$options);
            //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
            $goods[$k]->name = $name[0];
        }

        //var_dump($keyword);exit;
        return $this->render('search',['keywords'=>$keyword,'goods'=>$goods,'page'=>$page]);
    }

    //7.商品详情页
    public function actionGoods($id){
        //1.查出商品信息
        $goods_info = Goods::findOne(['id'=>$id]);
        //2.查出商品图片
        $goods_pic = GoodsGallery::find()->asArray()->where(['goods_id'=>$id])->all();
        $model = new Goods();
      //  var_dump($model->cate);exit;
        return $this->render('goods',['model'=>$model,'goods_info'=>$goods_info,'goods_pic'=>$goods_pic]);
    }

    //8.购物车操作,增加
    public function actionCart_add(){
       //1.接受到goods_id和amount
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        //2.查询商品是否存在
        if(!Goods::findOne(['id'=>$goods_id])){
            throw new NotFoundHttpException('商品不存在');
        }
        //3.判断
        //用户是否登录
        if(\Yii::$app->user->isGuest){
            //3.1创建新的cookie对象
            $cookies = \Yii::$app->request->cookies;//REQUEST cookie
            //3.2先获取cookie中已经有的数据$cart
            $old_cookies = $cookies->get('cart');

            if($old_cookies == null){
                //cookie中没有购物车数据
                $cart =[];
            }else{
                $cart = unserialize($old_cookies->value);
            }
            //3.3保存前检查cookie中是否已经有此商品,有就更新，没有添加
            if(key_exists($goods_id,$cart)){
                $cart[$goods_id] += $amount;//更新相加
            }else{
                $cart[$goods_id] = $amount;//新增
            }
            //3.4添加到cookie
            $cookie = \Yii::$app->response->cookies;//response Cookies
            $data = new Cookie(['name'=>'cart', 'value'=>serialize($cart)]);
            $cookie->add($data);
        }else{
            //登录情况，数据保存到数据表、

            $cart = new Cart();
            $cart->member_id= \Yii::$app->user->id;
            $cart->goods_id = $goods_id ;
            $cart->amount = $amount ;
            $cart->save();
        }
        //跳转到购物车
        return $this->redirect(['goods/cart1']);
    }

    //9.购物车显示页面,第一步
    public function actionCart1(){
        $this->layout = 'cart';

        //未登录查询出cookie数据显示
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;//读取request中的Cookie
            $cookie = $cookies->get('cart');
         //   var_dump($cookie->value);exit;
            if($cookie == null){//判断有没有值
                $carts=[];
            }else{
                $carts =unserialize($cookie->value);
            }
            $model = [];
            foreach($carts as $good_id => $amount){//遍历，根据carts下标为商品ID找到商品信息
                $goods = \backend\models\Goods::findOne(['id'=>$good_id])->attributes;
                $goods['amount'] = $amount;
                $model[] = $goods;
            }
            return $this->render('cart1',['model'=>$model]);
        }else{
            //登录了查询数据库数据显示
            $carts = Cart::find()->asArray()->all();
            $model = [];
            foreach($carts as $cart){//遍历，根据carts下标为商品ID找到商品信息
                $goods = \backend\models\Goods::findOne(['id'=>$cart['goods_id']])->attributes;
                $goods['amount'] = $cart['amount'];
                $model[] = $goods;
            }
            return $this->render('cart1',['model'=>$model]);
        }
    }

    //10.购物车显示页面,第二步
    public function actionCart2(){
        //1.数据显示
        $this->layout = 'cart';
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['user/login']);
        }

        //查询出地址
        $address = Address::find()->asArray()->where(['user_id' => \Yii::$app->user->id])->all();
       // var_dump($address);exit;
        //查询购物车数据
        $cart = Cart::find()->where(['member_id' => \Yii::$app->user->id])->all();
        //根据购物车数据，查询商品数据
        $goods = [];
        foreach ($cart as $k){
            $good = Goods::findOne(['id'=>$k['goods_id']])->attributes;
            $good['amount'] = $k['amount'];
            $goods[]=$good;
        }

        //2.数据提交，生成订单吧保存到order表
        $request = new Request();
        //处理订单
        if($request->isPost){
            $model = new Order();
            //订单保存数据库
            //赋值
            $model->member_id = \Yii::$app->user->id;
            //地址信息
            $address = Address::findOne(['id'=>$_POST['address']]);
            $model->name = $address->name;
            $model->province = $address->province;
            $model->city = $address->city;
            $model->area = $address->area;
            $model->address = $address->detail;
            $model->tel = $address->tel;
            //配送方式
            foreach (Order::$delivery_method as $delivery){
                if($delivery['id'] == $_POST['delivery_id']){
                    $model->delivery_id = $delivery['id'];
                    $model->delivery_name = $delivery['method'];
                    $model->delivery_price = $delivery['price'];
                }
            }
            //支付方式
            foreach (Order::$payment_method as $payment){
                if($payment['id'] == $_POST['payment_id']){
                    $model->payment_id = $payment['id'];
                    $model->payment_name = $payment['method'];
                }
            }
            //订单总金额
            $model->total = $_POST['total_money'];
            //订单状态
            $model->status = 1;//（0已取消1待付款2待发货3待收货4完成）
            //订单号trade
            $model->trade_no = date('md').uniqid();
            //创建时间
            $model->create_time = time();
            //写操作，事物开启
            $tansaction = \Yii::$app->db->beginTransaction();
            try{
                $model->save();

                //3.保存商品订单详情order_goods表
                //根据用户ID，查询购物车数据表
                $carts = Cart::find()->asArray()->where(['user_id'=>\Yii::$app->user->id])->all();

                if($carts){//判断有没有商品信息
                    foreach($carts as $cart){
                        //cart中的商品信息保存到ordergoods
                        $ordergoods = new OrderGoods();
                        //查询到一个商品信息,保存指定数据到order_goods表
                        $good = Goods::findOne(['id'=>$cart['goods_id']]);
                        if($good==null){
                            throw new Exception('商品不存在');
                        }
                       // var_dump($good->stock);exit;
                        if($good->stock < $cart['amount']){
                            throw new Exception('库存不足');
                        }
                        $ordergoods->order_id = $model->oldAttributes['id'];
                        $ordergoods->goods_id = $cart['goods_id'];
                        $ordergoods->goods_name = $good->name;
                        $ordergoods->logo = $good->logo;
                        $ordergoods->price = $good->shop_price;
                        //商品的数量和金额
                        $ordergoods->amount = $cart['amount'];
                        $ordergoods->total =($cart['amount']-0)*($good->shop_price-0);
                        //扣库存
                        $good->stock -= $cart['amount'];
                       //商品库存更新
                        $good->save();
                        //保存一条商品数据
                        $ordergoods->save();
                    }
                }
                //操作完成，清除cookie,删除cart数据表内容，跳转
                \Yii::$app->response->cookies->remove('cart');
                $carts = Cart::find()->where(['user_id'=>\Yii::$app->user->id])->all();
                foreach ($carts as $cart){
                    $cart->delete();
                }
                //提交事物
                $tansaction->commit();
            }catch (Exception $exception){
                //回滚
                $tansaction->rollBack();
               return  '<script>if(confirm("对不起,商品库存不足")){
                           window.location.href="http://www.yii2shop.com/goods/cart1.html";
                        }</script>';
            }
            return $this->redirect('http://www.yii2shop.com/goods/cart3.html');
        }
        //cart2视图
        return $this->render('cart2',['address'=>$address,'goods'=>$goods]);
    }

    //11.购物车显示页面,第三步,处理保存订单
    public function actionCart3(){
        $this->layout = 'cart';
        return $this->render('cart3');
    }

    //12.修改，删除购物车商品
    public function actionUpdateCart(){
        //和添加是基本一样
        //1.接受到goods_id和amount
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        //2.查询商品是否存在
        if(!Goods::findOne(['id'=>$goods_id])){
            throw new NotFoundHttpException('商品不存在');
        }
        //3.判断用户是否登录
        if(\Yii::$app->user->isGuest){
            //3.1创建新的cookie对象
            $cookies = \Yii::$app->request->cookies;//REQUEST cookie
            //3.2先获取cookie中已经有的数据$cart
            $old_cookies = $cookies->get('cart');
            if($old_cookies == null){
                //cookie中没有购物车数据
                $cart =[];
            }else{
                //cookie有数据,获取原来的cookie数据
                $cart = unserialize($old_cookies->value);
            }

            //3.3更新操作，判断$amount的值，为0删除，不为0更新
            $new_cookie = \Yii::$app->response->cookies;//response Cookies可以读写
            if(!$amount == 0){
                $cart[$goods_id] = $amount;//更新
            }else{
                if(key_exists($goods_id,$cart)) unset($cart[$goods_id]);//商品存在，amount为0删除
            }
            //3.4修改/删除完成，添加到新cookie
            $data = new Cookie(['name'=>'cart', 'value'=>serialize($cart)]);
            $new_cookie->add($data);
        }else{
            //登录情况，修改删除数据表数据、
            $goods = Cart::findOne(['goods_id'=>$goods_id]);
            if(!$goods){
                throw new NotFoundHttpException('商品不存在');
            }
            if($amount){//修改
                $goods->amount = $amount;
                $goods->save();
            }else{//删除
                $goods->delete();
            }
        }
        //跳转到购物车页
        return $this->redirect(['goods/cart1']);
    }

    //13.订单显示页面
    public function actionOrder(){

        //1.先根据用户ID找到所有订单
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->id])->all();
            //var_dump($orders->goods_order);exit;
        //2.更具订单id找到所有商品
        $count = 0;
        $id=[];
        foreach ($orders as $order) {
            $query = OrderGoods::find()->where(['order_id' => $order->id]);
            $count += $query->count();//总条数
            foreach ($query->asArray()->all() as $goods){
                $goods['trade_no'] = $order->trade_no;//订单号
                $goods['name'] = $order->name;//收货人
                $goods['payment_name'] = $order->payment_name;//付款方式
                $goods['create_time'] = $order->create_time;//订单时间
                $goods['status'] = $order->status;//订单状态
                $id[] = $goods;//将goods逐条放到ID中,/后面分页使用
            }
        }
       //<!--（0已取消1待付款2待发货3待收货4完成）-->
        //状态计数
        $status['ready']=$status['confirm']=$status['ziti']= 0;
        if($id){
            foreach ($id as $k){
                if($k['status']==1){
                    $status['ready'] +=1;
                }elseif($k['status']==2){
                    $status['confirm']+=1;
                }elseif($k['status']==3){
                    $status['ziti']+=1;
                }
            }
        }
        //3.分页
        $page = isset($_GET['page'])?$_GET['page']:1;
        $pagesize=4;//每页4条
        $yema = ceil($count/$pagesize);
        $j = ($page-1)*$pagesize;

        $max = $j+$pagesize;//每页显示的最后一条的下标
        if($j+$pagesize>$count){
            $max = $count;
        }
        $goods =[];//保存商品数据
        for($i=$j;$i<$max;$i++){
            $goods[]=$id[$i];
        }
        //返回数据，跳转
        $page = ['page'=>$page,'total'=>$count,'yema'=>$yema];//保存页码
        return $this->render('order',['goods'=>$goods,'page'=>$page,'status'=>$status]);
    }

    //14.用户中心
    public function actionUser(){
        return $this->render('user');
    }

    //15.首页
    public function actionIndex(){
        $this->layout = 'goodsindex';
        return $this->render('index');
    }

    //16.清理超时未支付订单
    public function actionClean()
    {
        set_time_limit(0);//不限制脚本执行时间
        while (1){
            //超时未支付订单  待支付状态1超过1小时==》已取消0
            $models = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-300])->all();
            foreach ($models as $model){
                $model->status = 0;
                $model->save();
                //返还库存
                foreach($model->goods_order as $goods){
                    Goods::updateAllCounters(['stock'=>$goods->amount],'id='.$goods->goods_id);
                }
                echo 'ID为'.$model->id.'的订单被取消了。。。';
            }
            //1秒钟执行一次
            sleep(1);
        }
    }

    //17.分词搜索测试
    public function actionTest(){
        $cl = new \backend\components\SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);

        $cl->SetConnectTimeout ( 10 );
        $cl->SetArrayResult ( true );

// $cl->SetMatchMode ( SPH_MATCH_ANY);
        $cl->SetMatchMode ( SPH_MATCH_ALL);
        $cl->SetLimits(0, 1000);
        $info = '小米';
        $res = $cl->Query($info, 'goods');//shopstore_search
//print_r($cl);
        var_dump($res);exit;
    }

    //18.微信支付(已登录)
    public function actionPay($order_id){
        //找到订单
        $model = Order::findOne(['id'=>$order_id]);
        //检查订单支付方式和状态
        if($model->payment_id != 1){
            throw  new Exception('支付方式错误');
        }
        //微信支付
        //2 调用统一下单api
        $options = \Yii::$app->params['wechat'];
        $app = new Application($options);
        $payment = $app->payment;
        //创建订单
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => '京西商城订单支付',
            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => $model->trade_no,//订单交易号
            'total_fee'        => $model->total, //单位：分  订单总价格
            'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];

        $order = new \EasyWeChat\Payment\Order($attributes);
        $payment = $app->payment;
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
          //  $prepayId = $result->prepay_id;
            $code_url = $result->code_url;

            //将交易链接制作成二维码（https://easywechat.org/zh-cn/docs/payment.html#支付结果通知）
            $qrCode = new QrCode($code_url);
            $qrCode->setSize(300);
            header('Content-Type: '.$qrCode->getContentType());
            echo $qrCode->writeString();
        }else{
            //请求支付失败
        }
    }

    //19.微信支付结果通知地址（关闭csrf验证）
    public function actionNotice(){
        $app=new Application(\Yii::$app->params['wechat']);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::findOne(['trade_no'=>$notify->out_trade_no]);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            if($order->status == 1 && $successful){//判断订单状态
                $order->status = 2;
                $order->save();
            }
            return true; // 返回处理完成
        });
        return $response;
    }

    //20.权限管理
    public function behaviors()
    {
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'rules'=>[
                    [//未认证的用户可以查看
                        'allow'=>true,//是否允许
                        'actions'=>['index','login','register',
                            'cart_add','goods','cart1','test',
                            'list','update-cart','search','clean'
                        ],//指定操作
                        'roles'=>['?'],//？表示未认证用户
                    ],
                    [//老板,项目经理可以对商品增删改查
                        'allow'=>true,//是否允许
                        'actions'=>['index','login','register',
                            'cart_add','goods','cart1','list',
                            'address','get-location','set-address',
                            'edit-address','cart2','cart3','update-cart',
                            'user','order','test','search','clean'
                        ],//指定操作
                        'roles'=>['@'],
                    ],
                ]
            ],
        ];
    }

}