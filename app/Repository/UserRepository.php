<?php


namespace App\Repository;

use App\Entity\UserEntity;
use App\Helper\DbConnection;
use App\Services\Router;
use Exception;

class UserRepository extends BaseRepository
{
    /**
     * @var string (название таблицы в БД)
     */
    protected string $tableName;

    /**
     * @var DbConnection (сущность для работы с БД)
     */
    protected DbConnection $db;

    /**
     * Конструктор для репозитория пользователя.
     */
    public function __construct()
    {
        $this->tableName = 'user_profile';
        try {
            $this->db = new DbConnection();
        } catch (Exception $e) {
            Router::errorPage(500);
        }
    }

    /**
     * Проверить существование пользователя в БД
     * @param string $email (почта по которой необходимо проверять)
     * @return bool (результат поиска, True - пользователь уже сущетсвует)
     */
    public function checkUserByEmail(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM " . $this->tableName . " WHERE email=:email";
        $data = $this->db->execGetDataArray($sql, ["email" => $email])[0];
        if ($data['count'] != 0) {
            return true;
        }
        return false;
    }

    /**
     * Получить имя пользователя по его ID
     * @param string $id (идентификатор пользователя)
     * @return string (имя пользователя в формате "фамилия имя")
     */
    public function getUserNameById(string $id): string
    {
        $sql = "SELECT last_name, first_name FROM " . $this->tableName . " WHERE user_id=:id";
        $data = $this->db->execGetDataArray($sql, ['id' => $id])[0];
        return $data['last_name'] . " " . $data['first_name'];
    }

    /**
     * Получение объекта пользователя по почте и паролю
     * @param string $email (почта пользователя)
     * @param string $password (пароль пользователя)
     * @return UserEntity (объект пользователя)
     * @throws Exception (пользователь не найден)
     */
    public function getUser(string $email, string $password): UserEntity
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE email=:email";
        $data = $this->db->execGetDataArray($sql, ["email" => $email])[0];
        if (password_verify($password, $data['password'])) {
            return new UserEntity($data);
        }
        throw new Exception("Пользователь не найден!");
    }
}
