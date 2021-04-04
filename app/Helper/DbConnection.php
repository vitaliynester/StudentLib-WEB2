<?php


namespace App\Helper;

use App\Services\Router;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class DbConnection
{
    /**
     * @var PDO (сущность для взаимодействия с БД)
     */
    private PDO $pdo;

    /**
     * Конструктор класса для работы с БД
     */
    public function __construct()
    {
        try {
            self::initDbConnection();
        } catch (Exception $e) {
            Router::errorPage(500);
        }
    }

    /**
     * Инициализация подключения к БД
     * @throws Exception (ошибка подключения к БД)
     */
    private function initDbConnection()
    {
        // Подключаем данные из файла .env
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        $dsn = 'pgsql:host=' . $_ENV['DB_HOST'] .
            ";port=" . $_ENV['DB_PORT'] .
            ";dbname=" . $_ENV['DB_NAME'] .
            ";user=" . $_ENV['DB_USER'] .
            ";password=" . $_ENV['DB_PASSWORD'];
        try {
            // Подключаемся к БД
            $this->pdo = new PDO($dsn);
        } catch (PDOException $e) {
            // Если не получилось подключиться к БД, то бросаем исключение
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Выполнить переданный SQL запрос с переданными данными, получить результат выполнения
     * @param string $sql (SQL запрос)
     * @param array $fields (поля которые необходимо замапить к запросу)
     * @return bool (результат выполнения запроса (True - успех, False - неудача))
     */
    public function execGetStatus(string $sql, array $fields = []): bool
    {
        if (count($fields) != 0) {
            return $this->pdo->prepare($sql)->execute($fields);
        }
        return $this->pdo->prepare($sql)->execute();
    }

    /**
     * Выполнить переданный SQL запрос с переданными данными, получить количество затронутых строк
     * @param string $sql (SQL запрос)
     * @param array $fields (поля которые необходимо замапить к запросу)
     * @return int (количество затронутых строк)
     */
    public function execGetRowCount(string $sql, array $fields = []): int
    {
        $statement = $this->pdo->prepare($sql);
        if (count($fields) != 0) {
            $statement->execute($fields);
        } else {
            $statement->execute();
        }
        return $statement->rowCount();
    }

    /**
     * Выполнить переданный SQL запрос с переданными данными, получить данные по этому запросу
     * @param string $sql (SQL запрос)
     * @param array $fields (поля которые необходимо замапить к запросу)
     * @return array (полученные данные при выполнении данного запроса)
     */
    public function execGetDataArray(string $sql, array $fields = []): array
    {
        $statement = $this->pdo->prepare($sql);
        if (count($fields) != 0) {
            $statement->execute($fields);
        } else {
            $statement->execute();
        }
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (isset($data)) {
            return $data;
        }
        return [];
    }

    /**
     * Получить идентификатор последней добавленной записи
     * @return int (идентификатор последней добавленной записи)
     */
    public function getLastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Сериализация данного класса
     * @return array (пустой массив, никакие данные не сохраняются при сериализации)
     */
    public function __serialize(): array
    {
        return [];
    }

    /**
     * Десериализация данного класса
     * @param array $data (массив с данными, по умолчанию пустой)
     * @throws Exception (ошибка подключения к БД)
     */
    public function __unserialize(array $data): void
    {
        try {
            $this->initDbConnection();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
