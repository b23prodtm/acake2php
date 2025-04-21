<?php
use Migrations\AbstractMigration;

class CreateFournisseur extends AbstractMigration
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
        $table = $this->table('fournisseur');
        $table->addColumn('code_fournisseur', 'string', [
            'default' => null,
            'limit' => 4,
            'null' => false,
        ]);
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 30,
            'null' => false,
        ]);
        $table->addColumn('adresse', 'string', [
            'default' => null,
            'limit' => 40,
            'null' => false,
        ]);
        $table->addColumn('numero_tel', 'integer', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('ville', 'string', [
            'default' => null,
            'limit' => 15,
            'null' => false,
        ]);
        $table->addColumn('pays', 'string', [
            'default' => null,
            'limit' => 15,
            'null' => false,
        ]);
        $table->addIndex([
            'code_fournisseur',
        ], [
            'name' => 'BY_CODE_FOURNISSEUR',
            'unique' => false,
        ]);
        $table->create();
    }
}
