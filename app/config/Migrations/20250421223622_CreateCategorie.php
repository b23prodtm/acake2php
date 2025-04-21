<?php
use Migrations\AbstractMigration;

class CreateCategorie extends AbstractMigration
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
        $table = $this->table('categorie');
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('parent', 'integer', [
            'default' => null,
            'limit' => 3,
            'null' => false,
        ]);
        $table->addColumn('image', 'integer', [
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
        $table->addIndex([
            'nom',
        ], [
            'name' => 'BY_NOM',
            'unique' => false,
        ]);
        $table->create();
    }
}
