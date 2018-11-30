<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 30/11/2018
 * Time: 09:10
 */

namespace app\assets\components\Summernote;

use yii\web\AssetBundle;

class SummerNoteAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/components/Summernote/dist';

    public $css = [
        'summernote.css',
    ];
    public $js = [
        'summernote.js',
    ];
}