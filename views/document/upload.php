<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 17/10/2018
 * Time: 10:23
 */

use \yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('microsept', 'Upload_Labo');
$this->params['breadcrumbs'][] = $this->title;

$baseUrl = Yii::$app->request->baseUrl;
$urlGetFoldersFile = Url::to(['/document/get-folders-file']);
$urlDownloadFiles = Url::to(['/document/download-files']);

$this->registerJS(<<<JS
    var url = {
        getFoldersFile:'{$urlGetFoldersFile}',
        downloadFiles:'{$urlDownloadFiles}',
    };
JS
);

?>