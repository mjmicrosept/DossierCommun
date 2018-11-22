<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Controller extends \yii\console\Controller
{
    public $verbose = false;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'verbose'
        ]);
    }

    protected function printv ($message) {
        //if ($this->verbose)
            echo $message.PHP_EOL;
    }
}
