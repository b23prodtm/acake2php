<?php
use Migrations\AbstractMigration;

class CreatePhp4uBookmarks extends AbstractMigration
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
        $table = $this->table('php4u_bookmarks');
        $table->addColumn('id', 'integer', [
            'default' => null,
            'limit' => 10,
            'null' => false,
        ]);
        $table->addColumn('dbase', 'string', [
            'default' => null,
            'limit' => 128,
            'null' => false,
        ]);
        $table->addColumn('user', 'string', [
            'default' => null,
            'limit' => 128,
            'null' => false,
        ]);
        $table->addColumn('label', 'string', [
            'default' => null,
            'limit' => 128,
            'null' => false,
        ]);
        $table->addColumn('query', 'text', [
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
