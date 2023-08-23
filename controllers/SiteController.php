<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\RoleAccessRule;

class SiteController extends Controller
{
    
    public $roleNames = [];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'as access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','createuser'],
                'ruleConfig' => [
                    'class' => RoleAccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['index','about'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['createuser', 'logout'],
                        'allow' => true,
                        'roles' => ['@'], 
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
        if(!Yii::$app->user->isGuest){
            $this->roleNames = Yii::$app->cache->get('userRoleNames_' . Yii::$app->user->id);
            //set session
            Yii::$app->session->set('roleNames', $this->roleNames);
        }
    }

    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) { 
            $user = Yii::$app->user->identity;
            $roles = $user->getRoleNames();
            Yii::$app->session->set('roleNames', $roles);
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /** 
     * funcion para probar los permisos de usuarios
     */
    public function actionCreateuser(){
        
    
            //yi flash si tiene permisos
            Yii::$app->session->setFlash('success', 'Tiene permisos para acceder a esta página.');
            $this->redirect(['site/index']);

    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) ) {
            if($model->login()){
                Yii::$app->session->set('userId', Yii::$app->user->identity->id);
                return $this->goBack();
            }else{
                return $this->render('login', [
                    'model' => $model,
                ]);
            }

        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }
    



    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
