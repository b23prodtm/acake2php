<?php
use Migrations\AbstractMigration;

class CreateClassification extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('classification');
        $table->addColumn('id', 'string', [
            'default' => null,
            'limit' => 4,
            'null' => false,
        ]);
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 30,
            'null' => false,
        ]);
        $table->addColumn('id_categorie', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addIndex([
            'id',
        ], [
            'name' => 'BY_ID',
            'unique' => false,
        ]);
        $table->create();
    }
}
