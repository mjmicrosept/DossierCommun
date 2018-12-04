<?php

namespace app\models;

use webvimark\modules\UserManagement\components\AuthHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\rbac\Assignment;
use yii\db\Query;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $email_confirmed
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property string $bind_to_ip
 * @property string $registration_ip
 * @property integer $status
 * @property integer $superadmin
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \webvimark\modules\UserManagement\models\User
{

    const LEVEL_SUPERADMIN = 1;
    const LEVEL_PORTAIL_ADMIN = 2;
    const LEVEL_LABO_ADMIN = 3;
    const LEVEL_LABO_USER = 4;
    const LEVEL_CLIENT_ADMIN = 5;
    const LEVEL_CLIENT_USER = 6;
    const LEVEL_CLIENT_USER_GROUP = 7;

    const TYPE_SUPERADMIN = 'superadmin';
    const TYPE_PORTAIL_ADMIN = 'portail_admin';
    const TYPE_LABO_ADMIN = 'labo_admin';
    const TYPE_LABO_USER = 'labo_user';
    const TYPE_CLIENT_ADMIN = 'client_admin';
    const TYPE_CLIENT_USER = 'client_user';
    const TYPE_CLIENT_USER_GROUP = 'client_user_group';

    public static $aAssignmentType = [
        self::TYPE_SUPERADMIN => self::LEVEL_SUPERADMIN,
        self::TYPE_PORTAIL_ADMIN  => self::LEVEL_PORTAIL_ADMIN,
        self::TYPE_LABO_ADMIN  => self::LEVEL_LABO_ADMIN,
        self::TYPE_LABO_USER => self::LEVEL_LABO_USER,
        self::TYPE_CLIENT_ADMIN => self::LEVEL_CLIENT_ADMIN,
        self::TYPE_CLIENT_USER => self::LEVEL_CLIENT_USER,
        self::TYPE_CLIENT_USER_GROUP => self::LEVEL_CLIENT_USER_GROUP
    ];


    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getClient()
    {
        $portailUser = PortailUsers::find()->andFilterWhere(['id_user'=>$this->id])->one();
        if(!is_null($portailUser))
            return Client::find()->andFilterWhere(['id'=>$portailUser->id_client])->one();
        else
            return null;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getLabo(){
        $portailUser = PortailUsers::find()->andFilterWhere(['id_user'=>$this->id])->one();
        if(!is_null($portailUser))
            return Labo::find()->andFilterWhere(['id'=>$portailUser->id_labo])->one();
        else
            return null;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getPortalAssign()
    {
        return PortailUsers::find()->andFilterWhere(['id_user'=>$this->id])->one();
    }




    /**
     * @return Client
     */
    public function getLastClient() {
        if(!is_null($this->client))
            return $this->client[0];
        else
            return null;
    }

    /**
     * Get assignments for user
     *
     * @param int $userId
     * @return array
     */
    public function getAssignments($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query)
            ->from(Yii::$app->getModule('user-management')->auth_assignment_table)
            ->where(['user_id' => (string) $userId]);

        $assignments = [];
        foreach ($query->all() as $row) {
            $assignments[$row['item_name']] = new Assignment([
                'userId' => $row['user_id'],
                'roleName' => $row['item_name'],
                'createdAt' => $row['created_at'],
            ]);
        }

        return $assignments;
    }

    public static function getUserAssignment($userId){
        if(empty($userId)){
            return null;
        }
        $assignment = (new Query)
            ->from(Yii::$app->getModule('user-management')->auth_assignment_table)
            ->where(['user_id' => (string) $userId])->one();
        if(is_null($assignment))
            return null;
        else
            return $assignment['item_name'];
    }

    public function getLevel() {
        return self::$aAssignmentType[self::getUserAssignment($this->id)];
    }

    public static function getUserLevel($userId) {
        return self::$aAssignmentType[self::getUserAssignment($userId)];
    }

    private function isRole($userId, $role) {
        //Get list of assignments for this user
        $assignments = self::getAssignments(($userId));
        foreach($assignments as $assignment){
            if($assignment->roleName === Yii::$app->params[$role])
                return true;
        }
        return false;
    }

    public static function getRole($userId) {
        //Get list of assignments for this user
        $assignments = self::getAssignments(($userId));
        foreach($assignments as $assignment){
            return $assignment->roleName;
        }
        return null;
    }

    /**
     * Determines whether the user is a superadmin
     *
     * @param int $userId
     * @return bool
     */
    public static function isSuperAdmin($userId){
        return self::isRole($userId,'roleSuperAdmin');
    }

    /**
     * Determines whether the user is a portail admin
     *
     * @param int $userId
     * @return bool
     */
    public static function isPortailAdmin($userId){
        return self::isRole($userId,'rolePortailAdmin');
    }
    /**
     * Determines whether the user is a labo admin
     *
     * @param int $userId
     * @return bool
     */
    public static function isLaboAdmin($userId){
        return self::isRole($userId,'roleLaboAdmin');
    }
    /**
     * Determines whether the user is a labo user
     *
     * @param int $userId
     * @return bool
     */
    public static function isLaboUser($userId){
        return self::isRole($userId,'roleLaboUser');
    }

    /**
     * Determines whether the user is a client admin
     *
     * @param int $userId
     * @return bool
     */
    public static function isClientAdmin($userId){
        return self::isRole($userId,'roleClientAdmin');
    }

    /**
     * Determines whether the user is a client user
     *
     * @param int $userId
     * @return bool
     */
    public static function isClientUser($userId){
        return self::isRole($userId,'roleClientUser');
    }

    /**
     * Determines whether the user is a client user group
     *
     * @param int $userId
     * @return bool
     */
    public static function isClientUserGroup($userId){
        return self::isRole($userId,'roleClientUserGroup');
    }

    /**
     * Assign role to user
     *
     * @param int  $userId
     * @param string $roleName
     * @return bool
     * @throws \Exception $e
     */
    public static function assignRole($userId, $roleName)
    {
        try
        {
            Yii::$app->db->createCommand()
                ->insert(Yii::$app->getModule('user-management')->auth_assignment_table, [
                    'user_id' => $userId,
                    'item_name' => $roleName,
                    'created_at' => time(),
                ])->execute();

            AuthHelper::invalidatePermissions();

            return true;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Crée l'utilisateur et ses permissions associées
     * @param $request_post
     * @return bool
     * @throws \yii\db\Exception
     */
    public function createUserWithPermission($request_post){
        $transaction = self::getDb()->beginTransaction();
        $error = false;
        try{
            if(!$this->save())
                $error = true;
            switch($request_post['radioPermission']){
                //Dans le cas d'un admin du portail ses droits sont complets sur tous les labos et clients
                case User::TYPE_PORTAIL_ADMIN:
                    if (!User::assignRole($this->id, User::TYPE_PORTAIL_ADMIN))
                        $error = true;
                    break;
                //Dans le cas d'un Admin de labo il faut lui affecter les droits admin sur SON labo
                case User::TYPE_LABO_ADMIN:
                    if (!User::assignRole($this->id, User::TYPE_LABO_ADMIN))
                        $error = true;
                    else
                    {
                        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                            PortailUsers::createNewEntry($this->id, intval($request_post['paramLabo']), null);
                        }
                        else {
                            //On récupère le labo de la personne connectée
                            $idLabo = PortailUsers::getIdLaboUser(User::getCurrentUser()->id);
                            PortailUsers::createNewEntry($this->id, $idLabo, null);
                        }
                    }
                    break;
                //Dans le cas d'un user de labo il faut lui affecter les droits client sur SON labo
                case User::TYPE_LABO_USER:
                    if (!User::assignRole($this->id, User::TYPE_LABO_USER))
                        $error = true;
                    else
                    {
                        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                            PortailUsers::createNewEntry($this->id,intval($request_post['paramLabo']),null);
                        else {
                            //On récupère le labo de la personne connectée
                            $idLabo = PortailUsers::getIdLaboUser(User::getCurrentUser()->id);
                            PortailUsers::createNewEntry($this->id, $idLabo, null);
                        }
                    }
                    break;
                //Dans le cas d'un Admin de client il faut lui affecter les droits admin sur SON client
                case User::TYPE_CLIENT_ADMIN:
                    if (!User::assignRole($this->id, User::TYPE_CLIENT_ADMIN))
                        $error = true;
                    else
                    {
                        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                            PortailUsers::createNewEntry($this->id,null,intval($request_post['paramClient']));
                        else {
                            //On récupère le labo de la personne connectée
                            $idClient = PortailUsers::getIdClientUser(User::getCurrentUser()->id);
                            PortailUsers::createNewEntry($this->id, null, $idClient);
                        }
                    }
                    break;
                //Dans le cas d'un user de client il faut lui affecter les droits user sur SON client
                case User::TYPE_CLIENT_USER:
                    if (!User::assignRole($this->id, User::TYPE_CLIENT_USER))
                        $error = true;
                    else
                    {
                        PortailUsers::createNewEntry($this->id,null,intval($request_post['etablissement']));
                    }
                    break;
                //Dans le cas d'un  user group de client il faut lui affecter les droits user sur SES clients (plutôt établissements)
                case User::TYPE_CLIENT_USER_GROUP:
                    if (!User::assignRole($this->id, User::TYPE_CLIENT_USER_GROUP))
                        $error = true;
                    else
                    {
                        //On crée une entrée pour chaque établissement
                        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                            for($i = 0; $i < count($request_post['etablissementgroup']);$i++){
                                PortailUsers::createNewEntry($this->id,null,intval($request_post['etablissementgroup'][$i]));
                            }
                        }
                        else{
                            for($i = 0; $i < count($request_post['kvformadmin']['etablissement']);$i++){
                                PortailUsers::createNewEntry($this->id,null,intval($request_post['kvformadmin']['etablissement'][$i]));
                            }
                        }
                    }
                    break;
            }
            if(!$error)
                $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $error = true;
        }

        return !$error;
    }


    /**
     * Met à jour l'utilisateur et ses permissions associées
     * @param $request_post
     * @param $old_role
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function updateUserWithPermission($request_post,$old_role){
        $transaction = self::getDb()->beginTransaction();
        $error = false;
        try{
            if(!$this->save())
                $error = true;
            switch($request_post['radioPermission']){
                //Dans le cas d'un admin du portail ses droits sont complets sur tous les labos et clients
                case User::TYPE_PORTAIL_ADMIN:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_PORTAIL_ADMIN))
                            $error = true;
                        else {
                            //Si avant son rôle n'était pas admin du portail il faut supprimer l'enregistrement de la table portail_users
                            if ($old_role != User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::deleteEntry($this->id);
                            }
                        }
                    }
                    break;
                //Dans le cas d'un Admin de labo il faut lui affecter les droits admin sur SON labo
                case User::TYPE_LABO_ADMIN:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_LABO_ADMIN))
                            $error = true;
                        else {
                            //Si avant son rôle était admin du portail il faut lui créer une entrée dans la table portail_users sinon la mettre à jour
                            if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::createNewEntry($this->id, intval($request_post['paramLabo']), null);
                            } else {
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                                    PortailUsers::updateEntry($this->id, intval($request_post['paramLabo']), null);
                                else {
                                    //On récupère le labo de la personne connectée
                                    $idLabo = PortailUsers::getIdLaboUser(User::getCurrentUser()->id);
                                    PortailUsers::updateEntry($this->id, $idLabo, null);
                                }
                            }
                        }
                    }
                    break;
                //Dans le cas d'un user de labo il faut lui affecter les droits client sur SON labo
                case User::TYPE_LABO_USER:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_LABO_USER))
                            $error = true;
                        else {
                            //Si avant son rôle était admin du portail il faut lui créer une entrée dans la table portail_users sinon la mettre à jour
                            if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::createNewEntry($this->id, intval($request_post['paramLabo']), null);
                            } else {
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                                    PortailUsers::updateEntry($this->id, intval($request_post['paramLabo']), null);
                                else {
                                    //On récupère le labo de la personne connectée
                                    $idLabo = PortailUsers::getIdLaboUser(User::getCurrentUser()->id);
                                    PortailUsers::updateEntry($this->id, $idLabo, null);
                                }
                            }
                        }
                    }
                    break;
                //Dans le cas d'un Admin de client il faut lui affecter les droits admin sur SON client
                case User::TYPE_CLIENT_ADMIN:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_CLIENT_ADMIN))
                            $error = true;
                        else {
                            //Si avant son rôle était admin du portail il faut lui créer une entrée dans la table portail_users sinon la mettre à jour
                            if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::createNewEntry($this->id, null, intval($request_post['paramClient']));
                            } else {
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                                    PortailUsers::updateEntry($this->id, null, intval($request_post['paramClient']));
                                else {
                                    //On récupère le labo de la personne connectée
                                    $idClient = PortailUsers::getIdClientUser(User::getCurrentUser()->id);
                                    PortailUsers::updateEntry($this->id, null, $idClient);
                                }

                            }
                        }
                    }
                    break;
                //Dans le cas d'un user de client il faut lui affecter les droits user sur SON client
                case User::TYPE_CLIENT_USER:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_CLIENT_USER))
                            $error = true;
                        else {
                            //Si avant son rôle était admin du portail il faut lui créer une entrée dans la table portail_users sinon la mettre à jour
                            if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::createNewEntry($this->id, null, intval($request_post['etablissement']));
                            } else {
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                                    PortailUsers::updateEntry($this->id, null, intval($request_post['etablissement']));
                                else {
                                    PortailUsers::updateEntry($this->id, null, intval($request_post['etablissement']));
                                }
                            }
                        }
                    }
                    break;
                //Dans le cas d'un user de client il faut lui affecter les droits user sur SON client
                case User::TYPE_CLIENT_USER_GROUP:
                    if(User::revokeRole($this->id, $old_role)) {
                        if (!User::assignRole($this->id, User::TYPE_CLIENT_USER_GROUP))
                            $error = true;
                        else {
                            //Si avant son rôle était admin du portail il faut lui créer une entrée dans la table portail_users sinon la mettre à jour
                            /*if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                PortailUsers::createNewEntry($this->id, null, intval($request_post['etablissement']));
                            } else {
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
                                    PortailUsers::updateEntry($this->id, null, intval($request_post['etablissement']));
                                else {
                                    PortailUsers::updateEntry($this->id, null, intval($request_post['etablissement']));
                                }
                            }*/
                            if ($old_role == User::TYPE_PORTAIL_ADMIN) {
                                //On crée une entrée pour chaque établissement
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                                    for($i = 0; $i < count($request_post['etablissementgroup']);$i++){
                                        PortailUsers::createNewEntry($this->id,null,intval($request_post['etablissementgroup'][$i]));
                                    }
                                }
                                else{
                                    for($i = 0; $i < count($request_post['kvformadmin']['etablissement']);$i++){
                                        PortailUsers::createNewEntry($this->id,null,intval($request_post['kvformadmin']['etablissement'][$i]));
                                    }
                                }
                            }
                            else{
                                //On supprime toutes les entrée de la table pour cet utilisateur
                                $listEntry = PortailUsers::find()->andFilterWhere(['id_user'=>$this->id])->all();
                                foreach ($listEntry as $item) {
                                    $item->delete();
                                }

                                //On crée une entrée pour chaque établissement
                                if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                                    for($i = 0; $i < count($request_post['etablissementgroup']);$i++){
                                        PortailUsers::createNewEntry($this->id,null,intval($request_post['etablissementgroup'][$i]));
                                    }
                                }
                                else{
                                    for($i = 0; $i < count($request_post['kvformadmin']['etablissement']);$i++){
                                        PortailUsers::createNewEntry($this->id,null,intval($request_post['kvformadmin']['etablissement'][$i]));
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
            if(!$error)
                $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $error = true;
        }

        return !$error;
    }

    /**
     * Supprime un utilisateur et ses permissions associées
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteUserWithPermission($id){
        $transaction = self::getDb()->beginTransaction();
        $error = false;
        try{
            if(!$this->delete())
                $error = true;

            PortailUsers::deleteEntry($id);

            if(!$error)
                $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $error = true;
        }

        return !$error;
    }


    /**
     * Renvoie les items du menu User
     * @return array
     */
    public static function menuItems()
    {
        return [
            ['label' => '<i class="fa fa-angle-double-right"></i> ' . Yii::t('menu', 'Users'), 'url' => ['/user-management/user/index']],
            ['label' => '<i class="fa fa-angle-double-right"></i> ' . Yii::t('menu', 'Roles'), 'url' => ['/user-management/role/index']],
            ['label' => '<i class="fa fa-angle-double-right"></i> ' . Yii::t('menu', 'Permissions'), 'url' => ['/user-management/permission/index']],
            ['label' => '<i class="fa fa-angle-double-right"></i> ' . Yii::t('menu', 'Permission groups'), 'url' => ['/user-management/auth-item-group/index']],
            ['label' => '<i class="fa fa-angle-double-right"></i> ' . Yii::t('menu', 'Visit log'), 'url' => ['/user-management/user-visit-log/index']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'username'           => Yii::t('microsept', 'Login'),
            'superadmin'         => Yii::t('microsept', 'Superadmin'),
            'confirmation_token' => 'Confirmation Token',
            'registration_ip'    => Yii::t('microsept', 'Registration IP'),
            'bind_to_ip'         => Yii::t('microsept', 'Bind to IP'),
            'status'             => Yii::t('microsept', 'Status'),
            'gridRoleSearch'     => Yii::t('microsept', 'Roles'),
            'created_at'         => Yii::t('microsept', 'created_at'),
            'updated_at'         => Yii::t('microsept', 'updated_at'),
            'password'           => Yii::t('microsept', 'Password'),
            'repeat_password'    => Yii::t('microsept', 'Repeat password'),
            'email_confirmed'    => Yii::t('microsept', 'E-mail confirmed'),
            'email'              => 'E-mail',
        ];
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE   => Yii::t('microsept', 'Active'),
            self::STATUS_INACTIVE => Yii::t('microsept', 'Inactive'),
        );
    }

    public function rules()
    {
        $rules =  [

        ];
        return array_merge(parent::rules(), $rules);
    }
}
