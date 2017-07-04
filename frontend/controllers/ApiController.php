<?php
namespace frontend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Brand;
use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\GoodsCategory;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    //获取品牌下面的商品
    /*public function actionGetGoodsByBrand()
    {
        if($brand_id = \Yii::$app->request->get('brand_id')){
            $goods = Goods::find()->where(['brand_id'=>$brand_id])->asArray()->all();
            return Json::encode(['status'=>1,'msg'=>'','data'=>$goods]);
        }
        return Json::encode(['status'=>'-1','msg'=>'参数不正确']);
    }*/
    public function init()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init();
    }

    //获取品牌下面的所有商品
    public function actionGetGoodsByBrand()
    {

        if ($brand_id = \Yii::$app->request->get('brand_id')) {
            $goods = Goods::find()->where(['brand_id' => $brand_id])->asArray()->all();
            return ['status' => 1, 'msg' => '', 'data' => $goods];
        }
        return ['status' => '-1', 'msg' => '参数不正确'];
    }

    /*public function actionGetGoods()
    {

    }*/

    //会员注册  POST
    public function actionUserRegister()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $member = new Member();
            $member->username = $request->post('username');
            $member->password = $request->post('password');
            //$member->password_hash = $request->post('password_hash');
            $member->email = $request->post('email');
            $member->tel = $request->post('tel');
            $member->code = $request->post('code');
            if ($member->validate()) {
                $member->password_hash = \Yii::$app->security->generatePasswordHash($member->password);
                $member->save(false);
                return ['status' => '1', 'msg' => '', 'data' => $member->toArray()];
            }
            //验证失败
            return ['status' => '-1', 'msg' => $member->getErrors()];
        }
        return ['status' => '-1', 'msg' => '请使用post请求'];
    }

    //修改会员注册
    public function actionUserEdit()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $member = Member::findOne(['id' => \Yii::$app->request->post('id')]);
            $member->username = $request->post('username');
            $member->password = $request->post('password');
            // $member->password_hash = $request->post('password_hash');
            $member->email = $request->post('email');
            $member->tel = $request->post('tel');
            if ($member->validate()) {
                $member->save(false);
                $member->password_hash = \Yii::$app->security->generatePasswordHash($member->password);
                return ['status' => '1', 'msg' => '', 'data' => $member->toArray()];
            }
            //验证失败
            return ['status' => '-1', 'msg' => $member->getErrors()];
        }
        return ['status' => '-1', 'msg' => '请使用post请求'];

    }

    //删除会员
    public function actionUserDel()
    {
        $member = Member::findOne(['id' => \Yii::$app->request->post('id')]);

        if ($member) {
            $member->delete();
            return ['status' => '1', 'msg' => '', 'data' => '删除成功'];
        }


        return ['status' => '-1', 'msg' => '请使用post请求'];
    }

    //登录
    public function actionLogin()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $user = Member::findOne(['username' => $request->post('username')]);
            if ($user && \Yii::$app->security->validatePassword($request->post('password'), $user->password_hash)) {
                \Yii::$app->user->login($user);
                return ['status' => '1', 'msg' => '登录成功'];
            }
            return ['status' => '-1', 'msg' => '账号或密码错误'];
        }
        return ['status' => '-1', 'msg' => '请使用post请求'];
    }

    //注销和退出
    public function actionLogout()
    {
        \Yii::$app->user->Logout;
        return ['status' => '-1', 'msg' => '注销成功'];
    }

    //获取当前登录用户信息
    public function actionGetCurrentUser()
    {
        if (\Yii::$app->user->isGuest) {
            return ['status' => '-1', 'msg' => '请先登录'];
        }
        return ['status' => '1', 'msg' => '', 'data' => \Yii::$app->user->identity->toArray()];
    }

    //高级API验证码
    public function actions()
    {
        return [
            'cpatcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 3,
                'maxLength' => 3,
            ],
        ];
    }

    //上传文件
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('img');
        if ($img) {
            $fileNname = '/imges/' . uniqid() . '.' . $img->extension;
            $result = $img->saveAs(\Yii::getAlias('@webroot') . $fileNname, 0);
            if ($result) {
                return ['status' => '1', 'msg' => '', 'data' => $fileNname];
            }
            return ['status' => '-1', 'msg' => $img->error];
        }
        return ['status' => '-1', 'msg' => '没有文件上传'];
    }
    //分页读取数据
    //获取商品列表
    public function actionList()
    {
        //
        $page1 = \Yii::$app->request->get('page1', 2);
        $page = \Yii::$app->request->get('page', 1);
        $keywords = \Yii::$app->request->get('keywords');
        $page = $page < 1 ? 1 : $page;
        $query = \frontend\models\Goods::find();
        $cate_id = \Yii::$app->request->get('cate_id');
        $cate = GoodsCategory::findOne(['id' => $cate_id]);
        if ($cate == null) {

        }
        switch ($cate->depth) {
            case 2:
                $query->andWhere(['goods_category_id' => $cate_id]);
                break;
            case 1://二级分类
                $ids = ArrayHelper::map($cate->children, 'id', 'id');
                $query->andWhere(['in', 'goods_category_id', $ids]);
                break;
            case 0;//一级分类
                $ids = ArrayHelper::map($cate->leaves()->asArray()->all(), 'id', 'id');
                $query->andWhere(['in', 'goods_category_id', $ids]);
                break;
        }
        if ($keywords) {
            $query->andWhere(['like', 'name', $keywords]);
        }

        //总条数
        $total = $query->count();
        //获取当前页的商品数据
        $goods = $query->offset($page1 * ($page - 1))->limit($page1)->asArray()->all();
        return ['status' => '1', 'msg' => '', 'data' => [
            'total' => $total,
            'per_page' => $page1,
            'page' => $page,
            'goods' => $goods
        ]];
    }

    //-发送手机验证码
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if (!preg_match('/^1[34578]\d{9}$/', $tel)) {
            return ['status' => '-1', 'msg' => '电话号码不正确'];
        }
        //检查上次发送时间是否超过1分钟
        $value = \Yii::$app->cache->get('time_tel_' . $tel);
        $s = time() - $value;
        if ($s < 60) {
            return ['status' => '-1', 'msg' => '请' . (60 - $s) . '秒后再试'];
        }

        $code = rand(1000, 9999);
        //$result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        $result = 1;
        if ($result) {
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_' . $tel, $code, 5 * 60);
            \Yii::$app->cache->set('time_tel_' . $tel, time(), 5 * 60);
            //echo 'success'.$code;
            return ['status' => '1', 'msg' => ''];
        } else {
            return ['status' => '-1', 'msg' => '短信发送失败'];
        }
    }

    //添加地址
    public function actionAddressRegister()
    {
        $request = \Yii::$app->request;
        if ($request->Post()) {
            if ($request->isPost) {
                $model = new Address();
                $model->name = $request->post('name');
                $model->province = $request->post('province');
                $model->city = $request->post('city');
                $model->area = $request->post('area');
                $model->detail = $request->post('detail');
                $model->tel = $request->post('tel');
                if ($model->validate()) {
                    $model->save(false);
                    return ['status' => '1', 'msg' => '', 'data' => $model->toArray()];
                }
                //验证失败
                return ['status' => '-1', 'msg' => $model->getErrors()];
            }
            return ['status' => '-1', 'msg' => '请使用post请求'];
        }
    }

    //修改地址
    public function actionAddressEdit()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model = Address::findOne(['id' => \Yii::$app->request->post('id')]);
            $model->name = $request->post('name');
            $model->province = $request->post('province');
            $model->city = $request->post('city');
            $model->area = $request->post('area');
            $model->detail = $request->post('detail');
            $model->tel = $request->post('tel');
            if ($model->validate()) {
                $model->save(false);
                return ['status' => '1', 'msg' => '', 'data' => $model->toArray()];
            }
            //验证失败
            return ['status' => '-1', 'msg' => $model->getErrors()];
        }
        return ['status' => '-1', 'msg' => '请使用post请求'];
    }

    //删除地址
    public function actionAddressDel()
    {
        $model = Address::findOne(['id' => \Yii::$app->request->post('id')]);
        if ($model) {
            $model->delete();
            return ['status' => '1', 'msg' => '', 'data' => '删除成功'];
        }
        return ['status' => '-1', 'msg' => '请使用post请求'];
    }

    //获取所有地址
    public function actionGetAddressBy()
    {

        if ($id = \Yii::$app->request->get('$id')) {
            $address = \frontend\models\Goods::find()->where(['$id' => $id])->asArray()->all();
            return ['status' => 1, 'msg' => '', 'data' => $address];
        }
        return ['status' => '-1', 'msg' => '参数不正确'];
    }
