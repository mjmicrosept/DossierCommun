<?php

namespace app\models;

use Yii;
use app\controllers\CronController;

/**
 * This is the model class for table "cron".
 *
 * @property int $id
 * @property string $nom
 * @property string $action
 * @property int $date_limite
 */
class Cron extends \yii\db\ActiveRecord
{
    private $_isInWindowsScheduler = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nom', 'action', 'frequence', 'heure'], 'required'],
            ['date_limite', 'date', 'timestampAttribute'=>'date_limite'],
            [['nom'], 'string', 'max' => 50],
            [['action'], 'string', 'max' => 150],
            [['frequence', 'heure'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nom' => 'Nom',
            'action' => 'Action',
            'date_limite' => 'Date Limite',
            'derniereExecution' => 'Dernière exécution',
            'prochaineExecution' => 'Prochaine exécution'
        ];
    }

    /**
     * @return string
     */
    public function getDerniereExecution() {
        if (CronController::isAlreadyInWindowsScheduler($this))
            return CronController::derniereExecution($this);
    }

    /**
     * @return string
     */
    public function getProchaineExecution() {
        if (CronController::isAlreadyInWindowsScheduler($this))
            return CronController::prochaineExecution($this);
    }

    /**
     * @return bool
     */
    public function getIsActive() {
        if (CronController::isAlreadyInWindowsScheduler($this))
            return !CronController::isDesactive($this);
    }

    /**
     * @return string
     */
    public function getFrequence() {
        if (CronController::isAlreadyInWindowsScheduler($this))
            return CronController::frequence($this);
        else
            return 'jours';
    }

    /**
     * Ne pas supprimer.
     */
    public function setFrequence() {

    }

    /**
     * @return string
     */
    public function getHeure() {
        if (CronController::isAlreadyInWindowsScheduler($this))
            return CronController::heure($this);
        else
            return date("h:i:s");
    }

    /**
     * Ne pas supprimer.
     */
    public function setHeure() {

    }

    /**
     * @return bool
     */
    public function getIsInWindowsScheduler() {
        if (is_null($this->_isInWindowsScheduler))
            $this->_isInWindowsScheduler = CronController::isAlreadyInWindowsScheduler($this);
        return $this->_isInWindowsScheduler;
    }
}
