<?php


namespace App\Repository;

use App\Helper\DbConnection;

abstract class BaseRepository
{
    /**
     * @var string (название таблицы в БД)
     */
    protected string $tableName;

    /**
     * @var DbConnection (сущность для взаимодействия с БД)
     */
    protected DbConnection $db;
}