//添加商品到购物车API
    public function actionAddCart()
    {
        if (\Yii::$app->request->isPost) {
            //1.接受到goods_id和amount
            $goods_id = \Yii::$app->request->post('goods_id');
            $amount = \Yii::$app->request->post('amount');
            //return $goods_id;
            //2.查询商品是否存在
            if (!Goods::findOne(['id' => $goods_id])) {
                return ['status' => -1, 'error' => 1, 'data' => '商品id错误'];
            }
            //3.判断
            //用户是否登录
            if (\Yii::$app->user->isGuest) {
                //3.1创建新的cookie对象
                $cookies = \Yii::$app->request->cookies;//REQUEST cookie
                //3.2先获取cookie中已经有的数据$cart
                $old_cookies = $cookies->get('cart');

                if ($old_cookies == null) {
                    //cookie中没有购物车数据
                    $cart = [];
                } else {
                    $cart = unserialize($old_cookies->value);
                }
                //3.3保存前检查cookie中是否已经有此商品,有就更新，没有添加
                if (key_exists($goods_id, $cart)) {
                    $cart[$goods_id] += $amount;//更新相加
                } else {
                    $cart[$goods_id] = $amount;//新增
                }
                //3.4添加到cookie
                $cookie = \Yii::$app->response->cookies;//response Cookies
                $data = new Cookie(['name' => 'cart', 'value' => serialize($cart)]);
                $cookie->add($data);
            } else {
                //登录情况，数据保存到数据表、
                $cart = new Cart();
                $cart->member_id = \Yii::$app->user->id;
                $cart->goods_id = $goods_id;
                $cart->amount = $amount;
                $cart->save();
            }
            //跳转到购物车
            return ['status' => 1, 'error' => '', 'data' => $cart];
        } else {
            return ['status' => 1, 'error' => '', 'data' => '请使用POST方式'];
        }
    }
