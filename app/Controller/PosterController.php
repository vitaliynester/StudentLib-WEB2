<?php


namespace App\Controller;

use App\Entity\FileEntity;
use App\Entity\PosterEntity;
use App\Services\Router;
use App\Services\View;
use App\Validator\FileValidator;
use App\Validator\PosterValidator;
use Exception;
use Ramsey\Uuid\Uuid;

class PosterController
{
    /**
     * Обработчик POST запроса на создание нового поста
     * @param array $data (заполненные поля для создания поста)
     * @param array $files (список файлов необходимых для загрузки)
     */
    public function create(array $data, array $files)
    {
        // Конвертируем список файлов для загрузки в удобный формат
        $filesData = $this->reArrayFiles($files['post_file']);
        // Производим валидацию полей для создания поста
        $posterValid = PosterValidator::validate($data);
        // Проводим валидацию переданные файлов для последующего их сохранения
        $filesValid = FileValidator::validate($filesData);
        // Если данные о посте не прошли проверку, то
        if (!$posterValid) {
            // Записываем в сессию полученные ошибки и сохраняем заполненные данные
            $_SESSION['errors'] = PosterValidator::getErrors();
            $_SESSION['user_data'] = $data;
            // Перенаправляем обратно на страницу создания нового поста
            Router::redirect("/create-post");
            die();
        }
        // Если данные о посте прошли проверку, но
        // переданные файлы невалидны, то
        if (!$filesValid) {
            // Записываем в сессию полученные ошибки и сохраняем заполненные данные
            $_SESSION['errors'] = FileValidator::getErrors();
            $_SESSION['user_data'] = $data;
            // Перенаправляем обратно на страницу создания нового поста
            Router::redirect("/create-post");
            die();
        }
        // Если данные о посте и переданные файлы валидны, то
        try {
            // Получаем ID пользователя для указания автора поста
            $data['user_id'] = $_SESSION['user']['id'];
            // Создаем новый экземпляр сущности поста
            $post = new PosterEntity($data);
            // Производим сохранение поста в БД
            $post->save();
            // Проходимся по всем переданным файлам и создаем для них новую запись в БД
            foreach ($filesData as $file) {
                self::createFileEntity($file, $post->getId());
            }
            // Сохраняем ID поста для перенаправления на только что созданный пост
            $_SESSION['detail_post'] = $post->getId();
            // Перенаправляем на страницу с детальной информацией о посте
            Router::redirect('/detail-post');
            die();
        } catch (Exception $e) {
            // Ошибка могла возникнуть при подключения к БД, поэтому
            // перенаправляем на страницу с ошибкой 500
            Router::errorPage(500);
            die();
        }
    }

    /**
     * Метод для конвертации списка файлов в удобный формат
     * @param array $files (исходный список файлов)
     * @return array (полученный список файлов)
     */
    private function reArrayFiles(array $files): array
    {
        // Создаем массив куда будет записывать полученный результат
        $reFiles = array();
        // Считаем количество переданных файлов
        $fileCount = count($files['name']);
        // Получаем ключи массива с файлами (размер, название, временное название и т.д.)
        $fileKeys = array_keys($files);

        // Проходимся по всем файлам
        for ($i = 0; $i < $fileCount; $i++) {
            // Проходимся по всем параметрам файла и сохраняем их для соответствующего файла
            foreach ($fileKeys as $key) {
                $reFiles[$i][$key] = $files[$key][$i];
            }
            // Разбиваем название файла по "." для получения расширения файла
            $fileNameSplit = explode('.', $files['name'][$i]);
            // Если у нас нет "." в названии, то устанавливаем расширение "other"
            if (count($fileNameSplit) == 0) {
                $reFiles[$i]['extension'] = "other";
            } else {
                // Иначе, мы берем последнее значение массива
                $reFiles[$i]['extension'] = end($fileNameSplit);
            }
        }
        // Возвращаем полученный преобразованный массив
        return $reFiles;
    }

    /**
     * Метод для создания сущности файла
     * @param array $file (необходимые данные для создания файла)
     * @param int $postId (идентификатор поста для привязки к файлу)
     * @return FileEntity (сущность файла)
     */
    private function createFileEntity(array $file, int $postId): FileEntity
    {
        // Генерируем новое случайное имя, которое будет храниться в БД
        // Это нужно для избежания потери данных при загрузке файлов с одинаковым именем
        $newFileName = (string)Uuid::uuid4();
        // Формируем путь по которому будет доступна данный файл
        $filPath = dirname(__DIR__, 2) . '/uploads/' . $newFileName;
        // Если получилось сохранить файл в локальной памяти
        if (move_uploaded_file($file['tmp_name'], $filPath)) {
            // Формируем массив с необходимыми данными для создания записи в БД
            $fileData = array();
            $fileData['post_id'] = $postId;
            $fileData['file_name'] = $newFileName;
            $fileData['file_type'] = $file['extension'];
            $fileData['file_path'] = '/uploads/' . $newFileName;
            $fileData['file_src_name'] = $file['name'];
            $fileData['file_download_count'] = 0;
            // Передаем полученный массив в конструктор класса сущности файла
            $file = new FileEntity($fileData);
            // Сохраняем полученную сущность в БД
            $file->save();
            // Возвращаем сущность файла из функции
            return $file;
        } else {
            // Если не получилось сохранить файл, то завершаем выполнение
            die();
        }
    }

    /**
     * Обработчик GET запроса на открытие конкретного поста
     * @param string $param (идентификатор поста)
     */
    public function openPost(string $param)
    {
        // Если идентификатор не был передан, то
        if (empty($param)) {
            // Перенаправляем на главную страницу
            Router::redirect('/home');
        }
        // Если идентификатор был передан, то проверяем авторизацию пользователя
        View::checkIfNotLogin('/login');
        // Сохраняем идентификатор поста в сессии пользователя
        $_SESSION['detail_post'] = $param;
        // Далее, перенаправляем на страницу с подробной информацией о посте
        Router::redirect('/detail-post');
        die();
    }

    /**
     * Обработчик GET запроса для перехода к конкретной странице записи (на данный момент не используется)
     * @param string $page (номер страницы для перехода)
     */
    public function navigationPosts(string $page)
    {
        // Проверяем, что пользователь авторизован
        View::checkIfNotLogin('/login');
        // Если страница не была передана, то выводим первую страницу с постами
        if (empty($page)) {
            Router::redirect('/all-posts');
            die();
        }
        // Если страница была передана, то сохраняем её в локальной сессии
        $_SESSION['page_num'] = $page;
        // Перенаправляем обратно на страницу с постами
        Router::redirect('/all-posts');
        die();
    }
}
