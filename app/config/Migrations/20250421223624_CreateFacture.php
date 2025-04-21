<?php
use Migrations\AbstractMigration;

class CreateFacture extends AbstractMigration
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
        $table = $this->table('facture');
        $table->addColumn('reference', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => false,
        ]);
        $table->addColumn('montant_facture', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 6,
            'scale' => 1,
        ]);
        $table->addColumn('date_de_facturation', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('mode_de_paiement', 'string', [
            'default' => null,
            'limit' => 4,
            'null' => false,
        ]);
        $table->addColumn('id', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addIndex([
            'reference',
        ], [
            'name' => 'BY_REFERENCE',
            'unique' => false,
        ]);
        $table->create();
    }
}
