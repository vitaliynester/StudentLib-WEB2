<?php


namespace App\Entity;

use App\Helper\DbConnection;
use App\Repository\UserRepository;
use DateTime;
use Exception;

class PosterEntity extends BaseEntity
{
    /**
     * @var string (название таблицы в БД)
     */
    protected string $tableName;

    /**
     * @var array|string[] (столбцы в БД для данной сущности)
     */
    protected array $tableFields = ['user_id', 'post_name', 'post_create_date', 'post_edit_date', 'post_description'];

    /**
     * @var DbConnection (сущность для работы с БД)
     */
    protected DbConnection $db;

    /**
     * @var UserRepository (репозиторий пользователя)
     */
    private UserRepository $userRepo;

    /**
     * @var int (идентификатор поста)
     */
    private int $id;

    /**
     * @var int (идентификатор автора поста)
     */
    private int $userId;

    /**
     * @var string (название поста)
     */
    private string $name;

    /**
     * @var string (дата создания поста)
     */
    private string $createDate;

    /**
     * @var string (дата изменения поста)
     */
    private string $editDate;

    /**
     * @var string (описание поста)
     */
    private string $description;


    /**
     * Конструктор для сущности поста.
     * @param array $data (ассоциативный массив с полями из БД)
     * @throws Exception (ошибка подключения к БД)
     */
    public function __construct(array $data)
    {
        $this->tableName = "post";
        // проверяем наличие данных значений в полученном массиве
        if (isset($data['post_id'])) {
            self::setId($data['post_id']);
        }
        self::setUserId($data['user_id']);
        if (isset($data['post_name'])) {
            self::setName($data['post_name']);
        } else {
            self::setName($data['title']);
        }
        // проверяем наличие даты создания в полученном массиве
        // если дата создания не указана, то берем текущую дату в формате (2021-04-05 02:33:44)
        if (isset($data['post_create_date'])) {
            self::setCreateDate($data['post_create_date']);
        } else {
            $date = new DateTime();
            self::setCreateDate($date->format('Y-m-d H:i:s'));
        }
        // проверяем наличие даты изменения в полученном массиве
        // если дата изменения не указана, то берем текущую дату в формате (2021-04-05 02:33:44)
        if (isset($data['post_edit_date'])) {
            self::setEditDate($data['post_edit_date']);
        } else {
            $date = new DateTime();
            self::setEditDate($date->format('Y-m-d H:i:s'));
        }
        if (isset($data['post_description'])) {
            self::setDescription($data['post_description']);
        } else {
            self::setDescription($data['description']);
        }
        try {
            $this->db = new DbConnection();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Установить новый идентификатор для поста
     * @param int $id (новый идентификатор поста)
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
     * Получить идентификатор поста
     * @return int (идентификатор поста, если -1 - запись в БД отсутствует)
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
        $startWith = $toNamedColumns ? ":" : "";
        $columnsIterable = "";
        foreach ($this->tableFields as $column) {
            if (end($this->tableFields) != $column) {
                $columnsIterable .= $startWith . $column . ", ";
            } else {
                $columnsIterable .= $startWith . $column;
            }
        }
        return $columnsIterable;
    }

    /**
     * Создать ассоциативный массив для указания значений в SQL запросе
     * @return array (ассоциативный массив: ключ - имя столбца, значение - значение соответствующего столбца)
     */
    protected function iterateFields(): array
    {
        $fieldsData = array();
        $fieldsData["user_id"] = self::getUserId();
        $fieldsData["post_name"] = self::getName();
        $fieldsData["post_create_date"] = self::getCreateDate();
        $fieldsData["post_edit_date"] = self::getEditDate();
        $fieldsData["post_description"] = self::getDescription();
        return $fieldsData;
    }

    /**
     * Получить идентификатор пользователя
     * @return int (идентификатор пользователя)
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Установить новый идентификатор пользователя
     * @param int $userId (новый идентификатор пользователя)
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Получить название поста
     * @return string (название поста)
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Установить новое название для поста
     * @param string $name (новое название для поста)
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Получить дату создания поста
     * @return string (дата создания поста)
     */
    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    /**
     * Установить новую дату создания поста
     * @param string $createDate (новая дата создания поста)
     */
    public function setCreateDate(string $createDate): void
    {
        $this->createDate = $createDate;
    }

    /**
     * Получить дату изменения поста
     * @return string (дата изменения поста)
     */
    public function getEditDate(): string
    {
        return $this->editDate;
    }

    /**
     * Установить новое значение для даты изменения поста
     * @param string $editDate (новое значение для даты изменения поста)
     */
    public function setEditDate(string $editDate): void
    {
        $this->editDate = $editDate;
    }

    /**
     * Получить описание из поста
     * @return string (описание поста)
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Установить новое значение для описания поста
     * @param string $description (новое описание для поста)
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Обновить сущность с установленными полями
     * @return int (количество строчек затронутых при обновлении)
     */
    protected function update(): int
    {
        $data = self::iterateColumnsAndFields();
        $sql = "UPDATE " . $this->tableName . " SET " . $data['query_string'] . "WHERE post_id=:post_id";
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
     * Получить имя автора данного поста
     * @return string (имя автора поста в формате "фамилия имя")
     */
    public function getUserName(): string
    {
        // проверяем инициализации репозитория пользователя
        if (!isset($this->userRepo)) {
            $this->userRepo = new UserRepository();
        }
        // получаем имя пользователя по его ID и возвращаем
        return $this->userRepo->getUserNameById(self::getUserId());
    }

    /**
     * Получить абсолютную ссылку для перехода к конкретному посту
     * @return string (абсолютная ссылка для подробной страницы поста)
     */
    public function getPostUrl(): string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . "/post/" . self::getId();
    }
}
