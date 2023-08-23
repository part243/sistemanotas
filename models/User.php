<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\base\Model;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuthAssignmentRole[] $authAssignmentRoles
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    private $_user = false;
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;
    public $temporalpassword;
    public $tempralpasswordtime;

    
    //public $_roleNames = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'auth_key', 'created_at'], 'required'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['currentPassword', 'newPassword', 'confirmPassword'], 'required',  'on' => 'changePassword'],
            [['currentPassword'], 'validateCurrentPassword', 'on' => 'changePassword'],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Las contraseñas no coinciden.', 'on' => 'changePassword'],
           // [['tempralpasswordtime','tempralpasswordtime', 'required', 'on'=> 'resetPassword']]
        ];
    }   


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Valida la contraseña actual.
     */
    public function validateCurrentPassword($attribute, $params)
    {
        $user = self::findByUsername($this->username);
        if (!$this->hasErrors()) {
            $user = self::findIdentity($this->id);
            if (!$user || !$user->validatePassword($this->currentPassword, $user->password)) {
                $this->addError($attribute, 'Contraseña actual incorrecta, comuníquese con el administrador.');
            }
        }
    }
    
    /**
     * Cambia la contraseña del usuario.
     */
    public function changePassword()
    {
        if ($this->validate()) {
            $user = self::findIdentity($this->id);
            $user->setPassword($this->newPassword);
            return $user->save(false);
        }
        return false;
    }



    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 1]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => 1]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => 1]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authkey)
    {
        return $this->auth_key === $authkey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }


    public function login()
    {
        if ($this->validate()) {
            $this->getRoleNames();
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }


    /**
     * Gets query for [[AuthAssignmentRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignmentRoles()
    {
        return $this->hasMany(AuthAssignmentRole::class, ['idusername' => 'id']);
    }


    /**
     * Obtener los nombres de los roles del usuario.
     * @return array
     */
    public function getRoleNames()
    {

        return Yii::$app->cache->getOrSet('userRoleNames_' . $this->id, function () {
            $roleNames = [];
    
            $authAssignmentRoles = $this->getAuthAssignmentRoles()
                ->orderBy(['role_id' => SORT_DESC])
                ->all();
    
            foreach ($authAssignmentRoles as $authAssignmentRole) {
                $role = $authAssignmentRole->getRole()->one();
                $roleNames[] = $role->name_role;
            }
    
            return $roleNames;
        });
    }


}