//修改购物车商品API
    public function actionEditCart()
    {
        if (\Yii::$app->request->isPost) {
            //1.接受到goods_id和amount
            $goods_id = \Yii::$app->request->post('goods_id');
            $amount = \Yii::$app->request->post('amount');
            //2.查询商品是否存在
            if (!Goods::findOne(['id' => $goods_id])) {
                return ['status' => -1, 'error' => 1, 'data' => '商品不存在'];
            }
            //3.判断用户是否登录
            if (\Yii::$app->user->isGuest) {
                //3.1创建新的cookie对象
                $cookies = \Yii::$app->request->cookies;//REQUEST cookie
                //3.2先获取cookie中已经有的数据$cart
                $old_cookies = $cookies->get('cart');
                if ($old_cookies == null) {
                    //cookie中没有购物车数据
                    $cart = [];
                } else {
                    //cookie有数据,获取原来的cookie数据
                    $cart = unserialize($old_cookies->value);
                }

                //3.3更新操作，判断$amount的值，为0删除，不为0更新
                $new_cookie = \Yii::$app->response->cookies;//response Cookies可以读写
                if (!$amount == 0) {
                    $cart[$goods_id] = $amount;//更新
                } else {
                    if (key_exists($goods_id, $cart)) unset($cart[$goods_id]);//商品存在，amount为0删除
                }
                //3.4修改/删除完成，添加到新cookie
                $data = new Cookie(['name' => 'cart', 'value' => serialize($cart)]);
                $new_cookie->add($data);
            } else {
                //登录情况，修改删除数据表数据、
                $goods = Cart::findOne(['goods_id' => $goods_id]);
                if (!$goods) {
                    return ['status' => -1, 'error' => 1, 'data' => '商品不存在'];
                }
                if ($amount) {//修改
                    $goods->amount = $amount;
                    $goods->save();
                } else {//删除
                    $goods->delete();
                }
            }
            return ['status' => 1, 'error' => '', 'data' => 'OK'];

        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用POST方式'];
        }
    }
//清空购物车API
    public function actionCleanCart()
    {

        if (\Yii::$app->request->isGet) {
            //判断用户是否登录
            if (\Yii::$app->user->isGuest) {
                //清除cookie
                \Yii::$app->response->cookies->remove('cart');
                return ['status' => 1, 'error' => '', 'data' => '清空成功'];
            }
            //清除数据库
            $user_id = \Yii::$app->user->id;
            $carts = Cart::findAll(['user_id' => $user_id]);
            foreach ($carts as $cart) {
                $cart->delete();
            }
            //清除cookie
            \Yii::$app->response->cookies->remove('cart');
            return ['status' => 1, 'error' => '', 'data' => '清空成功'];
        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用get方式'];
        }
    }
