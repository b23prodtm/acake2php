<?php
use Migrations\AbstractMigration;

class CreateExemplaire extends AbstractMigration
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
        $table = $this->table('exemplaire');
        $table->addColumn('code_reference', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('date_de_livraison', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 5,
            'null' => false,
        ]);
        $table->addIndex([
            'code_reference',
        ], [
            'name' => 'BY_CODE_REFERENCE',
            'unique' => false,
        ]);
        $table->create();
    }
}
