<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 25/10/2018
 * Time: 16:56
 */

use yii\db\Migration;

class m160408_074830_create_cron extends Migration
{
    public function up()
    {
        $this->createTable('cron', [
            'id' => $this->primaryKey(),
            'nom' => $this->string(),
            'action' => $this->string(),
            'date_limite' => $this->integer()
        ]);
    }

    public function down()
    {
        $this->dropTable('cron');
    }
}