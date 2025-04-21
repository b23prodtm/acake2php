<?php
use Migrations\AbstractMigration;

class CreateInfo extends AbstractMigration
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
        $table = $this->table('info');
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('categorie', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('titre', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('auteur', 'string', [
            'default' => null,
            'limit' => 250,
            'null' => false,
        ]);
        $table->addColumn('contenu', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('langue', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => false,
        ]);
        $table->addColumn('date', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('published', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('images', 'string', [
            'default' => null,
            'limit' => 35,
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
