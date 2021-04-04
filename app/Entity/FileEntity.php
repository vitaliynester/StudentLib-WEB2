<?php


namespace App\Entity;

use App\Helper\DbConnection;
use App\Services\Router;
use Exception;

class FileEntity extends BaseEntity
{
    /**
     * @var string (название таблицы БД)
     */
    protected string $tableName = 'file';

    /**
     * @var array (список столбцов в указанной таблице)
     */
    protected array $tableFields = ['post_id',
        'file_name',
        'file_type',
        'file_path',
        'file_src_name',
        'file_download_count'];

    /**
     * @var DbConnection (экземпляр подключения к БД с установленными параметрами)
     */
    protected DbConnection $db;

    /**
     * @var int (идентификатор файла)
     */
    private int $id;

    /**
     * @var int (идентификатор поста, к которому прикреплен файл)
     */
    private int $postId;

    /**
     * @var string (название сохраняемого файла)
     */
    private string $fileName;

    /**
     * @var string (расширение сохраняемого файла)
     */
    private string $fileType;


    /**
     * @var string (путь по которому будет доступен файл)
     */
    private string $filePath;

    /**
     * @var string (исходное название файла: используется при скачивании)
     */
    private string $fileSrcName;

    /**
     * @var int (количество загрузок данного файла)
     */
    private int $fileDownloadCount;

    /**
     * Конструктор для сущности файла.
     * @param array $data (ассоциативный массив с полями из БД)
     */
    public function __construct(array $data)
    {
        // если данные переданы из БД, то там есть данный идентификатор
        if (isset($data['file_id'])) {
            // если идентификатор есть, то устанавливаем данное значение
            self::setId($data['file_id']);
        }
        self::setPostId($data['post_id']);
        self::setFileName($data['file_name']);
        self::setFileType($data['file_type']);
        self::setFilePath($data['file_path']);
        self::setFileSrcName($data['file_src_name']);
        self::setFileDownloadCount($data['file_download_count']);
        try {
            // пытаемся создать подключение к базе данных
            $this->db = new DbConnection();
        } catch (Exception $e) {
            // если не получилось создать подключение к БД, то бросаем 500 ошибку
            Router::errorPage(500);
        }
    }

