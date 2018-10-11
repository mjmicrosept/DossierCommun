<?php
/**
 * Created by PhpStorm.
 * User: medissimo
 * Date: 02/05/2015
 * Time: 00:49
 */

namespace app\controllers;

use Yii;
use yii\web\Cookie;

class Controller extends \yii\web\Controller {

    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }


    /**
     * Set page size for grid
     */
    public function actionGridPageSize()
    {
        if ( Yii::$app->request->post('grid-page-size') )
        {
            $cookie = new Cookie([
                'name' => '_grid_page_size',
                'value' => Yii::$app->request->post('grid-page-size'),
                'expire' => time() + 86400 * 365, // 1 year
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

}