//获取购物车所有商品API
    public function actionGetCart()
    {
        if (\Yii::$app->request->isGet) {
            $user_id = \Yii::$app->user->id;
            //获得购物车数据
            $carts = Cart::find()->asArray()->where(['user_id' => $user_id])->all();
            //查询商品信息
            $goods = [];
            foreach ($carts as $cart) {
                $good = Goods::findOne(['id' => $cart['goods_id']])->attributes;
                $good['amount'] = $cart['goods_id'];
                //处理商品分类和品牌
                $good['brand'] = Brand::findOne(['id' => $good['brand_id']])->name;
                $good['category'] = GoodsCategory::findOne(['id' => $good['goods_category_id']])->name;
                unset($good['brand_id']);
                unset($good['goods_category_id']);
                //得到商品信息
                $goods[] = $good;
            }
            return ['status' => 1, 'error' => '', 'data' => $goods];
        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用GET方式'];
        }
    }
//获取支付方式API
    public function actionGetPayMethod()
    {
        if (\Yii::$app->request->isGet) {
            $order_id = \Yii::$app->request->get('order_id');//获取订单id
            //判断登录用户id和订单id是否匹配符
            if (!Order::find()->where(['id' => $order_id])->andWhere(['member_id' => \Yii::$app->user->id])->one()) {
                return ['status' => -1, 'error' => 1, 'data' => '订单号错误'];
            }
            //根据用户id和订单id查找支付方式
            $paymethod = Order::find()->where(['id' => $order_id])->andWhere(['member_id' => \Yii::$app->user->id])->one()->payment_name;
            return ['status' => 1, 'error' => '', 'data' => $paymethod];
        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用GET方式'];
        }
    }
//获取送货方式API
    public function actionGetDeliveryMethod()
    {
        if (\Yii::$app->request->isGet) {
            $order_id = \Yii::$app->request->get('order_id');//获取订单id
            //判断登录用户id和订单id是否匹配符
            if (!Order::find()->where(['id' => $order_id])->andWhere(['member_id' => \Yii::$app->user->id])->one()) {
                return ['status' => -1, 'error' => 1, 'data' => '订单号错误'];
            }
            //根据用户id和订单id查找支付方式
            $deliverymethod = Order::find()->where(['id' => $order_id])->andWhere(['member_id' => \Yii::$app->user->id])->one()->delivery_name;
            return ['status' => 1, 'error' => '', 'data' => $deliverymethod];
        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用GET方式'];
        }
    }
