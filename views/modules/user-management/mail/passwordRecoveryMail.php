<?php
/**
 * @var $this yii\web\View
 * @var $user webvimark\modules\UserManagement\models\User
 */
use yii\helpers\Html;

?>
<?php
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user-management/auth/password-recovery-receive', 'token' => $user->confirmation_token]);
?>

Bonjour <?= Html::encode($user->username) ?>,<br/>
Vous avez effectué une demande de réinitialisation de mot de passe.
Pour ce faire, il suffit de cliquer sur le lien ci-dessous :
<br/><br/>

<?= Html::a('Réinitialiser le mot de passe', $resetLink) ?>

<br/><br/><br/>
Bonne journée.
