<?php

namespace app\controllers;

use Yii;
use app\models\Cron;
use yii\data\ActiveDataProvider;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CronController implements the CRUD actions for Cron model.
 */
class CronController extends Controller
{
    private $frToEn = [
        '' => '',
        'minute' => 'minute',
        'heures' => 'hourly',
        'jours' => 'daily',
        'semaines' => 'weekly',
        'mois' => 'monthly'
    ];
    //private static $taskFolder = 'satellite-risque\\';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Cron models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Cron::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cron model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cron model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cron();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $frequence = Yii::$app->request->post('Cron')['frequence'];
            $heure = Yii::$app->request->post('Cron')['heure'];
            $this->createTask($model,$this->frToEn[$frequence],$heure);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Cron model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->updateTask($model);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cron model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (self::isAlreadyInWindowsScheduler($model)) {
            $cmd = 'schtasks /delete /TN "'.Yii::$app->multidb->defaultDB.'\\'.$model->nom.'" /F';
            exec($cmd);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionDisableEnable($id) {
        $model = $this->findModel($id);
        $cmd = 'schtasks /change /tn "'.Yii::$app->multidb->defaultDB.'\\'.$model->nom.'" ';
        if ($model->isActive)
            $cmd .= '/disable';
        else
            $cmd .= '/enable';
        exec($cmd);
        return $this->redirect('index');
    }

    /**
     * Finds the Cron model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cron the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cron::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function commandList($model,$filter) {
        $output = array();
        $cmd = 'schtasks /query /fo LIST /tn "'.Yii::$app->multidb->defaultDB.'\\'.$model->nom.'" /V | FindStr /c:"'.$filter.'"';
        exec($cmd, $output);
//        return htmlentities(explode(': ', $output[0])[1], ENT_COMPAT, 'UTF-8');
        return explode(': ', $output[0])[1];
    }

    public static function derniereExecution($model) {
        return self::commandList($model,'Heure de la dern');
    }

    public static function prochaineExecution($model) {
        return self::commandList($model,'Prochaine ex');
    }

    public static function frequence($model) {
        $freq = self::commandList($model, 'Type de planification');
        return substr($freq,strrpos($freq,' ')+1);
    }

    public static function heure($model) {
        return self::commandList($model,'Heure de d');
    }

    public static function isDesactive($model) {
        return strpos(self::commandList($model,'Statut de la t'),'sactiv');
    }

    /**
     * True si la tâche passée en arguments est déjà dans le planificateur de tâches windows
     * @param $cron
     * @return bool
     */
    public static function isAlreadyInWindowsScheduler($cron) {
        $output = array();

        $cmd = 'schtasks /query /tn "'.Yii::$app->multidb->defaultDB.'\\'.$cron->nom.'" /V';
        exec($cmd, $output);

        if (count($output) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Créer la tâche passée en arguments
     * @param $cron
     */
    public static function createTask($cron,$frequence,$heure, $modifier = '1') {
        $cmd = 'schtasks /create /RU "'.Yii::$app->params['cronUser'].'" /RP "'.Yii::$app->params['cronPassword'].'" /sc '.$frequence.' /mo '.$modifier.' /st '.$heure.' /tn "'.Yii::$app->multidb->defaultDB.'\\'.$cron->nom.'" /tr "'.$cron->action.'"';
        if ($cron->date_limite != '') {
            $date_limite = date("d/m/Y", $cron->date_limite);

            $cmd .= " /ed $date_limite";
        }
        exec($cmd);
    }

    public static function updateTask($cron) {
        $cmd = 'schtasks /change /RU "'.Yii::$app->params['cronUser'].'" /RP "'.Yii::$app->params['cronPassword'].'" /tn "'.Yii::$app->multidb->defaultDB.'\\'.$cron->nom.'" /tr "'.$cron->action.'"';
        if ($cron->date_limite != '') {
            $date_limite = date("d/m/Y", $cron->date_limite);

            $cmd .= " /ed $date_limite";
        }
        exec($cmd);
    }
}
