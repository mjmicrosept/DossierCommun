<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 08/11/2018
 * Time: 15:34
 */

namespace app\controllers;

use Yii;
use app\models\AppCommon;
use app\models\User;
use app\models\Client;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\AnalyseGerme;
use app\models\AnalyseService;
use app\models\PortailUsers;
use app\models\FilterPrefUser;
use app\models\FilterModel;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\depdrop\DepDrop;


class SyntheseController extends Controller
{
    /**
     * {@inheritdoc}
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

    public function actionIndex(){
        $idClient = null;
        $idEtablissement = null;
        $clientList = null;
        $laboList = null;
        $modelList = [];
        $idUser = User::getCurrentUser()->id;

        $filterList = FilterPrefUser::find()->andFilterWhere(['id_user'=>$idUser])->groupBy('id_model')->all();
        foreach ($filterList as $filter) {
            if(!isset($modelList[$filter->id_service]))
                $modelList[$filter->id_service] = [];

            $model = FilterModel::find()->andFilterWhere(['id'=>$filter->id_model])->one();
            array_push($modelList[$filter->id_service],['id_model'=>$filter->id_model,'libelle'=>$model->libelle]);
        }
        Yii::trace($modelList);


        if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) {
            $idClient = PortailUsers::find()->andFilterWhere(['id_user' => $idUser])->one()->id_client;
            $clients = Client::getChildList($idClient);
            $clientList = [];
            foreach ($clients as $client) {
                array_push($clientList,$client->id);
            }
            $laboList = LaboClientAssign::getListIdLaboFromClients($idClient,$clientList);
        }
        else{
            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])){
                $clientList = PortailUsers::getIdClientUserGroup($idUser);
                $laboList = LaboClientAssign::getListIdLaboFromClients($idClient,$clientList);
                $portailIdClient = PortailUsers::find()->andFilterWhere(['id_user'=>$idUser])->one()->id_client;
                $idClient = Client::find()->andFilterWhere(['id'=>$portailIdClient])->one()->id_parent;
            }
            else{
                $idClient = PortailUsers::find()->andFilterWhere(['id_user' => $idUser])->one()->id_client;
                $clientList = [];
                array_push($clientList,$idClient);
            }
        }

        $tItems = [
            [
                'label'=>'<i class="fas fa-home"></i> Général',
                'content'=>self::getGeneralFilterContent($idClient,$clientList,$laboList),
                'active'=>true
            ]
        ];

        $servicesTabs = self::getServiceTabs();
        foreach ($servicesTabs as $servicesTab) {
            array_push($tItems,$servicesTab);
        }

        return $this->render('index', [
            'items'=>$tItems,
            'modelList'=>$modelList
        ]);
    }

    /**
     * Met en forme pour chaque service le Tab correspondant
     * @return array
     */
    private static function getServiceTabs(){
        $aTabs = [];
        $aService = [];
        $listService = AnalyseService::find()->andFilterWhere(['active'=>1])->orderBy('libelle')->all();

        foreach ($listService as $item) {
            $service = [
                'label'=>'<i class="fas fa-user"></i> '.$item->libelle,
                'content'=>self::getServiceFilterContent($item->id),
                'headerOptions' => ['class'=>'disabled service-tabs','id'=>'service-'.$item->id],
            ];
            array_push($aService,$service);
        }

        foreach ($aService as $item) {
            array_push($aTabs,$item);
        }

        return $aTabs;
    }

    /**
     * Retourne le contenu du filtre des germes pour un service donné
     * @param $idService
     * @return string
     */
    private static function getServiceFilterContent($idService){
        $listGermes = AnalyseGerme::find()->andFilterWhere(['active'=>1])->andFilterWhere(['id_service'=>$idService])->orderBy('code')->all();

        $result = '';
        $result .= '<div class="row" style="margin-bottom:30px;">';
        $result .= '<button class="btn btn-primary btn-save-pref" data-service="'.$idService.'" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Sauvegarder les préférences</button>';
        $result .= '<button class="btn btn-success btn-load-pref" data-service="'.$idService.'" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Charger les préférences</button>';
        $result .= '</div>';
        $result .= '<div class="row">';
        $result .= '<input type=hidden id="hfServicePref-'.$idService.'" value=""/>';
        $result .= '<form id="kvform-service-'.$idService.'" class="form-vertical" action="" method="post" role="form">';
        foreach ($listGermes as $germe) {
            $result .= '<div class="col-sm-4">';
            $result .= '<label><input type="checkbox" class="btn-chk-list-germe-'.$idService.'" data-germe="'.$germe->id.'" name="germeList[]" value="'.$germe->id.'"> '.$germe->code.' ('.$germe->libelle.')</label>';
            $result .= '</div>';
        }
        $result .= '</form>';
        $result .= '</div>';

        return $result;
    }

