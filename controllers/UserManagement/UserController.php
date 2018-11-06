<?php

namespace app\controllers\UserManagement;

use app\models\PortailUsers;
use app\models\User;
use app\models\Client;
use webvimark\components\AdminDefaultController;
use Yii;
use webvimark\modules\UserManagement\models\search\UserSearch;
use yii\helpers\Inflector;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AdminDefaultController
{
	/**
	 * @var User
	 */
	public $modelClass = 'app\models\User';

	/**
	 * @var UserSearch
	 */
	public $modelSearchClass = 'app\models\UserSearch';

	/**
	 * Displays a single model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);

        return $this->renderIsAjax('view', [
            'model' => $model,
        ]);
	}

	/**
	 * @return mixed|string|\yii\web\Response
	 */
	public function actionCreate()
	{
		$model = new User(['scenario'=>'newUser']);
        $idClient = null;
        if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) && !Yii::$app->user->isSuperadmin){
            $portail = PortailUsers::find()->andFilterWhere(['id_user'=> User::getCurrentUser()])->one();
            $idClient = $portail->id_client;
        }

		if ($model->load(Yii::$app->request->post()))
		{
            if (User::findOne(['username' => Yii::$app->request->post()['User']['username']])) {
                Yii::$app->session->addFlash('danger', 'Un Utilisateur avec cet identifiant existe déjà');
                return $this->render('create',['model' => $model]);
            }

            if(isset(Yii::$app->request->post()['radioPermission'])){
                //Test de choix sur les listes déroulantes
                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                    if (Yii::$app->request->post()['radioPermission'] != User::TYPE_PORTAIL_ADMIN) {
                        if (Yii::$app->request->post()['radioPermission'] == User::TYPE_LABO_ADMIN || Yii::$app->request->post()['radioPermission'] == User::TYPE_LABO_USER) {
                            if (intval(Yii::$app->request->post()['paramLabo']) == 0 || Yii::$app->request->post()['paramLabo'] == '') {
                                Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLLabo'));
                                return $this->renderIsAjax('create', ['model' => $model,'idClient'=>$idClient]);
                            }
                        } else {
                            if (Yii::$app->request->post()['radioPermission'] == User::TYPE_CLIENT_USER){
                                if (intval(Yii::$app->request->post()['etablissement']) == 0 || Yii::$app->request->post()['paramClient'] == '') {
                                    Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLEtablissement'));
                                    return $this->renderIsAjax('create', ['model' => $model,'idClient'=>$idClient]);
                                }
                            }
                            else{
                                if(Yii::$app->request->post()['radioPermission'] == User::TYPE_CLIENT_USER_GROUP){
                                    if (!isset(Yii::$app->request->post()['etablissementgroup']) || Yii::$app->request->post()['paramClient'] == '') {
                                        Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLEtablissementGroup'));
                                        return $this->renderIsAjax('create', ['model' => $model,'idClient'=>$idClient]);
                                    }
                                }
                                else {
                                    if (intval(Yii::$app->request->post()['paramClient']) == 0 || Yii::$app->request->post()['paramClient'] == '') {
                                        Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLClient'));
                                        return $this->renderIsAjax('create', ['model' => $model, 'idClient' => $idClient]);
                                    }
                                }
                            }
                        }
                    }
                }

                $createUser = $model->createUserWithPermission(Yii::$app->request->post());
                if ($createUser) {
                    Yii::$app->session->setFlash('success', Yii::t('microsept', 'UserCreateSuccess'));
                    return $this->redirect(['index']);
                } else
                    Yii::$app->session->setFlash('danger', Yii::t('microsept', 'UserCreateError'));
            }
		}

		return $this->renderIsAjax('create',['model'=> $model,'idClient'=>$idClient]);
	}

	/**
	 * Updates an existing model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
        $idClient = null;
        $idLabo = null;
        $idEtablissement = null;
        $listEtablissement = [];

        if(User::isLaboAdmin($model->id) || User::isLaboUser($model->id))
            $idLabo = PortailUsers::find()->andFilterWhere(['id_user'=>$model->id])->one()->id_labo;

        if(User::isClientAdmin($model->id))
            $idClient = PortailUsers::find()->andFilterWhere(['id_user'=>$model->id])->one()->id_client;
        if(User::isClientUser($model->id)){
            $idEtablissement = PortailUsers::find()->andFilterWhere(['id_user'=>$model->id])->one()->id_client;
            $client = Client::find()->andFilterWhere(['id'=>$idEtablissement])->one();
            $idClient = $client->id_parent;
        }
        if(User::isClientUserGroup($model->id)){
            $oneEtablissement = null;
            $aEtablissement = PortailUsers::find()->andFilterWhere(['id_user'=>$model->id])->all();
            foreach ($aEtablissement as $item) {
                $oneEtablissement = $item->id_client;
                array_push($listEtablissement,$item->id_client);
            }
            $client = Client::find()->andFilterWhere(['id'=>$oneEtablissement])->one();
            $idClient = $client->id_parent;
        }

        $role = User::getRole($model->id);

        if ($model->load(Yii::$app->request->post()))
        {
            if($model->username != Yii::$app->request->post()['User']['username']) {
                if (User::findOne(['username' => Yii::$app->request->post()['User']['username']])) {
                    Yii::$app->session->addFlash('danger', 'Un Utilisateur avec cet identifiant existe déjà');
                    return $this->render('update', ['model' => $model,'id'=>$model->id]);
                }
            }

            if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                if (isset(Yii::$app->request->post()['radioPermission'])) {
                    //Test de choix sur les listes déroulantes
                    if (Yii::$app->request->post()['radioPermission'] != User::TYPE_PORTAIL_ADMIN) {
                        if (Yii::$app->request->post()['radioPermission'] == User::TYPE_LABO_ADMIN || Yii::$app->request->post()['radioPermission'] == User::TYPE_LABO_USER) {
                            if (intval(Yii::$app->request->post()['paramLabo']) == 0 || Yii::$app->request->post()['paramLabo'] == '') {
                                Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLLabo'));
                                if(User::isPortailAdmin($model->id)){
                                    return $this->renderIsAjax('update', ['model'=>$model,'id'=>$model->id, 'assignment' => User::getUserAssignment($model->id),'modifadmin'=>true]);
                                }
                                else {
                                    if (User::isLaboAdmin($model->id) || User::isLaboUser($model->id))
                                        return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idLabo' => $idLabo, 'assignment' => User::getUserAssignment($model->id)]);
                                    else {
                                        if(User::isClientAdmin($model->id))
                                            return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient, 'assignment' => User::getUserAssignment($model->id)]);
                                        else
                                            return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient,'idEtablissement'=>$idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => User::getUserAssignment($model->id)]);
                                    }
                                }
                            }
                        } else {
                            if (intval(Yii::$app->request->post()['paramClient']) == 0 || Yii::$app->request->post()['paramClient'] == '') {
                                Yii::$app->session->setFlash('warning', Yii::t('microsept', 'UserCreateDDLClient'));
                                if(User::isPortailAdmin($model->id)){
                                    return $this->renderIsAjax('update', ['model'=>$model,'id'=>$model->id, 'assignment' => User::getUserAssignment($model->id),'modifadmin'=>true]);
                                }
                                else {
                                    if (User::isLaboAdmin($model->id) || User::isLaboUser($model->id))
                                        return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idLabo' => $idLabo, 'assignment' => User::getUserAssignment($model->id)]);
                                    else {
                                        if(User::isClientAdmin($model->id))
                                            return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient, 'assignment' => User::getUserAssignment($model->id)]);
                                        else
                                            return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient,'idEtablissement'=>$idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => User::getUserAssignment($model->id)]);
                                    }
                                }
                            }
                        }
                    }

                    $updateUser = $model->updateUserWithPermission(Yii::$app->request->post(), $role);
                    if ($updateUser) {
                        Yii::$app->session->setFlash('success', Yii::t('microsept', 'UserUpdateSuccess'));
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('danger', Yii::t('microsept', 'UserCreateError'));
                    }
                }
            }
            else{
                if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
                    $updateUser = $model->updateUserWithPermission(Yii::$app->request->post(), $role);
                    if ($updateUser) {
                        Yii::$app->session->setFlash('success', Yii::t('microsept', 'UserUpdateSuccess'));
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('danger', Yii::t('microsept', 'UserCreateError'));
                    }
                }
                else{
                    $updateUser = $model->updateUserWithPermission(Yii::$app->request->post(), $role);
                    if ($updateUser) {
                        Yii::$app->session->setFlash('success', Yii::t('microsept', 'UserUpdateSuccess'));
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('danger', Yii::t('microsept', 'UserCreateError'));
                    }
                }
            }
        }

		if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
		    if(User::isPortailAdmin($model->id)){
                return $this->renderIsAjax('update', ['model'=>$model,'id'=>$model->id, 'assignment' => User::getUserAssignment($model->id),'modifadmin'=>true]);
            }
            else {
                if (User::isLaboAdmin($model->id) || User::isLaboUser($model->id))
                    return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idLabo' => $idLabo, 'assignment' => User::getUserAssignment($model->id)]);
                else {
                    if(User::isClientAdmin($model->id))
                        return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient,'listEtablissement'=>$listEtablissement, 'assignment' => User::getUserAssignment($model->id)]);
                    else {
                        return $this->renderIsAjax('update', ['model' => $model, 'id' => $model->id, 'idClient' => $idClient, 'idEtablissement' => $idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => User::getUserAssignment($model->id)]);
                    }
                }
            }
        }
        else{
            if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
                return $this->renderIsAjax('update', ['model'=>$model,'id'=>$model->id,'idLabo'=>$idLabo, 'idClient' => $idClient,'idEtablissement'=>$idEtablissement,'listEtablissement'=>$listEtablissement, 'assignment' => User::getUserAssignment($model->id)]);
            }
            else{
                return $this->renderIsAjax('update', ['model'=>$model,'id'=>$model->id]);
            }
        }
	}

	/**
	 * Deletes an existing model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel($id);
		$result = $model->deleteUserWithPermission($id);


		$redirect = $this->getRedirectPage('delete', $model);

		return $redirect === false ? '' : $this->redirect($redirect);
	}


	/**
	 * @param int $id User ID
	 *
	 * @throws \yii\web\NotFoundHttpException
	 * @return string
	 */
	public function actionChangePassword($id)
	{
		$model = User::findOne($id);

		if ( !$model )
		{
			throw new NotFoundHttpException('User not found');
		}

        if (!Yii::$app->user->isSuperadmin && Yii::$app->user->id == $id)
            throw new ForbiddenHttpException('Vous n\'êtes pas autorisé à effectuer cette action. ');

		$model->scenario = 'changePassword';

		if ( $model->load(Yii::$app->request->post()) && $model->save() )
		{
			return $this->redirect(['view',	'id' => $model->id]);
		}

		return $this->renderIsAjax('changePassword', compact('model'));
	}

	/**
	 * renvoie la liste des sociétés sous forme de tableau json
	 */
	public function actionLoadSocieteList(){
		$client_name = $_POST['client_name'];
		$connection = Yii::$app->db;
		$connection->createCommand('USE `'. Yii::$app->multidb->suffix . Inflector::slug($client_name) .'`;')->execute();

		$societes = $connection->createCommand("SELECT id,name FROM `societe` ;")->queryAll();
		$result = array();

		foreach ($societes as $societe) {
			array_push($result,[$societe['id'],$societe['name']]);
		}

		return json_encode($result)	;
	}


	/**
	 * Affichage de la page de profil
	 * @return string|\yii\web\Response
	 */
	public function actionProfileView(){

		$model = User::getCurrentUser();

		return $this->renderIsAjax('profile',[
			'model' => $model,
		]);
	}

	public function actionChangeEmail(){
		$model = User::getCurrentUser();

		if ( !$model )
		{
			throw new NotFoundHttpException('User not found');
		}

		if ( $model->load(Yii::$app->request->post()) && $model->save() )
		{
			Yii::$app->session->addFlash('success', 'Votre e-mail a bien été modifiée.');
			return $this->redirect(['/user-management/user/profile-view']);
		}

		return $this->renderIsAjax('changeEmail',[
			'model' => $model,
		]);
	}

}
