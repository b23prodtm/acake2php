<?php
use Migrations\AbstractMigration;

class CreateDisponibilite extends AbstractMigration
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
        $table = $this->table('disponibilite');
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 5,
            'null' => false,
        ]);
        $table->addColumn('id_exemplaire', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addIndex([
            'id',
        ], [
            'name' => 'BY_ID',
            'unique' => false,
        ]);
        $table->addIndex([
            'id_exemplaire',
        ], [
            'name' => 'BY_ID_EXEMPLAIRE',
            'unique' => false,
        ]);
        $table->create();
    }
}
