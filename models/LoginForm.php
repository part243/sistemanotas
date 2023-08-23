<?php

namespace app\models;

use yii\base\Model;
use Yii;
use app\models\Datospersonalesed;
use app\models\DatosPersonalesRepresentantes;

class LoginForm extends Model
{
    public $username;
    public $password;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            $loggedIn = Yii::$app->user->login($this->getUser());
            
            if ($loggedIn) {
                $user = Yii::$app->user->identity;
                $datosPersonales = Datospersonalesed::findOne(['ci' => $user->username]);
                
                if (!$datosPersonales) {
                    $datosPersonales = DatosPersonalesRepresentantes::findOne(['ci' => $user->username]);
                }
                
                if ($datosPersonales) {
                    Yii::$app->session->set('iddatospersonales', $datosPersonales->id);
                    Yii::$app->session->set('ci', $datosPersonales->ci);
                    Yii::$app->session->set('nombres', $datosPersonales->ApellidoPaterno." ".$datosPersonales->ApellidoMaterno." ".$datosPersonales->Nombres);
                    Yii::$app->session->set('email', $datosPersonales->email);
                    $authAssignmentRoles = $user->getAuthAssignmentRoles()
                                        ->orderBy(['role_id' => SORT_DESC])
                                        ->all();
                    $role = [];
                    foreach ($authAssignmentRoles as $authAssignmentRole) {
                        $rol = $authAssignmentRole->getRole()->one();
                        $role[] = [
                            'id' => $rol->id,
                            'name' => $rol->name_role
                        ];
                    }
                   //Yii::$app->session->set('role', $role);
                    return true; 
                } else {
                    Yii::$app->user->logout(); 
                    return false;
                }
            }
        }
        return false; // No se pudo validar o iniciar sesión
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user && $this->_user->status !== 1) {
                $this->_user = null;
            }
        }
        return $this->_user;
    }
}

