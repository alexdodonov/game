<?php
namespace Game\Models;

use Mezon\PdoCrud\ConnectionTrait;

class User
{
    use ConnectionTrait;

    /**
     * Метод создания пользователя
     *
     * @param string $email
     *            логин
     * @param string $password
     *            пароль
     * @return int id созданного пользователя
     */
    public function createUser(string $email, string $password): int
    {
        return $this->getConnection()->insert(
            'user',
            [
                'email' => htmlspecialchars($email),
                'password' => md5($password)
            ]);
    }

    /**
     * Проверка существования пользователя с указанным логином и паролем
     *
     * @param string $email
     *            логин
     * @param string $password
     *            пароль
     * @return bool true если пользователь существует и false если нет
     */
    public function userWithEmailAndPasswordExists(string $email, string $password): bool
    {
        return count(
            $this->getConnection()->select(
                '*',
                'user',
                'email LIKE "' . htmlspecialchars($email) . '" AND password LIKE "' . md5($password) . '"')) > 0;
    }

    /**
     * Метод возвращает id пользователя по его логину
     *
     * @param string $email
     *            логин
     * @return int id пользователя
     */
    public function getUserIdByLogin(string $email): int
    {
        $users = $this->getConnection()->select('*', 'user', 'email LIKE "' . htmlspecialchars($email) . '"');

        return $users[0]['id'];
    }

    /**
     * Проверка существования пользователя с указанным логином
     *
     * @param string $email
     *            логин
     * @return bool true если пользователь существует и false если нет
     */
    public function userWithEmailExists(string $email): bool
    {
        return count($this->getConnection()->select('*', 'user', 'email LIKE "' . htmlspecialchars($email) . '"')) > 0;
    }

    /**
     * Метод возвращает список пользователей, которые сейчас на сайте
     *
     * @return array список пользователей, которые сейчас на сайте
     */
    public function getOnlineUsers(): array
    {
        return $this->getConnection()->select(
            'user.id, email, "ready" AS status, MAX(tick.creation_date) AS last_activity',
            'user, tick',
            'user.id = tick.user_id AND user.id <> ' . intval($_SESSION['user-id']) .
            ' GROUP BY user.id HAVING last_activity >= DATE_SUB(NOW(), INTERVAL 15 SECOND) ORDER BY user.id');
    }

    /**
     * Method returns user's login by it's id
     *
     * @param int $id
     *            user's id
     * @return string user's login
     */
    public function getUserLoginById(int $id): string
    {
        $users = $this->getConnection()->select('email', 'user', 'user.id = ' . intval($id));

        if (count($users) == 0) {
            throw (new \Exception('User with id ' . $id . ' was not found'));
        }

        return $users[0]['email'];
    }
}
