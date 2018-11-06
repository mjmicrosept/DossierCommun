<?php

use app\models\User;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('microsept', 'Editing user') . ' : ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Editing');
?>
<div class="user-update">

	<div class="panel panel-primary">
		<div class="panel-heading">
            <h4><?= $this->title ?></h4>
		</div>

		<div class="panel-body">
			<?php
				if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
				    if(User::isPortailAdmin($model->id)){
                        echo $this->render('_form', ['model'=>$model,'id'=>$id, 'assignment' => $assignment,'modifadmin'=>$modifadmin]);
                    }
                    else {
                        if (isset($idLabo))
                            echo $this->render('_form', ['model' => $model, 'id' => $id, 'idLabo' => $idLabo, 'assignment' => $assignment]);
                        else
                            if(isset($idEtablissement)) {
                                echo $this->render('_form', ['model' => $model, 'id' => $id, 'idClient' => $idClient,'idEtablissement'=>$idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => $assignment]);
                            }
                            else
                                echo $this->render('_form', ['model' => $model, 'id' => $id, 'idClient' => $idClient,'listEtablissement'=>$listEtablissement, 'assignment' => $assignment]);
                    }
                }
				else{
                    if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]))
                        echo $this->render('_form', ['model'=>$model,'id'=>$id,'idLabo'=>$idLabo,'idClient'=>$idClient,'idEtablissement'=>$idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => $assignment]);
                    else
                        echo $this->render('_form', ['model'=>$model,'id'=>$id]);
                }
			?>
		</div>
	</div>

</div>