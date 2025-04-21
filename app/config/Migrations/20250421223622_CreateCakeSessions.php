<?php
use Migrations\AbstractMigration;

class CreateCakeSessions extends AbstractMigration
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
        $table = $this->table('cake_sessions');
        $table->addColumn('id', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('data', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('expires', 'integer', [
            'default' => null,
            'limit' => 11,
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
