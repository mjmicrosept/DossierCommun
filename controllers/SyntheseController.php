<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 08/11/2018
 * Time: 15:34
 */

namespace app\controllers;

use kartik\select2\Select2;
use Yii;
use app\models\User;
use app\models\Client;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\AnalyseService;
use app\models\AnalyseConformite;
use app\models\AnalyseConditionnement;
use app\models\AnalyseLieuPrelevement;
use app\models\AnalyseInterpretation;
use app\models\AnalyseData;
use app\models\AnalyseDataGerme;
use app\models\PortailUsers;
use app\models\FilterPrefUser;
use app\models\FilterPrefKeyword;
use app\models\FilterPrefPrelevement;
use app\models\FilterModel;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\depdrop\DepDrop;
use kartik\grid\GridView;


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

        //Génération de la liste des préférences
        $aModelKeyWord = FilterModel::find()
            ->leftJoin('filter_pref_keyword','filter_model.id = filter_pref_keyword.id_model')
            ->andFilterWhere(['filter_model.id_user'=>$idUser])
            ->andFilterWhere(['type'=>FilterModel::TYPE_GERME])
            //->groupBy('id_model')
            ->all();
        //Partie mots clés
        foreach ($aModelKeyWord as $item) {
            if(!isset($modelList['germe']))
                $modelList['germe'] = [];
            array_push($modelList['germe'],['id_model'=>$item->id,'libelle'=>$item->libelle]);
        }

        $aModelPreference = FilterModel::find()
            ->leftJoin('filter_pref_prelevement','filter_model.id = filter_pref_prelevement.id_model')
            ->andFilterWhere(['filter_model.id_user'=>$idUser])
            ->andFilterWhere(['type'=>FilterModel::TYPE_PRELEVEMENT])
            //->groupBy('id_model')
            ->all();
        //Partie lieux de prélèvements
        foreach ($aModelPreference as $item) {
            if(!isset($modelList['prelevement']))
                $modelList['prelevement'] = [];
            array_push($modelList['prelevement'],['id_model'=>$item->id,'libelle'=>$item->libelle]);
        }

        if(!Yii::$app->user->isSuperAdmin && !User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
            if (User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) {
                $idClient = PortailUsers::find()->andFilterWhere(['id_user' => $idUser])->one()->id_client;
                $clients = Client::getChildList($idClient);
                $clientList = [];
                foreach ($clients as $client) {
                    array_push($clientList, $client->id);
                }
                $laboList = LaboClientAssign::getListIdLaboFromClients($idClient, $clientList);
            } else {
                if (User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])) {
                    $clientList = PortailUsers::getIdClientUserGroup($idUser);
                    $laboList = LaboClientAssign::getListIdLaboFromClients($idClient, $clientList);
                    $portailIdClient = PortailUsers::find()->andFilterWhere(['id_user' => $idUser])->one()->id_client;
                    $idClient = Client::find()->andFilterWhere(['id' => $portailIdClient])->one()->id_parent;
                } else {
                    $idClient = PortailUsers::find()->andFilterWhere(['id_user' => $idUser])->one()->id_client;
                    $clientList = [];
                    array_push($clientList, $idClient);
                }
            }
        }
        else{
            $clientList = [];
            $laboList = [];
        }

        $tItems = [
            [
                'label'=>'<i class="fas fa-home"></i> Général',
                'content'=>self::getGeneralFilterContent($idClient,$clientList,$laboList),
                'active'=>true
            ],
            [
                'label'=>'<i class="fas fa-syringe"></i> Prélèvement',
                'content'=>self::getPrelevementFilterContent(),
            ],
            [
                'label'=>'<i class="fas fa-vials"></i> Germes',
                'content'=>self::getGermeFilterContent(),
            ]
        ];
        $idLabo = 0;
        if((User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_USER])) && !Yii::$app->user->isSuperAdmin){
            $idUser = User::getCurrentUser()->id;
            $idLabo = PortailUsers::find()->andFilterWhere(['id_user'=>$idUser])->one()->id_labo;
        }

        $is_delete = false;
        if(Yii::$app->user->isSuperAdmin){
            $is_delete = true;
        }
        else{
           if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_USER]) || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
               $is_delete = true;
        }


        return $this->render('index', [
            'items'=>$tItems,
            'modelList'=>$modelList,
            'idClient'=>User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]) ? $idClient : 0,
            'idLabo'=>$idLabo,
            'is_delete' => $is_delete
        ]);
    }

    /**
     * Retourne le contenu du filtre des mots clés pour la recherche de germes
     * @return string
     */
    private static function getGermeFilterContent(){
        $result = '';
        $result .= '<div class="row" style="margin-bottom:30px;">';
            $result .= '<button class="btn btn-primary btn-save-pref" data-tab="germe" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Sauvegarder les préférences</button>';
            $result .= '<button class="btn btn-success btn-load-pref" data-tab="germe" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Charger les préférences</button>';
        $result .= '</div>';
        $result .= '<div class="row">';
            $result .= '<div class="col-sm-3">';
                $result .= '<div class="form-group field-germe-new">';
                    $result .= '<label class="control-label" style="margin-left:15px;" for="input-germe-add"> Mot clé (contient)</label>';
                    $result .= '<div class="col-sm-3 form-control" style="border:none;">';
                        $result .= '<input type="text" class="form-control" id="input-germe-add" value=""/>';
                    $result .= '</div>';
                $result .= '</div>';
            $result .= '</div>';
            $result .= '<div class="col-sm-3">';
                $result .= '<button class="btn btn-default btn-add-word" style="margin-top: 30px;"><i class="fas fa-angle-double-right"></i></button>';
            $result .= '</div>';
            $result .= '<div class="col-sm-6">';
                $result .= '<div class="box box-info">';
                    $result .= '<div class="box-header with-border">';
                        $result .= '<h3 class="box-title">Liste des mots clés</h3>';
                    $result .= '</div>';
                    $result .= '<div class="row">';
                        $result .= '<div class="col-sm-6">';
                            $result .= '<ul id="list-word1">';
                            $result .= '</ul>';
                        $result .= '</div>';
                         $result .= '<div class="col-sm-6">';
                            $result .= '<ul id="list-word2">';
                            $result .= '</ul>';
                        $result .= '</div>';
                    $result .= '</div>';

                $result .= '</div>';
            $result .= '</div>';
        $result .= '</div>';
        $result .= '<div class="row">';

        $result .= '</div>';
        $result .= '<br/>';
        return $result;
    }

    /**
     * Retourne le contenu du filtre des prélèvements (conditionnement et lieu)
     * @return string
     */
    private static function getPrelevementFilterContent(){
        $result = '';
        $result .= '<div class="row">';
        $result .= '<button class="btn btn-primary btn-save-pref" data-tab="prelevement" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Sauvegarder les préférences</button>';
        $result .= '<button class="btn btn-success btn-load-pref" data-tab="prelevement" style="float:right;margin:10px;"><i class="fas fa-filter"></i> Charger les préférences</button>';
        $result .= '</div>';
        $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';
        $result .= Form::widget([
            'formName' => 'kvform',
            'form' => ActiveForm::begin(),
            'columns' => 2,
            'compactGrid' => true,

            // set global attribute defaults
            'attributeDefaults' => [
                'labelOptions' => ['class' => 'col-sm-6 control-label', 'style' => 'margin-top:20px;'],
                'inputContainer' => ['class' => 'col-sm-6 form-control', 'style' => 'border:none;'],
                'container' => ['class' => 'form-group field-user-etablissementGroupAdmin'],
            ],
            'attributes' => [
                'conditionnement' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => '\kartik\select2\Select2',
                    'options' => [
                        'data' => ArrayHelper::map(AnalyseConditionnement::find()->andFilterWhere(['active' => 1])->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                        'options' => [
                            'placeholder' => 'Sélectionner un ou plusieurs conditionnements', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ],
                    'label' => 'Conditionnements',
                ],
                'lieu_prelevement' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => '\kartik\select2\Select2',
                    'options' => [
                        'data' => ArrayHelper::map(AnalyseLieuPrelevement::find()->andFilterWhere(['active' => 1])->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                        'options' => [
                            'placeholder' => 'Sélectionner un ou plusieurs leiux de prélèvements', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ],
                    'label' => 'Lieux de prélèvements',
                ],
            ]
        ]);
        $result .= '</form>';

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
        Yii::trace($idClient);
        Yii::trace($clientList);
        Yii::trace($laboList);
        $dataEtablissement = null;
        $idTemoin = '';
        if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]))
            $idTemoin = $idClient;
        $dataLabo = Labo::getAsListFromIdsForDepDrop($laboList);
        $dataInterpretation = AnalyseInterpretation::getAsListFromIdsForDepDrop();
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
        elseif (User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_USER])){
            $idUser = User::getCurrentUser()->id;
            $idLabo = PortailUsers::find()->andFilterWhere(['id_user'=>$idUser])->one()->id_labo;
            $clientList = LaboClientAssign::getListIdClientFromLabo($idLabo);
            $dataEtablissement = Client::getListUserGroup($clientList);
        }

        $result = '';
        $result .= '<input type="hidden" id="hfAllIdClient" value="'.$strClientIds.'"/>';
        $result .= '<input type="hidden" id="hfIdParent" value=""/>';
        $result .= '<input type="hidden" id="hfIdClient" value="'. $idTemoin .'"/>';
        $result .= '<input type="hidden" id="hfIdConclusion" value=""/>';
        $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';

        if((User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])) && !Yii::$app->user->isSuperAdmin) {
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
                            'data' => ArrayHelper::map(AnalyseConformite::find()->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                            'options' => [
                                'placeholder' => 'Sélectionner une ou plusieurs conformités', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Conformité',
                    ],
                ]
            ]);
            $result .= '</div></div>';
            $result .= '</form>';

            $result .= '<div class="row">';
            $result .= '<div class="col-sm-6">';
            $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';
            $result .= Form::widget([
                'formName' => 'kvform',
                'columns' => 2,
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
                    ]
                ]
            ]);
            $result .= '</form>';
            $result .= '</div>';
            $result .= '<div class="col-sm-6">';
            $result .= '<div class="row">';
            $result .= '<div class="col-sm-12">';
            $result .= '<label class="col-sm-6" style="margin-top:20px;" for="child-id-interpretation">Interprétations</label>';
            $result .= '<div class="col-sm-6 form-control" style="border:none;">';
            $result .= DepDrop::widget([
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $dataInterpretation,
                'name' => 'interpretation',
                'options' => ['id' => 'child-id-interpretation', 'placeholder' => 'Aucun'],
                'select2Options' => ['pluginOptions' => ['allowClear' => true, 'multiple' => true]],
                'pluginOptions' => [
                    'depends' => ['kvform-conclusion'],
                    'url' => Url::to(['/synthese/get-interpretation-from-ids-conclusion']),
                    'params' => ['hfIdConclusion'],
                    'placeholder' => 'Sélectionner une ou plusieurs interprétations'
                ]
            ]);
            $result .= '</div></div></div></div></div>';
        }
        elseif(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]) && !Yii::$app->user->isSuperAdmin){
            //User de type simple utilisateur (responsable de SON établissement)
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
                            'data' => ArrayHelper::map(AnalyseConformite::find()->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                            'options' => [
                                'placeholder' => 'Sélectionner une ou plusieurs conformités', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Conformités',
                    ],
                ]
            ]);
            $result .= '</form>';

            $result .= '<div class="row">';
            $result .= '<div class="col-sm-6">';
            $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';
            $result .= Form::widget([
                'formName' => 'kvform',
                'columns' => 2,
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
                    ]
                ]
            ]);
            $result .= '</form>';
            $result .= '</div>';
            $result .= '<div class="col-sm-6">';
            $result .= '<div class="row">';
            $result .= '<div class="col-sm-12">';
            $result .= '<label class="col-sm-6" style="margin-top:20px;" for="child-id-interpretation">Interprétations</label>';
            $result .= '<div class="col-sm-6 form-control" style="border:none;">';
            $result .= DepDrop::widget([
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $dataInterpretation,
                'name' => 'interpretation',
                'options' => ['id' => 'child-id-interpretation', 'placeholder' => 'Aucun'],
                'select2Options' => ['pluginOptions' => ['allowClear' => true, 'multiple' => true]],
                'pluginOptions' => [
                    'depends' => ['kvform-conclusion'],
                    'url' => Url::to(['/synthese/get-interpretation-from-ids-conclusion']),
                    'params' => ['hfIdConclusion'],
                    'placeholder' => 'Sélectionner une ou plusieurs interprétations'
                ]
            ]);
            $result .= '</div></div></div></div></div>';
        }
        else{
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
                    'client' => [
                        'type' => Form::INPUT_WIDGET,
                        'widgetClass' => '\kartik\select2\Select2',
                        'options' => [
                            'data' => Client::getAsList(),
                            'options' => [
                                'placeholder' => 'Sélectionner un ou plusieurs clients', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => false
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Clients',
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
            $result .= '<div class="row"><div class="col-sm-6"><label class="col-sm-3" style="margin-top:20px;" for="child-id">Etablissements</label><div class="col-sm-6 form-control" style="border:none;">';
            if((User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_USER])) && !Yii::$app->user->isSuperAdmin){
                $result .= Select2::widget([
                    'data' => $dataEtablissement,
                    'name' => 'etablissement',
                    'options' => [
                        'id' => 'kvform-etablissement',
                        'placeholder' => 'Sélectionner un ou plusieurs établissements',
                        'multiple'=>true
                    ],
                ]);
            }
            else{
                $result .= DepDrop::widget([
                    'type' => DepDrop::TYPE_SELECT2,
                    'data' => [],
                    'name' => 'etablissement',
                    'options' => ['id' => 'kvform-etablissement', 'placeholder' => 'Aucun'],
                    'select2Options' => ['pluginOptions' => ['allowClear' => true, 'multiple' => true]],
                    'pluginOptions' => [
                        'depends' => ['kvform-client'],
                        'url' => Url::to(['/synthese/get-etablissement-from-id-client']),
                        'params' => ['hfIdParent'],
                        'placeholder' => 'Sélectionner un ou plusieurs établissements'
                    ]
                ]);
            }

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
                            'data' => ArrayHelper::map(AnalyseConformite::find()->orderBy('libelle')->asArray()->all(), 'id', 'libelle'),
                            'options' => [
                                'placeholder' => 'Sélectionner une ou plusieurs conformités', 'dropdownCssClass' => 'dropdown-vente-livr', 'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label' => 'Conformités',
                    ],
                ]
            ]);
            $result .= '</div></div>';

            $result .= '<div class="row">';
            $result .= '<div class="col-sm-6">';
            $result .= '<form id="kvform" class="form-vertical" action="" method="post" role="form">';
            $result .= Form::widget([
                'formName' => 'kvform',
                'columns' => 2,
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
                    ]
                ]
            ]);
            $result .= '</form>';
            $result .= '</div>';
            $result .= '<div class="col-sm-6">';
            $result .= '<div class="row">';
            $result .= '<div class="col-sm-12">';
            $result .= '<label class="col-sm-6" style="margin-top:20px;" for="child-id-interpretation">Interprétations</label>';
            $result .= '<div class="col-sm-6 form-control" style="border:none;">';
            $result .= DepDrop::widget([
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $dataInterpretation,
                'name' => 'interpretation',
                'options' => ['id' => 'child-id-interpretation', 'placeholder' => 'Aucun'],
                'select2Options' => ['pluginOptions' => ['allowClear' => true, 'multiple' => true]],
                'pluginOptions' => [
                    'depends' => ['kvform-conclusion'],
                    'url' => Url::to(['/synthese/get-interpretation-from-ids-conclusion']),
                    'params' => ['hfIdConclusion'],
                    'placeholder' => 'Sélectionner une ou plusieurs interprétations'
                ]
            ]);
            $result .= '</div></div></div></div></div>';
        }


        return $result;
    }

    /**
     * Récupère la liste des établissements liés au client
     * @return array
     */
    public function actionGetEtablissementFromIdClient(){
        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = $_POST['depdrop_params'];
        $clientId = $_data[0];
        $result = Client::getChildList($clientId);

        return ['output'=>$result];
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
     * Récupère la liste des interprétations possibles en fonction des conclusions sélectionnés
     * @return array
     */
    public function actionGetInterpretationFromIdsConclusion(){
        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = $_POST['depdrop_params'];
        $conclusionIdInterpretation = $_data[0];
        $conclusionIdInterpretation = explode(',',$conclusionIdInterpretation);

        if($_data[0] != '') {
            $aIdsInterpretation = null;
            $aIdsInterpretation = AnalyseInterpretation::getListIdInterpretationFromConclusion($conclusionIdInterpretation,false);
            foreach ($aIdsInterpretation as $item) {
                array_push($result,['id'=>$item->id,'name'=>$item->libelle]);
            }
        }
        else{
            $aIdsInterpretation = null;
            $aIdsInterpretation = AnalyseInterpretation::getListIdInterpretationFromConclusion(null,true);
            foreach ($aIdsInterpretation as $item) {
                array_push($result,['id'=>$item->id,'name'=>$item->libelle]);
            }
        }

        return ['output'=>$result];
    }

    /**
     * Fonction d'enregistrement des préférences de filtres sur les germes
     * @return array
     */
    public function actionSavePrefKeyWord(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $keyWordList = $_data['keyWordList'];
        $modelExist = $_data['modelExist'];
        $modelNew = $_data['modelNew'];

        $modelName = '';
        //Modèle existant
        if($modelNew == ''){
            //On supprime (ce qui sera plus rapide que de boucler) les préférence de cet utilisateur pour ce service et ce modèle
            FilterPrefKeyword::deleteAll(['id_user'=>User::getCurrentUser()->id,'id_model'=>$modelExist]);

            //Pour chaque germe on enregistre en préférence
            for($i = 0; $i < count($keyWordList);$i++){
                $filter = new FilterPrefKeyword();
                $filter->id_user = User::getCurrentUser()->id;
                $filter->keyword = $keyWordList[$i];
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
            $model->type = FilterModel::TYPE_GERME;
            $model->id_user = User::getCurrentUser()->id;
            $model->libelle = $modelNew;
            if(!$model->save())
                $errors = true;
            if(!$errors) {
                //Pour chaque germe on enregistre en préférence
                for ($i = 0; $i < count($keyWordList); $i++) {
                    $filter = new FilterPrefKeyword();
                    $filter->id_user = User::getCurrentUser()->id;
                    $filter->keyword = $keyWordList[$i];
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
     * Fonction de chargement des préférences de filtres sur les germes
     * @return array
     */
    public function actionLoadPrefKeyWord(){
        $errors = false;
        $keyWordList = [];
        $modelName = '';
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $modelExist = $_data['modelExist'];

        //On charge les filtres pour l'utilisateur, le service donné et le modèle donné
        $prefList = FilterPrefKeyword::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->andFilterWhere(['id_model'=>$modelExist])->all();

        foreach ($prefList as $pref) {
            array_push($keyWordList,$pref->keyword);
        }

        $model = FilterModel::find()->andFilterWhere(['id'=>$modelExist])->one();
        $modelName = $model->libelle;

        return ['errors'=>$errors,'keyWordList'=>$keyWordList,'modelName'=>$modelName];
    }

    /**
     * Fonction d'enregistrement des préférences de filtres sur les prélèvements
     * @return array
     */
    public function actionSavePrefPrelevement(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $conditionnement = $_data['conditionnement'];
        $lieuPrelevement = $_data['lieuPrelevement'];
        $modelExist = $_data['modelExist'];
        $modelNew = $_data['modelNew'];

        $modelName = '';
        //Modèle existant
        if($modelNew == ''){
            //On supprime (ce qui sera plus rapide que de boucler) les préférence de cet utilisateur pour ce service et ce modèle
            FilterPrefPrelevement::deleteAll(['id_user'=>User::getCurrentUser()->id,'id_model'=>$modelExist]);

            //Pour chaque conditionnement on enregistre en préférence
            if(count($conditionnement) != 0) {
                for ($i = 0; $i < count($conditionnement); $i++) {
                    $filter = new FilterPrefPrelevement();
                    $filter->id_user = User::getCurrentUser()->id;
                    $filter->id_conditionnement = $conditionnement[$i];
                    $filter->id_model = intval($modelExist);

                    if (!$filter->save())
                        $errors = true;
                }
            }

            //Pour chaque lieu de prélèvement on enregistre en préférence
            if(count($lieuPrelevement) != 0) {
                for ($i = 0; $i < count($lieuPrelevement); $i++) {
                    $filter = new FilterPrefPrelevement();
                    $filter->id_user = User::getCurrentUser()->id;
                    $filter->id_lieu_prelevement = $lieuPrelevement[$i];
                    $filter->id_model = intval($modelExist);

                    if (!$filter->save())
                        $errors = true;
                }
            }

            $model = FilterModel::find()->andFilterWhere(['id'=>$modelExist])->one();
            $modelName = $model->libelle;
        }
        else{
            //On crée le nouveau modèle
            $model = new FilterModel();
            $model->type = FilterModel::TYPE_PRELEVEMENT;
            $model->id_user = User::getCurrentUser()->id;
            $model->libelle = $modelNew;
            if(!$model->save())
                $errors = true;
            if(!$errors) {
                //Pour chaque conditionnement on enregistre en préférence
                if(count($conditionnement) != 0) {
                    for ($i = 0; $i < count($conditionnement); $i++) {
                        $filter = new FilterPrefPrelevement();
                        $filter->id_user = User::getCurrentUser()->id;
                        $filter->id_conditionnement = $conditionnement[$i];
                        $filter->id_model = intval($model->id);

                        if (!$filter->save())
                            $errors = true;
                    }
                }

                //Pour chaque lieu de prélèvement on enregistre en préférence
                if(count($lieuPrelevement) != 0) {
                    for ($i = 0; $i < count($lieuPrelevement); $i++) {
                        $filter = new FilterPrefPrelevement();
                        $filter->id_user = User::getCurrentUser()->id;
                        $filter->id_lieu_prelevement = $lieuPrelevement[$i];
                        $filter->id_model = intval($model->id);

                        if (!$filter->save())
                            $errors = true;
                    }
                }
            }
            $modelName = $modelNew;
        }

        return ['errors'=>$errors,'modelName'=>$modelName];
    }

    /**
     * Fonction de chargement des préférences de filtres sur les prélèvements
     * @return array
     */
    public function actionLoadPrefPrelevement(){
        $errors = false;
        $conditionnementList = [];
        $lieuPrelevementList = [];
        $modelName = '';
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $modelExist = $_data['modelExist'];

        //On charge les filtres pour l'utilisateur, le service donné et le modèle donné
        $prefList = FilterPrefPrelevement::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->andFilterWhere(['id_model'=>$modelExist])->all();

        foreach ($prefList as $pref) {
            if(is_null($pref->id_conditionnement))
                array_push($lieuPrelevementList,$pref->id_lieu_prelevement);
            else
                array_push($conditionnementList,$pref->id_conditionnement);
        }

        $model = FilterModel::find()->andFilterWhere(['id'=>$modelExist])->one();
        $modelName = $model->libelle;


        return ['errors'=>$errors,'lieuPrelevementList'=>$lieuPrelevementList,'conditionnementList'=>$conditionnementList,'modelName'=>$modelName];
    }


    /**
     * Construit de tableau des résultats d'analyses en fonction des filtres
     * @return string
     */
    public function actionGetSyntheseResult(){
        if(isset($_POST['data'])) {
            $_data = Json::decode($_POST['data']);
            $_SESSION['synthese'] = $_POST['data'];
        }
        else{
            $_data = Json::decode($_SESSION['synthese']);
        }

        if(isset($_data['listEtablissement']))
            if($_data['listEtablissement'] != '')
                $listEtablissement = $_data['listEtablissement'];
            else
                $listEtablissement = [];
        else
            $listEtablissement = [];

        //On rempli le tableau des établissements par les identifiants des établissements accessible par l'utilisateur connecté (sauf si admin car accès à tout)
        if(!User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) && !User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) && !Yii::$app->user->isSuperAdmin){
            if(!User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) && !User::getCurrentUser()->hasRole([User::TYPE_LABO_USER]))
                $listEtablissement = PortailUsers::getIdClientUserGroup(User::getCurrentUser()->id);
        }

        if(!User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) && !Yii::$app->user->isSuperAdmin)
            if($_data['listLabo'] != '')
                $listLabo = $_data['listLabo'];
            else
                $listLabo = [];

        $is_delete = false;
        if(Yii::$app->user->isSuperAdmin){
            $is_delete = true;
        }
        else{
            if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_USER]) || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                $is_delete = true;
        }


        $listService = $_data['listService'];
        $listConclusion = $_data['listConclusion'];

        $dateDebut = $_data['dateDebut'];
        $year = substr($dateDebut, 6, 4);
        $month = intval(substr($dateDebut, 3, 2));
        $day = substr($dateDebut, 0, 2);
        $dateDebut = $year . '-' . $month . '-' . $day;

        $dateFin = $_data['dateFin'];
        $year = substr($dateFin, 6, 4);
        $month = intval(substr($dateFin, 3, 2));
        $day = substr($dateFin, 0, 2);
        $dateFin = $year . '-' . $month . '-' . $day;

        $listInterpretation = $_data['listInterpretation'];
        $aKeyWord = $_data['aKeyWord'];
        $listConditionnement = $_data['listConditionnement'];
        $listLieuPrelevement = $_data['listLieuPrelevement'];

        $aGlobalData = [];
        $data = [];

        //Construction des données en fonction des filtres
        //Construction de l'analyse globale (sans notion de germe)
        $aAnalyseData = AnalyseData::find();
        $aAnalyseData = $aAnalyseData->andWhere(['>=','date_analyse',$dateDebut]);
        $aAnalyseData = $aAnalyseData->andWhere(['<=','date_analyse',$dateFin]);

        if(count($listEtablissement) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_client',$listEtablissement]);
        if(!User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) && !Yii::$app->user->isSuperAdmin)
            if(count($listLabo) != 0)
                $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_labo',$listLabo]);
        if(count($listService) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_service',$listService]);
        if(count($listConclusion) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_conformite',$listConclusion]);
        if(count($listInterpretation) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_interpretation',$listInterpretation]);
        if(count($listConditionnement) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_conditionnement',$listConditionnement]);
        if(count($listLieuPrelevement) != 0)
            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_lieu_prelevement',$listLieuPrelevement]);


        $aAnalyseData = $aAnalyseData->orderBy('id_client,id_labo,date_analyse,id_service,num_analyse')->all();

        //Ajout des germes dans la requête
        foreach ($aAnalyseData as $analyseData) {
            if (!isset($data[$analyseData->id])) {
                $data[$analyseData->id]['id'] = $analyseData->id;
                $data[$analyseData->id]['num_analyse'] = $analyseData->num_analyse;
                $data[$analyseData->id]['id_labo'] = $analyseData->id_labo;
                $data[$analyseData->id]['id_client'] = $analyseData->id_client;
                $data[$analyseData->id]['id_parent'] = $analyseData->id_parent;
                $data[$analyseData->id]['id_service'] = $analyseData->id_service;
                $data[$analyseData->id]['prelevements']['id_conditionnement'] = $analyseData->id_conditionnement;
                $data[$analyseData->id]['prelevements']['id_lieu_prelevement'] = $analyseData->id_lieu_prelevement;
                $data[$analyseData->id]['id_interpretation'] = $analyseData->id_interpretation;
                $data[$analyseData->id]['id_conformite'] = $analyseData->id_conformite;
                $data[$analyseData->id]['designation'] = $analyseData->designation;
                $data[$analyseData->id]['commentaire'] = $analyseData->commentaire;
                $data[$analyseData->id]['date_analyse'] = $analyseData->date_analyse;
            }

            //On recherche tous les germes répertoriés sur cette analyse
            $aAnalyseGerme = AnalyseDataGerme::find();
            if(count($aKeyWord) != 0){
                for($i = 0; $i < count($aKeyWord) ; $i++) {
                    if($i == 0){
                        $aAnalyseGerme = $aAnalyseGerme->andWhere(['like','libelle',$aKeyWord[$i]]);
                    }
                    else{
                        $aAnalyseGerme = $aAnalyseGerme->orWhere(['like','libelle',$aKeyWord[$i]]);
                    }
                }
            }
            $aAnalyseGerme = $aAnalyseGerme->andFilterWhere(['id_analyse'=>$analyseData->id]);
            $aAnalyseGerme = $aAnalyseGerme->all();

            //On rajoute au tableau de données générales les données des germes pour chaque analyse
            foreach ($aAnalyseGerme as $germe) {
                //On récupères TOUS les germes d'une analyse qui contient le mot clé recherché
                $aAnalyseGermeFromId = AnalyseDataGerme::find()->andFilterWhere(['id_analyse'=>$germe->id_analyse])->all();
                foreach ($aAnalyseGermeFromId as $item) {
                    $data[$item->id_analyse]['germes'][$item->id]['libelle'] = $item->libelle;
                    $data[$item->id_analyse]['germes'][$item->id]['resultat'] = $item->resultat;
                    $data[$item->id_analyse]['germes'][$item->id]['expression'] = $item->expression;
                    $data[$item->id_analyse]['germes'][$item->id]['interpretation'] = $item->interpretation;
                }
            }
            if(count($aAnalyseGerme) != 0){
                array_push($aGlobalData,$data[$analyseData->id]);
            }
        }


        $dataProvider = new ArrayDataProvider([
            'key'=>function($row) {
                return $row['num_analyse'];
            },
            'allModels' => $aGlobalData,
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        $gridColumns = [
            [
                'label' => 'client',
                'format' => 'raw',
                'contentOptions' => ['style'=>'font-weight:bold'],
                'value' => function($row) {
                    return '<i class="far fa-building"></i>&nbsp;&nbsp;' . Client::find()->andFilterWhere(['id'=>$row['id_client']])->one()->name;
                },
                'vAlign'=>'middle',
                'group'=>true,
                'groupedRow'=>true,                    // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
            ],
            [
                'label' => '',
                'format' => 'raw',
                'contentOptions' => ['style'=>'font-weight:bold'],
                'value' => function($row) {
                    return '<i class="fas fa-microscope"></i>&nbsp;Laboratoire&nbsp;-&nbsp;' . Labo::find()->andFilterWhere(['id'=>$row['id_labo']])->one()->raison_sociale;
                },
                'vAlign'=>'middle',
                'group'=>true,
                'groupedRow'=>true,                    // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-grouped-child-row',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-grouped-child-row', // configure even group cell css class
            ],
            [
                'label' => 'N° Analyse',
                'value' => function($row) {
                    return $row['num_analyse'];
                },
            ],
            [
                'label' => 'Conclusion',
                'format'=>'raw',
                'value' => function($row) {
                    if(is_null($row['id_interpretation']))
                        return '-';
                    else
                        return AnalyseInterpretation::find()->andFilterWhere(['id'=>$row['id_interpretation']])->one()->libelle;
                },
            ],
            [
                'label' => 'Conformité',
                'format'=>'raw',
                'hAlign'=>'center',
                'value' => function($row) {
                    if($row['id_conformite'] == 1){
                        return '<i class="fa fa-circle text-green"></i>';
                    }
                    elseif ($row['id_conformite'] == 2){
                        return '<i class="fa fa-circle text-red"></i>';
                    }
                    else{
                        return '<i class="fa fa-circle text-yellow"></i>';
                    }
                },
            ],
            [
                'label' => 'Service',
                'format' => 'raw',
                'value' => function($row) {
                    return AnalyseService::find()->andFilterWhere(['id'=>$row['id_service']])->one()->libelle;
                },
            ],
            [
                'label' => 'Désignation',
                'format'=>'raw',
                'value' => function($row) {
                    return $row['designation'];
                },
            ],
            [
                'label' => 'Commentaire',
                'format'=>'raw',
                'value' => function($row) {
                    if($row['commentaire'] == '')
                        return '-';
                    else
                        return $row['commentaire'];
                },
            ],
            [
                'label' => 'Date analyse',
                'value' => function($row) {
                    $year = substr($row['date_analyse'], 0, 4);
                    $month = intval(substr($row['date_analyse'], 5, 2));
                    $day = substr($row['date_analyse'], 8, 2);

                    $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                    return $day . ' ' . $tMonths[$month] . ' ' . $year;
                },
            ],
            [
                'class'=>'kartik\grid\ExpandRowColumn',
                'width'=>'50px',
                'value'=>function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail'=>function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_grid-synthese-detail', [
                        'germes'=>$model['germes'],
                        'prelevements'=>$model['prelevements']
                    ]);
                },
                'disabled' => function($model) {
                    return false;
                },
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'expandOneOnly'=>true,
                'detailRowCssClass' => 'primary-content',
                'contentOptions' => function($model) {
                    return ['id' => $model['num_analyse']];
                },
            ],
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'checkboxOptions' => function($model, $key, $index, $column) {
                    return [
                        'class' => 'chk-'.$model['id']. ' chk-data',
                        'id' => $model['id'],
                    ];
                },
                'pageSummary' => Html::button(\kartik\helpers\Html::icon('save'), ['class' => '', 'id' => 'js-update-doc-ligne']),
                'visible' => $is_delete ? true : false
            ]
        ];

        return $this->renderAjax('grid-synthese', [
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns
        ]);
    }

    public function actionDeleteAnalyse(){
        $errors = false;
        $conditionnementList = [];
        $lieuPrelevementList = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $_data = Json::decode($_POST['data']);
        $idAnalyse = $_data['idAnalyse'];
        Yii::trace($idAnalyse);

        if(count($idAnalyse) != 0) {
            try {
                for($i = 0; $i < count($idAnalyse); $i++) {
                    $analyse = AnalyseData::find()->andFilterWhere(['id' => $idAnalyse[$i]])->one();

                    $transaction = AnalyseData::getDb()->beginTransaction();

                    if (!is_null($analyse)) {
                        //On supprime les germes pour cette analyse
                        $germeList = AnalyseDataGerme::find()->andFilterWhere(['id_analyse' => $analyse->id])->all();
                        if (count($germeList) != 0) {
                            foreach ($germeList as $germe) {
                                if (!$germe->delete())
                                    $errors = true;
                            }
                        }

                        if (!$errors) {
                            //On supprime l'analyse
                            if (!$analyse->delete())
                                $errors = true;
                        }

                        if (!$errors)
                            $transaction->commit();
                    }
                }
            } catch (\yii\db\IntegrityException $e) {
                $transaction->rollBack();
                $errors = true;
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                $errors = true;
            }
        }


        return ['errors'=>$errors];
    }
}
