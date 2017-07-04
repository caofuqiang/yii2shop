<?php
namespace frontend\controllers;

use EasyWeChat\Message\News;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;
class WechatController extends Controller{
    //微信开发依赖的插件  easyWechat
    //关闭csrf验证
    public $enableCsrfValidation = false;

    //url 就是用于接受微信服务器发送的请求
    public function actionIndex()
    {
        //echo 'wechat-index';
        //echo  $_GET['echostr'];
        $app = new Application(\Yii::$app->params['wechat']);

        $app->server->setMessageHandler(function ($message) {
            // $message->FromUserName // 用户的 openid
            // $message->MsgType // 消息类型：event, text....
            //return "您好！欢迎关注我!";
            switch ($message->MsgType){
                case 'text':
                    //文本消息
                    switch ($message->Content){
                        case '成都':
                            $xml = simplexml_load_file('http://flash.weather.com.cn/wmaps/xml/sichuan.xml');
                            foreach($xml as $city){
                                if($city['cityname'] == '成都'){
                                    $weather = $city['stateDetailed'];
                                    break;
                                }
                            }
                            return '成都的天气情况是：'.$weather;
                            break;
                        case '注册':
                            $url = Url::to(['user/register'],true);
                            return '点此注册'.$url;
                            break;
                        case '活动':
                            //回复图文消息 单图文信息
                            /*$news = new News([
                                'title'       => '十一大减价',
                                'description' => '图文信息的描述...',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://pic27.nipic.com/20130131/1773545_150401640000_2.jpg',
                            ]);
                            return $news;*/
                            //多图文信息
                            $news1 = new News([
                                'title'       => '十一大减价',
                                'description' => '图文信息的描述...',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://pic27.nipic.com/20130131/1773545_150401640000_2.jpg',
                            ]);
                            $news2 = new News([
                                'title'       => '流量5折',
                                'description' => '流量5折...',
                                'url'         => 'http://www.qq.com',
                                'image'       => 'http://pic74.nipic.com/file/20150810/10115763_233338370780_2.jpg',
                            ]);
                            $news3 = new News([
                                'title'       => '无限流量套餐',
                                'description' => '流量5折...',
                                'url'         => 'http://www.jd.com',
                                'image'       => 'http://pic3.16pic.com/00/37/06/16pic_3706700_b.jpg',
                            ]);
                            return [$news1,$news2,$news3];
                            break;
                    }



                    return '收到你的消息:'.$message->Content;
                    break;
                case 'event'://事件
                    //事件的类型   $message->Event
                    //事件的key值  $message->EventKey
                    if($message->Event == 'CLICK'){//菜单点击事件
                        if($message->EventKey == 'zxhd'){
                            $news1 = new News([
                                'title'       => '十一大减价',
                                'description' => '图文信息的描述...',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://pic27.nipic.com/20130131/1773545_150401640000_2.jpg',
                            ]);
                            $news2 = new News([
                                'title'       => '流量5折',
                                'description' => '流量5折...',
                                'url'         => 'http://www.qq.com',
                                'image'       => 'http://pic74.nipic.com/file/20150810/10115763_233338370780_2.jpg',
                            ]);
                            $news3 = new News([
                                'title'       => '无限流量套餐',
                                'description' => '流量5折...',
                                'url'         => 'http://www.jd.com',
                                'image'       => 'http://pic3.16pic.com/00/37/06/16pic_3706700_b.jpg',
                            ]);
                            return [$news1,$news2,$news3];
                        }
                    }

                    return '接受到了'.$message->Event.'类型事件'.'key:'.$message->EventKey;
                    break;
            }
        });
        $response = $app->server->serve();
// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;

    }


    //设置菜单
    public function actionSetMenu()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "最新活动",
                "key"  => "zxhd"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "个人中心",
                        "url"  => Url::to(['wechat/user'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/order'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账户",
                        "url" => Url::to(['wechat/login'],true)
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        //获取已设置的菜单（查询菜单）
        $menus = $menu->all();
        var_dump($menus);
    }

    //我的订单
    public function actionOrder()
    {
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $orders = Order::findAll(['member_id'=>$member->id]);
            var_dump($orders);
        }
    }

    //个人中心
    public function actionUser()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        var_dump($openid);
    }

    //授权回调页
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
//        var_dump($user->getId());
        //将openid放入session
        \Yii::$app->session->set('openid',$user->getId());
        return $this->redirect([\Yii::$app->session->get('redirect')]);

    }

    public function actionTest()
    {
        \Yii::$app->session->removeAll();
    }


    //绑定用户账号   将openid和用户账号绑定
    public function actionLogin()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }

        //让用户登录，如果登录成功，将openid写入当前登录账户
        $request = \Yii::$app->request;
        if(\Yii::$app->request->isPost){
            $user = Member::findOne(['username'=>$request->post('username')]);
            if($user && \Yii::$app->security->validatePassword($request->post('password'),$user->password_hash)){
                \Yii::$app->user->login($user);
                //如果登录成功，将openid写入当前登录账户
                Member::updateAll(['openid'=>$openid],'id='.$user->id);
                if(\Yii::$app->session->get('redirect')) return $this->redirect([\Yii::$app->session->get('redirect')]);
                echo '绑定成功';exit;
            }else{
                echo '登录失败';exit;
            }
        }

        return $this->renderPartial('login');
    }

    /*
     * 消息回复，回复普通文本消息，回复多图文消息
     * 设置菜单 （view click）
     * 处理菜单点击事件（点击菜单回复文本信息，点击菜单回复图文信息）
     * 网页授权获取openid
     * 绑定账户
     * 获取用户的订单
     */


}