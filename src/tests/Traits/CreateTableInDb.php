<?php

namespace Elgndy\FileUploader\Tests\Traits;

use Illuminate\Database\Schema\Blueprint;

trait CreateTableInDb
{
    protected function createTableInDB(string $tableName)
    {
        tap(
            $this->app['db']->connection()->getSchemaBuilder(),
            function ($schema) use($tableName) {
                if (!$schema->hasTable($tableName)) {
                    $schema->create(
                        $tableName,
                        function (Blueprint $table) {
                            $table->increments('id');
                            $table->timestamps();
                        }
                    );
                }
            }
        );
    }
}
