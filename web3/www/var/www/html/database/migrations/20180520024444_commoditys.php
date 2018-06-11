<?php

use Phinx\Migration\AbstractMigration;

class Commoditys extends AbstractMigration
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
        $table = $this->table('commoditys');
        $table->addColumn('name', 'string', ['limit'=> 200])
            ->addColumn('desc', 'string', ['limit'=> 500, 'default'=> 'no description'])
            ->addColumn('amount', 'integer', ['default'=> 10])
            ->addColumn('price', 'float')
            ->addIndex(array('name'), array('unique' => true))
            ->create();
    }
}
