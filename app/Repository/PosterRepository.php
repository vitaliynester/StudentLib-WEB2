<?php


namespace App\Repository;

use App\Entity\PosterEntity;
use App\Helper\DbConnection;
use App\Services\Router;
use Exception;

class PosterRepository extends BaseRepository
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
     * @var int (номер страницы для получения постов)
     */
    protected int $pageNum;

    /**
     * Конструктор для репозитория постов.
     */
    public function __construct()
    {
        $this->pageNum = 0;
        $this->tableName = 'post';
        try {
            $this->db = new DbConnection();
        } catch (Exception $e) {
            Router::errorPage(500);
        }
    }

    /**
     * Получаем все посты из БД
     * @return array (массив со всеми постами)
     */
    public function getAllPosts(): array
    {
        $sql = "SELECT * FROM " . $this->tableName . " ORDER BY post_id DESC";
        $posts = $this->db->execGetDataArray($sql);
        $postEntities = array();
        if (!isset($posts)) {
            return $postEntities;
        }
        try {
            foreach ($posts as $post) {
                array_push($postEntities, new PosterEntity($post));
            }
        } catch (Exception $e) {
            Router::errorPage(500);
        }
        return $postEntities;
    }

    /**
     * Получаем посты из БД с похожим названием
     * @param string $name (искомое название)
     * @return array (массив найденных постов)
     */
    public function getPostsLikeName(string $name): array
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE post_name LIKE %:like_name%";
        $posts = $this->db->execGetDataArray($sql, ['like_name' => $name]);
        $postEntities = array();
        if (!isset($posts)) {
            return $postEntities;
        }
        try {
            foreach ($posts as $post) {
                array_push($postEntities, new PosterEntity($post));
            }
        } catch (Exception $e) {
            Router::errorPage(500);
        }
        return $postEntities;
    }

    /**
     * Получить пост по ID поста
     * @param int $id (ID поста для поиска)
     * @return PosterEntity (сущность пользователя найденного по ID)
     */
    public function getPosterById(int $id): PosterEntity
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE post_id=:id";
        $post = $this->db->execGetDataArray($sql, ['id' => $id])[0];
        if (isset($post)) {
            try {
                return new PosterEntity($post);
            } catch (Exception $e) {
                Router::errorPage(404);
            }
        }
        Router::errorPage(404);
        die();
    }

    /**
     * Получить посты при переходе на следующую страницу
     * @param int $limit (количество элементов на странице)
     * @return array|PosterEntity[] (массив постов для указанной страницы)
     */
    public function getPostsFromNextPage(int $limit): array
    {
        $posts = self::getPostsPerPage($this->pageNum, $limit);
        if (count($posts) == 0) {
            return self::getPostsPerPage($this->pageNum - 1, $limit);
        } else {
            $this->pageNum++;
            return $posts;
        }
    }

    /**
     * Получаем посты из БД на данной странице
     * @param int $page (номер страницы)
     * @param int $limit (количество получаемых элементов)
     * @return array (массив постов)
     */
    public function getPostsPerPage(int $page, int $limit): array
    {
        $sql = "SELECT * FROM " . $this->tableName . " ORDER BY post_id DESC LIMIT :limit OFFSET :offset";
        if ($page == 1) {
            $bindData = ['limit' => $limit, 'offset' => 0];
        } else {
            $bindData = ['limit' => $limit, 'offset' => $page * $limit];
        }
        $posts = $this->db->execGetDataArray($sql, $bindData);
        $postEntities = array();
        if (!isset($posts)) {
            return $postEntities;
        }
        try {
            foreach ($posts as $post) {
                array_push($postEntities, new PosterEntity($post));
            }
        } catch (Exception $e) {
            Router::errorPage(500);
        }
        return $postEntities;
    }

    /**
     * Получить посты при переходе на предыдущую страницу
     * @param int $limit (количество элементов на странице)
     * @return array|PosterEntity[] (массив постов для указанной страницы)
     */
    public function getPostsFromPrevPage(int $limit): array
    {
        $posts = self::getPostsPerPage($this->pageNum, $limit);
        if (count($posts) == 0) {
            return self::getPostsPerPage($this->pageNum + 1, $limit);
        } else {
            $this->pageNum--;
            return $posts;
        }
    }
}
