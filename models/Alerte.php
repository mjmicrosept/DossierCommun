<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_alerte".
 *
 * @property int $id
 * @property int $id_hashed
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_etablissement
 * @property int $id_user
 * @property int $context
 * @property int $type
 * @property int $type_emetteur
 * @property int $vecteur
 * @property int $year_missing
 * @property int $month_missing
 * @property int $year_corrupted
 * @property int $month_corrupted
 * @property int $year_nocontext
 * @property int $month_nocontext
 * @property int $periode_missing
 * @property string $date_create
 * @property string $date_update
 * @property int $vue
 * @property int $active
 */
class Alerte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alerte';
    }

    const CONTEXT_ANALYSE = 1;
    const CONTEXT_DOCUMENT = 2;

    const TYPE_DATE_MISSING = 1;
    const TYPE_DATE_CORRUPTED = 2;
    const TYPE_DATE_NOCONTEXT = 3;
    const TYPE_PERIODE_MISSING = 4;
    const TYPE_NODOC = 5;
    const TYPE_SENDMAIL = 6;

    const VECTEUR_MAIL = 1;
    const VECTEUR_APPLI = 2;

    const EMETTEUR_ADMIN = 1;
    const EMETTEUR_CLIENT = 2;

    const MAIL_ERROR_NOERROR = 0;
    const MAIL_ERROR_NOMAILUSER = 1;
    const MAIL_ERROR_NOSEND = 2;
    const MAIL_ERROR_NOMAILLABO = 3;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_client', 'id_user','context', 'type', 'type_emetteur', 'vecteur'], 'required'],
            [['id_labo', 'id_client', 'id_user','context', 'type', 'type_emetteur', 'vecteur', 'year_missing', 'month_missing', 'year_corrupted', 'month_corrupted', 'year_nocontext', 'month_nocontext', 'periode_missing', 'vue', 'active','id_etablissement'], 'integer'],
            [['id_hashed'], 'string', 'max' => 255],
            [['date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'id_user' => 'Id User',
            'id_etablissement' => 'Id Etablissement',
            'type' => 'Type',
            'context' => 'Context',
            'type_emetteur' => 'Type Emetteur',
            'vecteur' => 'Vecteur',
            'year_missing' => 'Year Missing',
            'month_missing' => 'Month Missing',
            'year_corrupted' => 'Year Corrupted',
            'month_corrupted' => 'Month Corrupted',
            'year_nocontext' => 'Year Nocontext',
            'month_nocontext' => 'Month Nocontext',
            'periode_missing' => 'Periode Missing',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'vue' => 'Vue',
            'active' => 'Active',
            'id_hashed' => 'Id Hashed',
        ];
    }

    /**
     * Envoi d'un mail d'alerte pour le cas d'aucun document présent pour un labo
     * @param $idClient
     * @param $idLabo
     * @param $idUser
     * @return bool
     */
    public static function mailGeneralNoDocument($idClient,$idLabo,$idUser,$idEtablissement,$clientName,$etablissementName,$idAlerte,$contextAlerte){
        $error = self::MAIL_ERROR_NOERROR;
        $emailLabo = '';
        $emailClient = '';
        if(!is_null(User::getCurrentUser()->email))
            $emailClient = User::getCurrentUser()->email;

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        if(!is_null($labo))
            $emailLabo = $labo->email;

        if($emailClient != '') {
            if($emailLabo != '') {
                //Dans le cas de l'établissement d'un groupe
                if (!is_null($idEtablissement)) {
                    $bodyText = '';
                    $bodyText .= 'Une alerte a été levée par l\'établissement <strong>' . $etablissementName . '</strong> du groupe <strong>' . $clientName . '</strong> pour la raison suivante : <br/>';
                    if($contextAlerte == self::CONTEXT_DOCUMENT)
                        $bodyText .= ' - Pas de documents présents sur la plateforme.<br/><br/>';
                    else
                        $bodyText .= ' - Pas d\'analyses présentes sur la plateforme.<br/><br/>';
                    $bodyText .= 'Vous pouvez passer le statut de cette alerte à <strong>"traitée"</strong> en cliquant sur le lien suivant : <br>';
                    $bodyText .= 'http://dossiercommun.test.local/index.php/alerte/change-statut?alerte='.md5($idAlerte);
                } else {
                    //Dans le cas d'une entreprise seule
                    $bodyText = '';
                }
                $body = $bodyText;
                $message = \Swift_Message::newInstance();
                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setStreamOptions(['ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]]);
                // on utilise cette adresse pour l'instant TODO configurer serveur SMTP sur le serveur
                $transport->setUsername("maratier.microsept@gmail.com");
                $transport->setPassword("K9dzk4t_1138");
                $message->setTo(array(
                    $emailLabo
                ));
                $message->setSubject('RFL Portail - Documents d\'analyses non présents');
                $message->setContentType("text/html");
                $message->setBody($body);
                $message->setFrom('maratier.microsept@gmail.com');

                // on va générer ici un pdf
                //$message->attach(\Swift_Attachment::newInstance($doc, 'document.pdf', 'application/pdf'));
                $mailer = \Swift_Mailer::newInstance($transport);
                $success = $mailer->send($message, $failedRecipients);
                if (!$success)
                    $error = self::MAIL_ERROR_NOSEND;

            }
            else{
                $error = self::MAIL_ERROR_NOMAILLABO;
            }
        }
        else{
            $error = self::MAIL_ERROR_NOMAILUSER;
        }

        return $error;
    }

    /**
     * Envoi d'un mail d'alerte pour le cas d'une période sans document
     * @param $idClient
     * @param $idLabo
     * @param $idUser
     * @param $periode
     * @return bool
     */
    public static function mailPeriodeMissing($idClient,$idLabo,$idUser,$idEtablissement,$clientName,$etablissementName,$periode,$idAlerte,$contextAlerte){
        $error = false;
        $emailLabo = '';
        $emailClient = '';
        if(!is_null(User::getCurrentUser()->email))
            $emailClient = User::getCurrentUser()->email;

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        if(!is_null($labo))
            $emailLabo = $labo->email;

        if($emailClient != '') {
            if($emailLabo != '') {
                //Dans le cas de l'établissement d'un groupe
                if (!is_null($idEtablissement)) {
                    $bodyText = '';
                    $bodyText .= 'Une alerte a été levée par l\'établissement <strong>' . $etablissementName . '</strong> du groupe <strong>' . $clientName . '</strong> pour la raison suivante : <br/> ';
                    if($contextAlerte == self::CONTEXT_DOCUMENT)
                        $bodyText .= '- Pas de documents présents pendant une période de '.$periode.' mois.<br/><br/>';
                    else
                        $bodyText .= '- Pas d\'analyses présentes pendant une période de '.$periode.' mois.<br/><br/>';
                    $bodyText .= 'Vous pouvez passer le statut de cette alerte à <strong>"traitée"</strong> en cliquant sur le lien suivant : <br>';
                    $bodyText .= 'http://dossiercommun.test.local/index.php/alerte/change-statut?alerte='.md5($idAlerte);
                } else {
                    //Dans le cas d'une entreprise seule
                    $bodyText = '';
                }
                $body = $bodyText;
                $message = \Swift_Message::newInstance();
                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setStreamOptions(['ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]]);
                // on utilise cette adresse pour l'instant TODO configurer serveur SMTP sur le serveur
                $transport->setUsername("maratier.microsept@gmail.com");
                $transport->setPassword("K9dzk4t_1138");
                $message->setTo(array(
                    $emailLabo
                ));
                //$message->setSubject('Dossier commun - Alerte periode missing');
                $message->setSubject('RFL Portail - Documents d\'analyses en attente.');
                $message->setContentType("text/html");
                $message->setBody($body);
                $message->setFrom('maratier.microsept@gmail.com');

                // on va générer ici un pdf
                //$message->attach(\Swift_Attachment::newInstance($doc, 'document.pdf', 'application/pdf'));
                $mailer = \Swift_Mailer::newInstance($transport);
                $success = $mailer->send($message, $failedRecipients);
                if (!$success)
                    $error = self::MAIL_ERROR_NOSEND;
            }
            else{
                $error = self::MAIL_ERROR_NOMAILLABO;
            }
        }
        else{
            $error = self::MAIL_ERROR_NOMAILUSER;
        }

        return $error;
    }

    /**
     * Envoi d'un mail au labo
     * @param $idClient
     * @param $idLabo
     * @param $idUser
     * @return bool
     */
    public static function mailSendMailLabo($idClient,$idLabo,$idUser,$idEtablissement,$clientName,$etablissementName,$idAlerte,$messageClient){
        $error = self::MAIL_ERROR_NOERROR;
        $emailLabo = '';
        $emailClient = '';
        if(!is_null(User::getCurrentUser()->email))
            $emailClient = User::getCurrentUser()->email;

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        if(!is_null($labo))
            $emailLabo = $labo->email;

        if($emailClient != '') {
            if($emailLabo != '') {
                //Dans le cas de l'établissement d'un groupe
                if (!is_null($idEtablissement)) {
                    $bodyText = '';
                    $bodyText .= 'Ce message vous est adressé de la part du groupe <strong>' . $clientName . '</strong> concernant l\'établissement <strong>' . $etablissementName . '</strong> :<br/><br/><strong>"</strong> ';
                    $bodyText .= $messageClient .' <strong>"</strong>';
                } else {
                    //Dans le cas d'une entreprise seule
                    $bodyText = '';
                }
                $body = $bodyText;
                $message = \Swift_Message::newInstance();
                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setStreamOptions(['ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]]);
                // on utilise cette adresse pour l'instant TODO configurer serveur SMTP sur le serveur
                $transport->setUsername("maratier.microsept@gmail.com");
                $transport->setPassword("K9dzk4t_1138");
                $message->setTo(array(
                    $emailLabo
                ));
                $message->setSubject('RFL Portail - Contact client');
                $message->setContentType("text/html");
                $message->setBody($body);
                $message->setFrom('maratier.microsept@gmail.com');

                // on va générer ici un pdf
                //$message->attach(\Swift_Attachment::newInstance($doc, 'document.pdf', 'application/pdf'));
                $mailer = \Swift_Mailer::newInstance($transport);
                $success = $mailer->send($message, $failedRecipients);
                if (!$success)
                    $error = self::MAIL_ERROR_NOSEND;

            }
            else{
                $error = self::MAIL_ERROR_NOMAILLABO;
            }
        }
        else{
            $error = self::MAIL_ERROR_NOMAILUSER;
        }

        return $error;
    }
}
