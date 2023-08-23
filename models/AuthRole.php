<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_role".
 *
 * @property int $id
 * @property string $name_role
 * @property string|null $description
 * @property string|null $created_at
 * @property string $updated_at
 *
 * @property AuthAssignmentRole[] $authAssignmentRoles
 */
class AuthRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_role'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_role'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_role' => 'Name Role',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthAssignmentRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignmentRoles()
    {
        return $this->hasMany(AuthAssignmentRole::class, ['role_id' => 'id']);
    }

 

 
}
