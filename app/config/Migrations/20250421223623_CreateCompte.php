<?php
use Migrations\AbstractMigration;

class CreateCompte extends AbstractMigration
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
        $table = $this->table('compte');
        $table->addColumn('id', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('nb_de_produits_achetes', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('montant_d_achat_total', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 6,
            'scale' => 1,
        ]);
        $table->addColumn('date_ouverture_du_compte', 'date', [
            'default' => null,
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
