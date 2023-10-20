<?php
namespace app\controllers;

use Yii;
use app\models\Customer;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;

class CustomerController extends ActiveController
{
    public $modelClass = Customer::class;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                QueryParamAuth::class,
            ],
        ];
        $behaviors['access'] = [    
            'class' => AccessControl::class,
            'only' => ['login','logout','signup'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login','signup'],
                    'roles' => ['?'],
                ],
                [
                    'allow'=> true,
                    'actions'=> ['logout'],
                    'roles'=> ['@'],
                ]
            ]
        ];
        return $behaviors;
    }
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }
    public function actions() 
    {
        $actions = parent::actions();
        // unset($actions['update'], $actions['delete'],$actions['create'],$actions['view']);
        return $actions;
    }
    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest) {
            return ["status"=>false, "message"=>"actionLogin error. Already logged in"];
        }
        $username = Yii::$app->request->get('username');
        $password = Yii::$app->request->get('password');
        $identity = Customer::findOne(['login'=>$username]);

        if($identity === null) {
            return ["status"=>true, "message"=> "actionLogin error. No such user;"];
        }
        if (password_verify($password,$identity['password'])) {
            if(Yii::$app->user->login($identity,3600)) {
                return ["status"=>true, "message"=> "actionLogin success. Login session is 60 minutes"];
            }
            else {
                return ["status"=>true, "message"=> "actionLogin login error."];
            }
        }
        return ["status"=>true, "message"=> "actionLogin error. Login or password incorrect."];
    }
    public function actionLogout()
    {
        if(!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return ["status"=>true, "message"=>"actionLogout success"];
        }
        return ["status"=>true, "message"=>"Already logouted"];
    }
    public function actionSignup()  {
        if(!Yii::$app->user->isGuest) {
            return ["status"=>false, "message"=>"actionSignup Already user"];
        }

        $username = Yii::$app->request->getBodyParam('username');
        $password = Yii::$app->request->getBodyParam('password');
        $name = Yii::$app->request->getBodyParam('name');
        $surname = Yii::$app->request->getBodyParam('surname');
        
        return ["status"=>true, "message"=>"actionSignup in process login:{$username}, pass:{$password}, name:{$name}, surname:{$surname}"];
    }
}
