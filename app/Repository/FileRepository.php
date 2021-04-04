<?php


namespace App\Repository;

use App\Entity\FileEntity;
use App\Helper\DbConnection;
use App\Services\Router;
use Exception;

class FileRepository extends BaseRepository
{
    /**
     * @var string (название таблицы в БД)
     */
    protected string $tableName;

    /**
     * @var DbConnection (сущность для взаимодействия с БД)
     */
    protected DbConnection $db;

    /**
     * Конструктор для репозитория файлов.
     */
    public function __construct()
    {
        $this->tableName = 'file';
        try {
            $this->db = new DbConnection();
        } catch (Exception $e) {
            Router::errorPage(500);
        }
    }

    /**
     * Получить массив с информацией о хранимых файлах
     * @return array (массив с информацией о хранимых файлах. Пример:
     * [
     *  "archive" => 5,
     *  "image" => 7,
     *  "other" => 13
     * ]
     * )
     */
    public function getTypeFilesArray(): array
    {
        $sql = "SELECT file_type, COUNT(file_type) as file_type_count FROM " . $this->tableName . " GROUP BY file_type";
        $data = $this->db->execGetDataArray($sql);
        $permittedArrayTypes = require "app/Helper/files_type_constants.php";
        $displayedTypes = array_keys($permittedArrayTypes);
        $resultArray = array();
        foreach ($displayedTypes as $displayedType) {
            $resultArray[$displayedType] = self::calculateFilesCountByType($displayedType, $data);
        }
        return $resultArray;
    }

    /**
     * Получить количество файлов относящихся к определенному типу
     * @param string $displayedType (тип к которому необходимо относить файлы)
     * @param array $data (сырые данные полученные из БД)
     * @return int (количество файлов относящихся к заданному типу)
     */
    private function calculateFilesCountByType(string $displayedType, array $data): int
    {
        // Получаем список названий для форматов
        $permittedArrayTypes = require "app/Helper/files_type_constants.php";
        // Получаем список самих форматов
        $types = array_flip(explode(',', $permittedArrayTypes[$displayedType]));
        // Получаем массив из данных БД по ключу тип файла
        $keysInDb = array_column($data, 'file_type');
        // Получаем массив из данных БД по ключу количество файлов данного типа
        $valuesInDb = array_column($data, 'file_type_count');
        $countByType = 0;
        // Проходимся по типам и количествам файла определенного типа
        foreach (array_combine($keysInDb, $valuesInDb) as $key => $value) {
            if (isset($types[$key])) {
                $countByType += $value;
            }
        }
        return $countByType;
    }

    /**
     * Получить массив файлов относящихся к заданному посту
     * @param int $postId (идентификатор поста)
     * @return array|FileEntity[] (массив файлов относящихся к данному посту)
     */
    public function getFilesFromPost(int $postId): array
    {
        $sql = "SELECT f.file_id, f.post_id, f.file_name, f.file_type, f.file_path, f.file_src_name, f.file_download_count FROM " . $this->tableName . "  f INNER JOIN post p on p.post_id = f.post_id WHERE p.post_id = :post_id";
        $files = $this->db->execGetDataArray($sql, ['post_id' => $postId]);
        if (count($files) != 0) {
            $filesEntities = array();
            foreach ($files as $fileData) {
                $file = new FileEntity($fileData);
                array_push($filesEntities, $file);
            }
            return $filesEntities;
        }
        return [];
    }
}
