<?php
use Migrations\AbstractMigration;

class CreateAchat extends AbstractMigration
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
        $table = $this->table('achat');
        $table->addColumn('id_commande', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('id_facture', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => false,
        ]);
        $table->addColumn('id_magasin', 'string', [
            'default' => null,
            'limit' => 4,
            'null' => false,
        ]);
        $table->addIndex([
            'id_commande',
        ], [
            'name' => 'BY_ID_COMMANDE',
            'unique' => false,
        ]);
        $table->addIndex([
            'id_facture',
        ], [
            'name' => 'BY_ID_FACTURE',
            'unique' => false,
        ]);
        $table->addIndex([
            'id_magasin',
        ], [
            'name' => 'BY_ID_MAGASIN',
            'unique' => false,
        ]);
        $table->create();
    }
}
