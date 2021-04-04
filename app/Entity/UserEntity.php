<?php


namespace App\Entity;

use App\Helper\DbConnection;
use Exception;

class UserEntity extends BaseEntity
{
    /**
     * @var string (название таблицы в БД)
     */
    protected string $tableName;

    /**
     * @var array|string[] (поля таблицы в БД)
     */
    protected array $tableFields = ["last_name", "first_name", "patronymic", "email", "password"];

    /**
     * @var DbConnection (сущность для работы с БД)
     */
    protected DbConnection $db;

    /**
     * @var int (идентификатор пользователя)
     */
    private int $id;

    /**
     * @var string (фамилия пользователя)
     */
    private string $lastName;

    /**
     * @var string (имя пользователя)
     */
    private string $firstName;

    /**
     * @var string (отчество пользователя)
     */
    private string $patronymic;

    /**
     * @var string (электронная почта пользователя)
     */
    private string $email;

    /**
     * @var string (пароль пользователя)
     */
    private string $password;

    /**
     * Конструктор для сущности пользователя.
     * @param array $data (ассоциативный массив с полями из БД)
     */
    public function __construct(array $data)
    {
        $this->tableName = "user_profile";
        if (isset($data['user_id'])) {
            self::setId($data['user_id']);
        }
        self::setLastName($data['last_name']);
        self::setFirstName($data['first_name']);
        self::setEmail($data['email']);
        self::setPassword($data['password']);
        if (isset($data['patronymic'])) {
            self::setPatronymic($data['patronymic']);
        }
        try {
            $this->db = new DbConnection();
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Установить новое значение для идентификатора пользователя
     * @param int $id (новый идентификатор пользователя)
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
     * Получить идентификатор пользователя
     * @return int (идентификатор пользователя, если -1 - запись в БД отсутствует)
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
        $sql = "INSERT INTO " . $this->tableName .
            "(" . self::iterateColumns() .
            ") VALUES (" . self::iterateColumns(true) . ")";
        $fieldValues = self::iterateFields();
        return $this->db->execGetStatus($sql, $fieldValues);
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
            if ($column == "patronymic") {
                if (self::getPatronymic() != "") {
                    $columnsIterable .= $startWith . $column . ", ";
                } else {
                    continue;
                }
            } else {
                if (end($this->tableFields) != $column) {
                    $columnsIterable .= $startWith . $column . ", ";
                } else {
                    $columnsIterable .= $startWith . $column;
                }
            }
        }
        return $columnsIterable;
    }

    /**
     * Получить отчество пользователя
     * @return string (отчество пользователя)
     */
    public function getPatronymic(): string
    {
        // если отчество у пользователя есть, то возвращаем его, иначе пустую строку
        return isset($this->patronymic) ? $this->patronymic : "";
    }

    /**
     * Установить новое отчество для пользователя
     * @param string $patronymic (новое отчество пользователя)
     */
    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * Создать ассоциативного массива для указания значений в SQL запросе
     * @return array (ассоциативный массив: ключ - имя столбца, значение - значение соответствующего столбца)
     */
    protected function iterateFields(): array
    {
        $fieldsData = array();
        $fieldsData["last_name"] = self::getLastName();
        $fieldsData["first_name"] = self::getFirstName();
        $fieldsData["email"] = self::getEmail();
        $fieldsData["password"] = self::getPassword();
        if (!empty(self::getPatronymic())) {
            $fieldsData["patronymic"] = self::getPatronymic();
        }
        return $fieldsData;
    }

    /**
     * Получить фамилию пользователя
     * @return string (фамилия пользователя)
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Установить новую фамилию для пользователя
     * @param string $lastName (новая фамилия пользователя)
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * Получить имя пользователя
     * @return string (имя пользователя)
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Установить новое имя для пользователя
     * @param string $firstName (новое имя пользователя)
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * Получить электронную почту пользователя
     * @return string (электронная почта пользователя)
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Установить новую электронную почту пользователю
     * @param string $email (новая электронная почта)
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Получить пароль пользователя
     * @return string (хэш значение пароля пользователя)
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Установить новый пароль для пользователя
     * @param string $password (новый пароль для пользователя)
     */
    public function setPassword(string $password): void
    {
        $this->password = (string)password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Обновить сущность с установленными полями
     * @return int (количество строчек затронутых при обновлении)
     */
    protected function update(): int
    {
        $data = self::iterateColumnsAndFields();
        $sql = "UPDATE " . $this->tableName . " SET " . $data['query_string'] . " WHERE user_id=:user_id";
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
        $result = array();
        $queryString = "";
        foreach ($this->tableFields as $column) {
            if ($column == "patronymic") {
                if (!empty(self::getPatronymic())) {
                    $queryString .= $column . "=:" . $column . ", ";
                } else {
                    continue;
                }
            } else {
                $queryString .= $column . "=:" . $column;
                if (end($this->tableFields) != $column) {
                    $queryString .= ", ";
                }
            }
        }
        $result['query_string'] = $queryString;
        $result['fields'] = self::iterateFields();
        return $result;
    }

    /**
     * Получает имя пользователя в формате "Фамилия Имя"
     * @return string (имя пользователя, пример: Иванов Иван)
     */
    public function getUserName(): string
    {
        return self::getLastName() . " " . self::getFirstName();
    }
}
