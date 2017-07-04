<?php

namespace frontend\controllers;



use common\models\Product;
use frontend\models\Cart;
use yii\web\Controller;
use Yii;
use frontend\models\Order;
use yii\web\Response;
use yii\data\ActiveDataProvider;

use yii\web\NotFoundHttpException;

class OrderController extends Controller
{
    public $layout = 'order';

    public function actionIndex()
    {
      $model = new Cart();
      return $this->render('index',['model'=>$model]);
    }

    public function actionView($id)
    {
        $this->layout = 'cart';
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionAjaxStatus($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model= $this->findModel($id);

        if ($model) {
            $oldStatus = $model->status;
            $model->status = $status;
            $model->save();

            // 记录订单日志
            $orderLog = new Order([
                'order_id' => $model->id,
                'status' => $model->status,
            ]);
            $orderLog->save();

            // 如果订单为取消，则恢复对应的库存
            if ($oldStatus > 0 && $status == Order::STATUS_CANCEL) {
                $orderProducts = Order::find()->where(['order_id' => $model->id])->all();
                foreach ($orderProducts as $product) {
                    Product::updateAllCounters(['stock' => $product->number], ['id' => $product->product_id]);
                }
            }

            return [
                'status' => 1,
            ];
        }
        return [
            'status' => -1,
        ];
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}

