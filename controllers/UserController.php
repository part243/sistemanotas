<?php

namespace app\controllers;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\RoleAccessRule;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    // public $roleNames=[];
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'as access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index','view','create','resetpassword','update'],
                    'ruleConfig' => [
                        'class' => RoleAccessRule::className(),
                    ],
                    'rules' => [
                        [
                            'actions' => ['index','view'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => [ 'delete','create','resetpassword','update'],
                            'allow' => true,
                            'roles' => ['SuperAdmin'], 
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
    // public function init()
    // {
    //     parent::init();
    //     if(!Yii::$app->user->isGuest){
    //         //$this->roleNames = Yii::$app->cache->get('userRoleNames_' . Yii::$app->user->id);
    //         // //set session
    //         // Yii::$app->session->set('roleNames', $this->roleNames);
    //     }
    // }

    // public function beforeAction($action)
    // {
    //     if (!Yii::$app->user->isGuest) { 
    //         $user = Yii::$app->user->identity;
    //         $roles = $user->getRoleNames();
    //         Yii::$app->session->set('roleNames', $roles);
    //     }
    //     return parent::beforeAction($action);
    // }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

   /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        if ($this->request->isPost) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
                
            if ($model->load($this->request->post())) {
                $security = Yii::$app->security;
                $model->password = $security->generatePasswordHash($model->password);
                $model->auth_key = $security->generateRandomString();
    
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    // var_dump($model->errors);
                    // return 'No se guardó';
                }
            }
        } else {
            $model->loadDefaultValues();
        }
    
        return $this->render('create', [
            'model' => $model,
            'isUpdate' => false,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'isUpdate' => true,
        ]);
    }

    /** 
     * Change password
     */
    public function actionChangepassword(){
        
        $existingUser = new User();
        $existingUser->setScenario('changePassword');
        if ($this->request->isPost && $existingUser->load($this->request->post())) {
            $userFromDb  = User::findOne(['username' => $existingUser->username]);
           // var_dump($existingUser); die();
            
            if ($userFromDb  !== null) {
                //$existingUser = $userFromDb;
                $existingUser->setScenario('changePassword');
               // $existingUser->currentPassword = $existingUser;
               // $existingUser->newPassword = $this->request->post('newPassword');
               // $existingUser->confirmPassword = $this->request->post('confirmPassword');
      
                if(!$userFromDb->status )
                {
                    Yii::$app->session->setFlash('error', 'El usuario ingresado no está activo.');
                    return $this->redirect(['user/changepassword']);
                }
                $userFromDb->setScenario('changePassword');
                $userFromDb->currentPassword = $existingUser->currentPassword;
                $userFromDb->newPassword = $existingUser->newPassword;
                $userFromDb->confirmPassword = $existingUser->confirmPassword;

                //var_dump($userFromDb); die();
                if ($userFromDb->validate()) {
                    $security = Yii::$app->security;
                    $userFromDb->password = $security->generatePasswordHash($userFromDb->newPassword);
                    $userFromDb->auth_key = $security->generateRandomString();
                    $userFromDb->updated_at = date('Y-m-d H:i:s');
                    //$userFromDb->save()
                    if ($userFromDb->save()) {
                        Yii::$app->session->setFlash('success', 'Se cambió la contraseña correctamente');
                        return $this->redirect(['site/index']);
                    } else {
                        Yii::$app->session->setFlash('error', $this->geterrorsString($userFromDb->errors));
                        return $this->redirect(['user/changepassword']);
                    }
                }else{
                    Yii::$app->session->setFlash('error', $this->geterrorsString($userFromDb->errors));

                    return $this->redirect(['user/changepassword']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'El nombre de usuario ingresado no existe.');
                return $this->redirect(['user/changepassword']);
            }
        }
        
    
        return $this->render('changepassword', [
            'model' => $existingUser, // Cambia el nombre de la variable aquí, debe ser $user, no $model
        ]);
    }

    /** 
     * reset password
     */
    public function actionResetpassword(){
        $user = new User();

        if($this->request->isPost && $user->load($this->request->post())){
            $userFromDb  = User::findOne(['username' => $user->username]);
            if($userFromDb){
                $security = Yii::$app->security;
                $userFromDb->password = $security->generatePasswordHash($user->username);
                $userFromDb->auth_key = $security->generateRandomString();
                $userFromDb->updated_at = date('Y-m-d H:i:s');
                if($userFromDb->save()){
                    Yii::$app->session->setFlash('success', 'Se cambió la contraseña correctamente de usuario: '.$userFromDb->username.'');
                    return $this->redirect(['user/resetpassword']);
                }else{
                    Yii::$app->session->setFlash('error', $this->geterrorsString($userFromDb->errors));
                    return $this->redirect(['user/resetpassword']);
                }

            }else{
                Yii::$app->session->setFlash('error', 'El nombre de usuario ingresado no existe.');
                return $this->redirect(['user/resetpassword']);
            }
        }



        return $this->render('resetpassword', [
            'model' => $user, // Cambia el nombre de la variable aquí, debe ser $user, no $model
        ]);
    }

    public function geterrorsString($errors) {
        $errorMessages = [];
        foreach ($errors as $fieldErrors) {
            $errorMessages = array_merge($errorMessages, $fieldErrors);
        }
        $errorString = implode(', ', $errorMessages);
        return $errorString;
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // Eliminar registros relacionados en auth_assignment_role
        \Yii::$app->db->createCommand()->delete('auth_assignment_role', ['idusername' => $id])->execute();
        $this->findModel($id)->delete();

        
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
