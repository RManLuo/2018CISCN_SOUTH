<?php

use Phinx\Migration\AbstractMigration;

class User extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user');
        //默认增加id主键
        $table->addColumn('username', 'string', ['limit'=> 15,])
            ->addColumn('password', 'string', ['limit'=> 32])
            ->addColumn('salt', 'string', ['limit'=> 10])
            ->addColumn('mail', 'string')
            ->addColumn('integral', 'float', ['default'=> 1000]) //用户持有的积分
            ->addColumn('recommend', 'integer') //邀请注册数，最多20
            ->addIndex(array('username'), array('unique' => true))
            ->create();
    }
}
