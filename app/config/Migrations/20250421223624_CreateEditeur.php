<?php
use Migrations\AbstractMigration;

class CreateEditeur extends AbstractMigration
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
        $table = $this->table('editeur');
        $table->addColumn('code_editeur', 'string', [
            'default' => null,
            'limit' => 4,
            'null' => false,
        ]);
        $table->addColumn('image', 'integer', [
            'default' => null,
            'limit' => 5,
            'null' => false,
        ]);
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addIndex([
            'code_editeur',
        ], [
            'name' => 'BY_CODE_EDITEUR',
            'unique' => false,
        ]);
        $table->create();
    }
}
