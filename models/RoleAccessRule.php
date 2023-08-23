<?php

namespace app\models;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use Yii;

class RoleAccessRule extends AccessRule
{
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        //$this->role es el rol que se le pasa en el controlador: 'roles' => ['?']....
        if (empty($this->roles)) {
            return true;
        }

        if (in_array('?', $this->roles) && $user->isGuest) {
            return true; // Acceso permitido para usuarios invitados
        }

        if (in_array('@', $this->roles) && !$user->isGuest) {
            return true; // Acceso permitido para usuarios autenticados
        }

        //si esta logueado verificar el rol
        if (!Yii::$app->user->isGuest) {
            foreach ($this->roles as $role) {
                if (in_array($role, $user->identity->getRoleNames(), true)) {
                    return true;
                }
            }
        }
        return false;
    }

}
