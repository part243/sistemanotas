<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_assignment_role".
 *
 * @property int $id
 * @property int $idusername
 * @property int $role_id
 * @property string|null $created_at
 * @property string $updated_at
 *
 * @property User $idusername0
 * @property AuthRole $role
 */
class AuthAssignmentRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_assignment_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idusername', 'role_id'], 'required'],
            [['idusername', 'role_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['idusername'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['idusername' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRole::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idusername' => 'Idusername',
            'role_id' => 'Role ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Idusername0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdusername0()
    {
        return $this->hasOne(User::class, ['id' => 'idusername']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AuthRole::class, ['id' => 'role_id']);
    }
}