    /**
     * Устанавливает идентификатор файла
     * @param int $id (идентификатор файла)
     */
    private function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Сохранить внесенные изменения в БД
     * @return bool (успех выполнения операции сохранения)
     */
    public function save(): bool
    {
        try {
            // если идентификатора нет, то файл нужно создать
            if (self::getId() == -1) {
                // создаем и возвращаем результат создания
                return self::create();
            }
            // если идентификатор есть, то обновляем установленные данные
            // и получаем количество затронутых строк
            $rowCountAffected = self::update();
            // если количество затронутых строк 0, то запись в БД отсутствует
            if ($rowCountAffected == 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            // в случае ошибки бросаем исключение
            return false;
        }
    }

    /**
     * Получение идентификатора файла
     * @return int (идентификатор файла, если -1 - запись в БД отсутствует)
     */
    public function getId(): int
    {
        // если значение установлено, то возвращаем его, иначе -1
        return isset($this->id) ? $this->id : -1;
    }

    /**
     * Создать новую сущность в БД
     * @return bool (успех выполнения операции создания новой записи)
     */
    protected function create(): bool
    {
        // формируем строку на добавление в таблицу
        $sql = "INSERT INTO " . $this->tableName . " (" . self::iterateColumns() .
            ") VALUES (" . self::iterateColumns(true) . ")";
        // формируем данные к которым будем мапиться
        $fieldValues = self::iterateFields();
        // обращаемся к классу БД и пытаемся выполнить запрос
        $insertStatus = $this->db->execGetStatus($sql, $fieldValues);
        // получаем последний добавленный индекс и сохраняем его в экземпляре сущности
        $lastId = $this->db->getLastInsertId();
        self::setId($lastId);
        // возвращаем результат выполнения запроса
        return $insertStatus;
    }

    /**
     * Создать строку для указания значений (Values при SQL запросе)
     * @param bool $toNamedColumns (True - использовать при указании именованных значений,
     *                              False - при перечислении в VALUES)
     * @return string (полученная строка для использования в SQL запросе)
     */
    protected function iterateColumns(bool $toNamedColumns = false): string
    {
        // формируем начало строки
        $startWith = $toNamedColumns ? ":" : "";
        // переменная для хранения результата
        $columnsIterable = "";
        // проходимся по всем столбцам таблицы БД
        foreach ($this->tableFields as $column) {
            // если мы обращаемся не к последнему элементу, то добавляем в конце ","
            if (end($this->tableFields) != $column) {
                $columnsIterable .= $startWith . $column . ", ";
            } else {
                $columnsIterable .= $startWith . $column;
            }
        }
        // возвращаем полученный результат
        return $columnsIterable;
    }

    /**
     * Создать ассоциативный массив для указания значений в SQL запросе
     * @return array (ассоциативный массив: ключ - имя столбца, значение - значение соответствующего столбца)
     */
    protected function iterateFields(): array
    {
        $fieldsData = array();
        $fieldsData['post_id'] = self::getPostId();
        $fieldsData['file_name'] = self::getFileName();
        $fieldsData['file_type'] = self::getFileType();
        $fieldsData['file_path'] = self::getFilePath();
        $fieldsData['file_src_name'] = self::getFileSrcName();
        $fieldsData['file_download_count'] = self::getFileDownloadCount();
        return $fieldsData;
    }

    /**
     * Получение идентификатора поста
     * @return int (идентификатор поста)
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Установка нового значения для идентификатора поста
     * @param int $postId (идентификатор поста)
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    /**
     * Получение названия файла в БД
     * @return string (название файла в БД)
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Установка нового значения для названия файла в БД
     * @param string $fileName (новое значение для названия файла)
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * Получение расширения экземпляра файла
     * @return string (расширение файла)
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * Установка расширения экземпляра файла
     * @param string $fileType (новое значение расширения экземпляра файла)
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * Получение относительного пути до файла
     * @return string (относительный путь до файла)
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Установка нового относительного пути до файла
     * @param string $filePath (новый относительный путь до файла)
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * Получение оригинального названия файла
     * @return string (оригинальное название файла)
     */
    public function getFileSrcName(): string
    {
        return $this->fileSrcName;
    }

    /**
     * Установка оригинального названия файла
     * @param string $fileSrcName (новое значение оригинального названия файла)
     */
    public function setFileSrcName(string $fileSrcName): void
    {
        $this->fileSrcName = $fileSrcName;
    }

    /**
     * Получение количества скачиваний данного файла
     * @return int (количество скачиваний файла)
     */
    public function getFileDownloadCount(): int
    {
        // если количество скачиваний не нулл, то возвращаем его, иначе 0
        return isset($this->fileDownloadCount) ? $this->fileDownloadCount : 0;
    }

    /**
     * Установить количество скачиваний для файла
     * @param int $fileDownloadCount (новое значение количества скачиваний)
     */
    public function setFileDownloadCount(int $fileDownloadCount): void
    {
        $this->fileDownloadCount = $fileDownloadCount;
    }

    /**
     * Обновить сущность с установленными полями
     * @return int (количество строчек затронутых при обновлении)
     */
    protected function update(): int
    {
        // формируем значения для выполнения UPDATE запроса SQL
        $data = self::iterateColumnsAndFields();
        // формируем SQL запрос
        $sql = "UPDATE " . $this->tableName . " SET " . $data['query_string'] . "WHERE file_id=:file_id";
        // выполняем запрос и получаем количество затронутых строк
        return $this->db->execGetRowCount($sql, $data['fields']);
    }

    /**
     * Создать ассоциативный массив для указания значений в SQL запросе на UPDATE
     * @return array (ассоциативный массив следующего вида:
     * [
     *  "query_string" => "value",
     *  "fields" => []
     * ])
     */
    protected function iterateColumnsAndFields(): array
    {
        $resultData = array();
        $queryString = "";
        foreach ($this->tableFields as $column) {
            $queryString .= $column . "=:" . $column;
            if (end($this->tableFields) != $column) {
                $queryString .= ", ";
            }
        }
        $resultData['query_string'] = $queryString;
        $resultData['fields'] = $this->iterateFields();
        return $resultData;
    }

    /**
     * Формирует строку для скачивания данного файла
     * @return string (URL адрес для скачивания файла)
     */
    public function getDownloadUrl(): string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . self::getFilePath();
    }
}
