<?php

namespace App\Entity;

use App\Helper\DbConnection;

abstract class BaseEntity
{
    /**
     * @var DbConnection (экземпляр подключения к БД с установленными параметрами)
     */
    protected DbConnection $db;

    /**
     * @var string (название таблицы БД)
     */
    protected string $tableName;

    /**
     * @var array (список столбцов в указанной таблице)
     */
    protected array $tableFields;

    /**
     * Сохранить внесенные изменения в БД
     * @return bool (успех выполнения операции сохранения)
     */
    abstract public function save(): bool;

    /**
     * Создать новую сущность в БД
     * @return bool (успех выполнения операции создания новой записи)
     */
    abstract protected function create(): bool;

    /**
     * Обновить сущность с установленными полями
     * @return int (количество строчек затронутых при обновлении)
     */
    abstract protected function update(): int;

    /**
     * Создать ассоциативный массив для указания значений в SQL запросе
     * @return array (ассоциативный массив: ключ - имя столбца, значение - значение соответствующего столбца)
     */
    abstract protected function iterateFields(): array;

    /**
     * Создать строку для указания значений (Values при SQL запросе)
     * @param bool $toNamedColumns (True - использовать при указании именованных значений,
     *                              False - при перечислении в VALUES)
     * @return string (полученная строка для использования в SQL запросе)
     */
    abstract protected function iterateColumns(bool $toNamedColumns = false): string;

    /**
     * Создать ассоциативный массив для указания значений в SQL запросе на UPDATE
     * @return array (ассоциативный массив следующего вида:
     * [
     *  "query_string" => "value",
     *  "fields" => []
     * ])
     */
    abstract protected function iterateColumnsAndFields(): array;
}