//提交订单API
    public function actionOrder()
    {
        if (\Yii::$app->request->isPost) {
            //接收参数
            $address_id = \Yii::$app->request->post('address_id');//收货地址
            $delivery_id = \Yii::$app->request->post('delivery_id');//送货方式
            $payment_id = \Yii::$app->request->post('payment_id');//支付方式
            $total_money = \Yii::$app->request->post('total_money');//订单总金额

            //订单保存数据库
            $model = new Order();
            $model->member_id = \Yii::$app->user->id;
            //地址信息查询保存
            $address = Address::findOne(['id' => $address_id]);
            $model->name = $address->name;
            $model->province = $address->province;
            $model->city = $address->city;
            $model->area = $address->area;
            $model->address = $address->detail;
            $model->tel = $address->tel;
            //配送方式
            foreach (Order::$delivery_method as $delivery) {
                if ($delivery['id'] == $delivery_id) {
                    $model->delivery_id = $delivery['id'];
                    $model->delivery_name = $delivery['method'];
                    $model->delivery_price = $delivery['price'];
                }
            }
            //支付方式
            foreach (Order::$payment_method as $payment) {
                if ($payment['id'] == $payment_id) {
                    $model->payment_id = $payment['id'];
                    $model->payment_name = $payment['method'];
                }
            }
            //订单总金额
            $model->total = $total_money;
            //订单状态
            $model->status = 1;//（0已取消1待付款2待发货3待收货4完成）
            //订单号trade
            $model->trade_no = date('md') . uniqid();
            //创建时间
            $model->create_time = time();
            //写操作，事物开启
            $tansaction = \Yii::$app->db->beginTransaction();
            try {
                $model->save();

                //3.保存商品订单详情order_goods表
                //根据用户ID，查询购物车数据表
                $carts = Cart::find()->asArray()->where(['user_id' => \Yii::$app->user->id])->all();

                if ($carts) {//判断有没有商品信息
                    foreach ($carts as $cart) {
                        //cart中的商品信息保存到ordergoods
                        $ordergoods = new OrderGoods();
                        //查询到一个商品信息,保存指定数据到order_goods表
                        $good = Goods::findOne(['id' => $cart['goods_id']]);
                        if ($good == null) {
                            throw new Exception('商品不存在');
                        }
                        // var_dump($good->stock);exit;
                        if ($good->stock < $cart['amount']) {
                            throw new Exception('库存不足');
                        }
                        $ordergoods->order_id = $model->oldAttributes['id'];
                        $ordergoods->goods_id = $cart['goods_id'];
                        $ordergoods->goods_name = $good->name;
                        $ordergoods->logo = $good->logo;
                        $ordergoods->price = $good->shop_price;
                        //商品的数量和金额
                        $ordergoods->amount = $cart['amount'];
                        $ordergoods->total = ($cart['amount'] - 0) * ($good->shop_price - 0);
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
                $carts = Cart::find()->where(['user_id' => \Yii::$app->user->id])->all();
                foreach ($carts as $cart) {
                    $cart->delete();
                }
                //提交事物
                $tansaction->commit();
                return ['status' => 1, 'error' => '', 'data' => $model];//添加成功，返回订单信息
            } catch (Exception $exception) {
                //回滚
                $tansaction->rollBack();
                return ['status' => -1, 'error' => 1, 'data' => '商品库存不足'];
            }
        } else {
            return ['status' => -1, 'error' => 1, 'data' => '请使用post提交'];
        }
    }
    //商品分类查询API
    public function actionGetGoodsCategory()
    {
        if(\Yii::$app->request->isGet){
            $parent_id = \Yii::$app->request->get('parent_id');//有值表示查询所有子分类
            $child_id = \Yii::$app->request->get('child_id');//有值表示查询父分类
            if($child_id){//查询父分类
                $child = GoodsCategory::findOne(['id'=>$child_id]);//先找到子分类
                $categories = GoodsCategory::find()->where(['id'=>$child->parent_id])->all();//查询父分类
            }else {
                if ($parent_id == 0) {//全部分类
                    $categories = GoodsCategory::find()->orderBy('tree,lft')->all();//找到所有子分类，
                } else {//全部子分类
                    $categories = GoodsCategory::find()->where(['parent_id' => $parent_id])->all();
                }
            }
            return ['status' => 1, 'error' => '', 'data' => $categories];
        }else{
            return ['status'=>-1,'error'=>1,'data'=>'请使用GET方式'];
        }
    }
//根据品牌获取商品API
    public function actionGetGoodsByCategory()
    {
        if($goods_category_id = \Yii::$app->request->get('goods_category_id')){
            $goods = Goods::find()->where(['goods_category_id'=>$goods_category_id])->all();
            return ['status'=>1,'error'=>'','data'=>$goods];
        }
        return ['status'=>-1,'error'=>'参数不正确','data'=>''];
    }
//获取文章分类API
    public function actionGetArticleCategory()
    {
        if(\Yii::$app->request->isGet){
            $article_categories = ArticleCategory::find()->all();
            return ['status'=>1,'error'=>'','data'=>$article_categories];
        }
        return ['status'=>-1,'error'=>1,'data'=>'请使用GET方式'];
    }
//根据分类获取文章API
    public function actionGetArticleByCategory()
    {
        if(\Yii::$app->request->isGet){
            $category_id = \Yii::$app->request->get('category_id');//获得该文章
            $articles =Article::find()->where(['article_category_id'=>$category_id])->all();
            return ['status'=>1,'error'=>'','data'=>$articles];
        }
        return ['status'=>-1,'error'=>1,'data'=>'请使用GET方式'];
    }
//根据文章获取文章分类API
    public function actionGetArticleCategoryByArticle()
    {
        if(\Yii::$app->request->isGet){
            $article_id = \Yii::$app->request->get('article_id');//获得该文章
            $article = Article::findOne(['id'=>$article_id]);
            $articles = ArticleCategory::findOne(['id'=>$article->article_category_id]);
            return ['status'=>1,'error'=>'','data'=>$articles];
        }
        return ['status'=>-1,'error'=>1,'data'=>'请使用GET方式'];
    }
}