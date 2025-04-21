<?php
use Migrations\AbstractMigration;

class CreateArticles extends AbstractMigration
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
        $table = $this->table('articles');
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('entete', 'string', [
            'default' => null,
            'limit' => 250,
            'null' => false,
        ]);
        $table->addColumn('id_categorie', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('corps', 'text', [
            'default' => null,
            'limit' => 4,
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
        $table->addIndex([
            'id',
        ], [
            'name' => 'BY_ID',
            'unique' => false,
        ]);
        $table->create();
    }
}
