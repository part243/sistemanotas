<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "datos_personales_representantes".
 *
 * @property int $id
 * @property string $ApellidoPaterno
 * @property string $ApellidoMaterno
 * @property string $Nombres
 * @property string $email
 * @property string $ci
 */
class DatosPersonalesRepresentantes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datos_personales_representantes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ApellidoPaterno', 'ApellidoMaterno', 'Nombres', 'email', 'ci'], 'required'],
            [['ApellidoPaterno', 'ApellidoMaterno', 'Nombres'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 45],
            [['ci'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ApellidoPaterno' => 'Apellido Paterno',
            'ApellidoMaterno' => 'Apellido Materno',
            'Nombres' => 'Nombres',
            'email' => 'Email',
            'ci' => 'Ci',
        ];
    }
}