    /**
     * Retourne le ccontenu du filtre général en fonction du profil utilisateur
     * @param $idClient
     * @param $clientList
     * @param $laboList
     * @return string
     * @throws \Exception
     */
    private static function getGeneralFilterContent($idClient,$clientList,$laboList){
        $dataEtablissement = null;
        $idTemoin = '';
        if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]))
            $idTemoin = $idClient;
        $dataLabo = Labo::getAsListFromIdsForDepDrop($laboList);
        $strClientIds = '';
        foreach ($clientList as $item) {
            if($strClientIds == '')
                $strClientIds .= $item;
            else
                $strClientIds .= ','.$item;
        }

        if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
            $dataEtablissement = Client::getAsChildList($idClient);
        }
        elseif (User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])){
            $dataEtablissement = Client::getListUserGroup($clientList);
        }

        $result = '';
        $result .= '<input type=hidden id="hfAllIdClient" value="'.$strClientIds.'"/>';
        $result .= '<input type=hidden id="hfIdClient" value="'. $idTemoin .'"/>';
        $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';

        if(!User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])) {
            //User de type Admin ou Responsable
            $result .= Form::widget([
                'formName' => 'kvform',
                'form' => ActiveForm::begin(),
                'columns' => 2,
                'compactGrid' => true,

                // set global attribute defaults
                'attributeDefaults' => [
                    'labelOptions' => ['class' => 'col-sm-3 control-label', 'style' => 'margin-top:20px;'],
                    'inputContainer' => ['class' => 'col-sm-6 form-control', 'style' => 'border:none;'],
                    'container' => ['class' => 'form-group field-user-etablissementGroupAdmin'],
                ],
                'attributes' => [
                    'etablissement' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => $dataEtablissement,
                            'options' => [
                                'placeholder' => 'Sélectionner un ou plusieurs établissements', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Etablissements',
                    ],
                    'service' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => ArrayHelper::map(AnalyseService::find()->andFilterWhere(['active' => 1])->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                            'options' => [
                                'placeholder' => 'Sélectionner un ou plusieurs service', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Services',
                    ],
                ]
            ]);

            $result .= '<div class="row"><div class="col-sm-6"><label class="col-sm-3" style="margin-top:20px;" for="child-id">Laboratoires</label><div class="col-sm-6 form-control" style="border:none;">';
            $result .= DepDrop::widget([
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $dataLabo,
                'name' => 'laboratoire',
                'options' => ['id' => 'child-id', 'placeholder' => 'Aucun'],
                'select2Options' => ['pluginOptions' => ['allowClear' => true, 'multiple' => true]],
                'pluginOptions' => [
                    'depends' => ['kvform-etablissement'],
                    'url' => Url::to(['/synthese/get-labo-from-ids-client']),
                    'params' => ['hfIdClient', 'hfAllIdClient'],
                    'placeholder' => 'Sélectionner un ou plusieurs laboratoires'
                ]
            ]);
            $result .= '</div></div>';

            $result .= '<div class="col-sm-6">';
            $result .= Form::widget([
                'formName' => 'kvform',
                'columns' => 2,
                'compactGrid' => true,

                // set global attribute defaults
                'attributeDefaults' => [
                    'labelOptions' => ['class' => 'col-sm-3 control-label', 'style' => 'margin-top:20px;'],
                    'inputContainer' => ['class' => 'col-sm-6 form-control', 'style' => 'border:none;'],
                ],
                'attributes' => [
                    'conclusion' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => [0=>'Non conforme',1=>'Conforme'],
                            'options' => [
                                'placeholder' => 'Sélectionner une ou plusieurs conclusions', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Conclusions',
                    ],
                ]
            ]);
            $result .= '</div></div>';
            $result .= '</form>';

            $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';
            $result .= Form::widget([
                'formName' => 'kvform',
                'columns' => 4,
                'compactGrid' => true,

                // set global attribute defaults
                'attributeDefaults' => [
                    'labelOptions' => ['class' => 'col-sm-6 control-label', 'style' => 'margin-top:20px;'],
                    'inputContainer' => ['class' => 'col-sm-6 form-control', 'style' => 'border:none;'],
                ],
                'attributes' => [
                    'dateDebut' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\date\DatePicker',
                        'options' => [
                            'options' => [
                                'placeholder' => 'Date de début', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'autoclose' => true,
                            ]
                        ],
                        'label' => 'Date de début',
                    ],
                    'dateFin' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\date\DatePicker',
                        'options' => [
                            'options' => [
                                'placeholder' => 'Date de fin', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'autoclose' => true,
                            ]
                        ],
                        'label' => 'Date de fin',
                    ],
                ]
            ]);
            $result .= '</form>';
        }
        else{
            //User de type simple utilisateur (responsable que SON établissement)
            $result .= Form::widget([
                'formName' => 'kvform',
                'form' => ActiveForm::begin(),
                'columns' => 2,
                'compactGrid' => true,

                // set global attribute defaults
                'attributeDefaults' => [
                    'labelOptions' => ['class' => 'col-sm-3 control-label', 'style' => 'margin-top:20px;'],
                    'inputContainer' => ['class' => 'col-sm-6 form-control', 'style' => 'border:none;'],
                    'container' => ['class' => 'form-group field-user-etablissementGroupAdmin'],
                ],
                'attributes' => [
                    'service' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => ArrayHelper::map(AnalyseService::find()->andFilterWhere(['active' => 1])->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                            'options' => [
                                'placeholder' => 'Sélectionner un ou plusieurs service', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Services',
                    ],
                    'conclusion' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => [0=>'Non conforme',1=>'Conforme'],
                            'options' => [
                                'placeholder' => 'Sélectionner une ou plusieurs conclusions', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Conclusions',
                    ],
                ]
            ]);
            $result .= '</form>';
        }


        return $result;
    }

    /**
     * Récupère la liste des labo assignés aux établissements sélectionnés
     * @return array
     */
    public function actionGetLaboFromIdsClient(){
        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = $_POST['depdrop_params'];
        $clientIdLabo = $_data[0];
        $clientIdLabo = explode(',',$clientIdLabo);

        $listLabo = null;
        if(count($_data) > 1) {
            $allClientIdLabo = $_data[1];
            $allClientIdLabo = explode(',',$allClientIdLabo);
        }

        if($_data[0] != '') {
            $aIdsLabo = null;
            $aIdsLabo = LaboClientAssign::getListIdLaboFromClients(null,$clientIdLabo);
            $listLabo = Labo::find()->andFilterWhere(['active' => 1])->andFilterWhere(['IN','id',$aIdsLabo])->select('id, raison_sociale')->all();
            foreach ($listLabo as $item) {
                array_push($result,['id'=>$item->id,'name'=>$item->raison_sociale]);
            }
        }
        else{
            $aIdsLabo = null;
            $aIdsLabo = LaboClientAssign::getListIdLaboFromClients(null,$allClientIdLabo);
            $listLabo = Labo::find()->andFilterWhere(['active' => 1])->andFilterWhere(['IN','id',$aIdsLabo])->select('id, raison_sociale')->all();
            foreach ($listLabo as $item) {
                array_push($result,['id'=>$item->id,'name'=>$item->raison_sociale]);
            }
        }

        return ['output'=>$result];
    }

    /**
     * Fonction d'enregistrement des préférences de filtres sur les germes en fonction du service
     * @return array
     */
    public function actionSavePrefUser(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $idService = $_data['serviceId'];
        $germList = $_data['germList'];
        $modelExist = $_data['modelExist'];
        $modelNew = $_data['modelNew'];

        $modelName = '';

        //Modèle existant
        if($modelNew == ''){
            //On supprime (ce qui sera plus rapide que de boucler) les préférence de cet utilisateur pour ce service et ce modèle
            FilterPrefUser::deleteAll(['id_user'=>User::getCurrentUser()->id,'id_service'=>$idService,'id_model'=>$modelExist]);

            //Pour chaque germe on enregistre en préférence
            for($i = 0; $i < count($germList);$i++){
                $filter = new FilterPrefUser();
                $filter->id_user = User::getCurrentUser()->id;
                $filter->id_service = intval($idService);
                $filter->id_germe = intval($germList[$i]);
                $filter->id_model = intval($modelExist);

                if(!$filter->save())
                    $errors = true;
            }
            $model = FilterModel::find()->andFilterWhere(['id'=>$modelExist])->one();
            $modelName = $model->libelle;
        }
        else{
            //On crée le nouveau modèle
            $model = new FilterModel();
            $model->libelle = $modelNew;
            if(!$model->save())
                $errors = true;
            if(!$errors) {
                //Pour chaque germe on enregistre en préférence
                for ($i = 0; $i < count($germList); $i++) {
                    $filter = new FilterPrefUser();
                    $filter->id_user = User::getCurrentUser()->id;
                    $filter->id_service = intval($idService);
                    $filter->id_germe = intval($germList[$i]);
                    $filter->id_model = intval($model->id);

                    if (!$filter->save())
                        $errors = true;
                }
            }
            $modelName = $modelNew;
        }

        return ['errors'=>$errors,'modelName'=>$modelName];
    }

    /**
     * Fonction de chargement des préférences de filtres sur les germes en fonction du service
     * @return array
     */
    public function actionLoadPrefUser(){
        $errors = false;
        $germList = [];
        $modelName = '';
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $idService = $_data['serviceId'];
        $modelExist = $_data['modelExist'];

        //On charge les filtres pour l'utilisateur, le service donné et le modèle donné
        $prefList = FilterPrefUser::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->andFilterWhere(['id_service'=>$idService])->andFilterWhere(['id_model'=>$modelExist])->all();

        foreach ($prefList as $pref) {
            array_push($germList,$pref->id_germe);
        }

        $model = FilterModel::find()->andFilterWhere(['id'=>$modelExist])->one();
        $modelName = $model->libelle;

        return ['errors'=>$errors,'germList'=>$germList,'modelName'=>$modelName];
    }
}