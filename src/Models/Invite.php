<?php
namespace Game\Models;

use Mezon\PdoCrud\ConnectionTrait;

class Invite
{
    use ConnectionTrait;

    /**
     * Проверка создал ли пользователь А приглашение для пользователя Б
     *
     * @param int $authorUserId
     *            ид создателя инвайта
     * @param int $inviteUserId
     *            ид получателя инвайта
     * @return bool true если инвайт был создан, иначе false
     */
    public function haveCreatedInviteFor(int $authorUserId, int $inviteUserId): bool
    {
        $result = $this->getConnection()->select(
            'id',
            'invite',
            'usera_id = ' . intval($authorUserId) . ' AND userb_id = ' . intval($inviteUserId));

        return count($result) > 0;
    }

    /**
     * Создание инвайта от пользователя А пользователю Б
     *
     * @param int $authorUserId
     *            ид создателя инвайта
     * @param int $inviteUserId
     *            ид получателя инвайта
     */
    public function createInvite(int $authorUserId, int $inviteUserId): void
    {
        $this->getConnection()->insert(
            'invite',
            [
                'usera_id' => intval($authorUserId),
                'userb_id' => intval($inviteUserId)
            ]);
    }

    /**
     * Проверка, есть ли инвайт для пользователя $inviteUserId
     *
     * @param int $inviteUserId
     *            пользователь
     * @return bool true если инвайт сущестует, false если не существует
     */
    public function inviteForUserExists(int $inviteUserId): bool
    {
        $result = $this->getConnection()->select('id', 'invite', 'userb_id = ' . intval($inviteUserId));

        return count($result) > 0;
    }

    /**
     * Получить один инвайт для пользователя
     *
     * @param int $inviteUserId
     *            пользователь
     * @return int id инвайта
     */
    public function getInviteIdForUser(int $inviteUserId): int
    {
        $result = $this->getConnection()->select('id', 'invite', 'userb_id = ' . intval($inviteUserId));

        return intval($result[0]['id']);
    }

    /**
     * Удаляем инвайт
     *
     * @param int $id
     *            идентификатор удаляемого инвайта
     */
    public function deleteInvite(int $id): void
    {
        $this->getConnection()->delete('invite', 'id = ' . intval($id));
    }

    /**
     * Получение инвайта по его id
     *
     * @param int $id
     *            id инвайта
     * @return array инвайт
     */
    public function getInviteById(int $id): array
    {
        $result = $this->getConnection()->select('id, usera_id, userb_id', 'invite', 'id = ' . intval($id));

        return $result[0];
    }

    /**
     * Получение инвайта по его id
     *
     * @param int $id
     *            идентификатор инвайта
     * @return bool true если инвайт существует, иначе false
     */
    public function inviteWithIdExists(int $id): bool
    {
        $result = $this->getConnection()->select('id', 'invite', 'id = ' . intval($id));

        return count($result) > 0;
    }
}
