<?php

/**
 * Created by PhpStorm.
 * User: jmaratier
 * Date: 19/05/2017
 * Time: 16:51
 */
namespace app\controllers\UserManagement;

use app\controllers\ApiController;
use app\models\salve\SalveColumnConfig;
use webvimark\components\BaseController;
use webvimark\modules\UserManagement\components\UserAuthEvent;
use webvimark\modules\UserManagement\models\forms\ChangeOwnPasswordForm;
use webvimark\modules\UserManagement\models\forms\ConfirmEmailForm;
use app\models\LoginForm;
use app\models\User;
use app\models\salve\SalveLicence;
use webvimark\modules\UserManagement\UserManagementModule;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class AuthController extends BaseController
{
    /**
     * @var array
     */
    public $freeAccessActions = ['login', 'logout', 'confirm-registration-email', 'password-recovery', 'password-recovery-receive'];

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => Yii::$app->getModule('user-management')->captchaOptions,
        ];
    }

    /**
     * Login form
     *
     * @return string
     */
    public function actionLogin($error = null)
    {
        if ( !Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ( Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->login() ) {
            //Récupération de la licence
            //$licenceSalve = SalveLicence::find()->one();

            //$infoLicence = ApiController::getInfosLicence($licenceSalve->licence);

            //$isValid = ApiController::isLicenceValid($licenceSalve->licence);

            //if (!$isValid)
                //return $this->redirect(['/user-management/auth/logout', 'error' => 'Autorisation expirée']);
            //else {
                //Yii::$app->session->set('__licence.info', $infoLicence);
                if(isset(Yii::$app->request->post()['alerte-link']) && Yii::$app->request->post()['alerte-link'] != '')
                    return $this->redirect([Yii::$app->request->baseUrl.'/alerte/change-statut','alerte'=>Yii::$app->request->post()['alerte-link']]);
                else
                    return $this->redirect('/index.php');
            //}
//            $user = User::getCurrentUser();
//
//            if($user->superadmin){
//                Yii::$app->user->setLicenceValid(true);
//                return $this->redirect('/client/index');
//            }
//            else {
//                if (User::getUserLevel($user->id) < User::LEVEL_CLIENT_ADMIN) {
//                    //User multiclient (superadmin,global admin, global consultant)
//                    Yii::$app->user->setLicenceValid(true);
//                    return $this->redirect('/client/index');
//                } else {
//                    if (User::getUserLevel($user->id) == User::LEVEL_CLIENT_ADMIN || User::getUserLevel($user->id) == User::LEVEL_CLIENT_CONSULTANT) {
//                        //User multisociété (client admin et client consultant)
//                        if ($user->getAssignClient($user->id, false, false)->active) {
//                            //Si le client est actif
//                            Yii::$app->client->current = $user->getLastClient();
//
//                            if (Yii::$app->client->current->isLicenceValid())
//                                Yii::$app->user->setLicenceValid(true);
//                            else {
//                                Yii::$app->user->setLicenceValid(false);
//
//                                $error = Yii::t('app', 'Licence Invalid action');
//                                return $this->redirect(['/user-management/auth/logout', 'error' => $error]);
//                            }
//
//                            return $this->redirect('/societe/index');
//                        } else {
//                            //Si le client est inactif
//                            $error = Yii::t('app','ClientInactive');
//                            return $this->redirect(['/user-management/auth/logout','error'=>$error]);
//                        }
//
//                    } else {
//                        //User de type client client
//                        if ($user->getAssignClient($user->id, false, false)->active) {
//                            //Client actif
//                            Yii::$app->client->current = $user->getLastClient();
//
//                            if (Yii::$app->client->current->isLicenceValid())
//                                Yii::$app->user->setLicenceValid(true);
//                            else {
//                                Yii::$app->user->setLicenceValid(false);
//
//                                $error = Yii::t('app','Licence Invalid action');
//                                return $this->redirect(['/user-management/auth/logout','error'=>$error]);
//                            }
//
//                            if ($user->getAssignSociety($user->id_user_client, false, false)->active) {
//                                //Société active
//                                Yii::$app->societe->current = Societe::findOne(['id' => User_societe::findOne(['id_user' => $user->id_user_client])->id_societe]);
//                                $this->goBack();
//                            } else {
//                                //Société inactive
//                                $error = Yii::t('app','SocieteInactive');
//                                return $this->redirect(['/user-management/auth/logout','error'=>$error]);
//                            }
//                        }
//                        else {
//                            //client inactif
//                            $error = Yii::t('app','ClientInactive');
//                            return $this->redirect(['/user-management/auth/logout','error'=>$error]);
//                        }
//                    }
//                }
//            }
        }

        return $this->renderIsAjax('login', compact('model','error'));
    }

    /**
     * Logout and redirect to home page
     */
    public function actionLogout($error = null)
    {
//        Yii::$app->user->reset();
        Yii::$app->user->logout();

        return $this->redirect(['/user-management/auth/login','error'=>$error]);
    }

    /**
     * Change your own password
     *
     * @throws \yii\web\ForbiddenHttpException
     * @return string|\yii\web\Response
     */
    public function actionChangeOwnPassword($profile = false)
    {
        if ( Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $user = User::getCurrentUser();

        if ( $user->status != User::STATUS_ACTIVE )
        {
            throw new ForbiddenHttpException();
        }

        $model = new ChangeOwnPasswordForm(['user'=>$user]);


        if ( Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->changePassword() )
        {
            if($profile) {
                Yii::$app->session->addFlash('success', 'Votre mot de passe a bien été modifié.');
                return $this->redirect(['/user-management/user/profile-view']);
            }
            else{
                return $this->renderIsAjax('changeOwnPasswordSuccess');
            }
        }

        return $this->renderIsAjax('changeOwnPassword', compact('model'));
    }

    /**
     * Registration logic
     *
     * @return string
     */
    public function actionRegistration()
    {
        if ( !Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $model = new $this->module->registrationFormClass;


        if ( Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post()) )
        {

            Yii::$app->response->format = Response::FORMAT_JSON;

            // Ajax validation breaks captcha. See https://github.com/yiisoft/yii2/issues/6115
            // Thanks to TomskDiver
            $validateAttributes = $model->attributes;
            unset($validateAttributes['captcha']);

            return ActiveForm::validate($model, $validateAttributes);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->validate() )
        {
            // Trigger event "before registration" and checks if it's valid
            if ( $this->triggerModuleEvent(UserAuthEvent::BEFORE_REGISTRATION, ['model'=>$model]) )
            {
                $user = $model->registerUser(false);

                // Trigger event "after registration" and checks if it's valid
                if ( $this->triggerModuleEvent(UserAuthEvent::AFTER_REGISTRATION, ['model'=>$model, 'user'=>$user]) )
                {
                    if ( $user )
                    {
                        if ( Yii::$app->getModule('user-management')->useEmailAsLogin AND Yii::$app->getModule('user-management')->emailConfirmationRequired )
                        {
                            return $this->renderIsAjax('registrationWaitForEmailConfirmation', compact('user'));
                        }
                        else
                        {
                            $roles = (array)$this->module->rolesAfterRegistration;

                            foreach ($roles as $role)
                            {
                                User::assignRole($user->id, $role);
                            }

                            Yii::$app->user->login($user);

                            return $this->redirect(Yii::$app->user->returnUrl);
                        }

                    }
                }
            }

        }

        return $this->renderIsAjax('registration', compact('model'));
    }


    /**
     * Receive token after registration, find user by it and confirm email
     *
     * @param string $token
     *
     * @throws \yii\web\NotFoundHttpException
     * @return string|\yii\web\Response
     */
    public function actionConfirmRegistrationEmail($token)
    {
        if ( Yii::$app->getModule('user-management')->useEmailAsLogin AND Yii::$app->getModule('user-management')->emailConfirmationRequired )
        {
            $model = new $this->module->registrationFormClass;

            $user = $model->checkConfirmationToken($token);

            if ( $user )
            {
                return $this->renderIsAjax('confirmEmailSuccess', compact('user'));
            }

            throw new NotFoundHttpException(UserManagementModule::t('front', 'Token not found. It may be expired'));
        }
    }


    /**
     * Form to recover password
     *
     * @return string|\yii\web\Response
     */
    public function actionPasswordRecovery()
    {
        if ( !Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $model = new PasswordRecoveryForm();

        if ( Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Ajax validation breaks captcha. See https://github.com/yiisoft/yii2/issues/6115
            // Thanks to TomskDiver
            $validateAttributes = $model->attributes;
            unset($validateAttributes['captcha']);

            return ActiveForm::validate($model, $validateAttributes);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->validate() )
        {
            if ( $this->triggerModuleEvent(UserAuthEvent::BEFORE_PASSWORD_RECOVERY_REQUEST, ['model'=>$model]) )
            {
                if ( $model->sendEmail(false) )
                {
                    if ( $this->triggerModuleEvent(UserAuthEvent::AFTER_PASSWORD_RECOVERY_REQUEST, ['model'=>$model]) )
                    {
                        return $this->renderIsAjax('passwordRecoverySuccess');
                    }
                }
                else
                {
                    Yii::$app->session->setFlash('error', UserManagementModule::t('front', "Unable to send message for email provided"));
                }
            }
        }

        return $this->renderIsAjax('passwordRecovery', compact('model'));
    }

    /**
     * Receive token, find user by it and show form to change password
     *
     * @param string $token
     *
     * @throws \yii\web\NotFoundHttpException
     * @return string|\yii\web\Response
     */
    public function actionPasswordRecoveryReceive($token)
    {
        if ( !Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $user = User::findByConfirmationToken($token);

        if ( !$user )
        {
            throw new NotFoundHttpException(UserManagementModule::t('front', 'Token not found. It may be expired. Try reset password once more'));
        }

        $model = new ChangeOwnPasswordForm([
            'scenario'=>'restoreViaEmail',
            'user'=>$user,
        ]);

        if ( $model->load(Yii::$app->request->post()) AND $model->validate() )
        {
            if ( $this->triggerModuleEvent(UserAuthEvent::BEFORE_PASSWORD_RECOVERY_COMPLETE, ['model'=>$model]) )
            {
                $model->changePassword(false);

                if ( $this->triggerModuleEvent(UserAuthEvent::AFTER_PASSWORD_RECOVERY_COMPLETE, ['model'=>$model]) )
                {
                    return $this->renderIsAjax('changeOwnPasswordSuccess');
                }
            }
        }

        return $this->renderIsAjax('passwordRecoveryReceive', compact('model'));
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionConfirmEmail()
    {
        if ( Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $user = User::getCurrentUser();

        if ( $user->email_confirmed == 1 )
        {
            return $this->renderIsAjax('confirmEmailSuccess', compact('user'));
        }

        $model = new ConfirmEmailForm([
            'email'=>$user->email,
            'user'=>$user,
        ]);

        if ( Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->validate() )
        {
            if ( $this->triggerModuleEvent(UserAuthEvent::BEFORE_EMAIL_CONFIRMATION_REQUEST, ['model'=>$model]) )
            {
                if ( $model->sendEmail(false) )
                {
                    if ( $this->triggerModuleEvent(UserAuthEvent::AFTER_EMAIL_CONFIRMATION_REQUEST, ['model'=>$model]) )
                    {
                        return $this->refresh();
                    }
                }
                else
                {
                    Yii::$app->session->setFlash('error', UserManagementModule::t('front', "Unable to send message for email provided"));
                }
            }
        }

        return $this->renderIsAjax('confirmEmail', compact('model'));
    }

    /**
     * Receive token, find user by it and confirm email
     *
     * @param string $token
     *
     * @throws \yii\web\NotFoundHttpException
     * @return string|\yii\web\Response
     */
    public function actionConfirmEmailReceive($token)
    {
        $user = User::findByConfirmationToken($token);

        if ( !$user )
        {
            throw new NotFoundHttpException(UserManagementModule::t('front', 'Token not found. It may be expired'));
        }

        $user->email_confirmed = 1;
        $user->removeConfirmationToken();
        $user->save(false);

        return $this->renderIsAjax('confirmEmailSuccess', compact('user'));
    }

    /**
     * Universal method for triggering events like "before registration", "after registration" and so on
     *
     * @param string $eventName
     * @param array  $data
     *
     * @return bool
     */
    protected function triggerModuleEvent($eventName, $data = [])
    {
        $event = new UserAuthEvent($data);

        $this->module->trigger($eventName, $event);

        return $event->isValid;
    }
}