<?php
namespace Game\Models;

use Mezon\PdoCrud\ConnectionTrait;

class Tick
{
    use ConnectionTrait;

    /**
     * Метод создания тика
     *
     * @param int $id
     *            id пользователя, для которого создаём тик
     */
    public function createTick(int $userId): void
    {
        $this->getConnection()->insert('tick', [
            'user_id' => intval($userId),
            'creation_date' => 'NOW()'
        ]);
    }
}