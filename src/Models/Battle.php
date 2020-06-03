<?php
namespace Game\Models;

use Mezon\PdoCrud\ConnectionTrait;

class Battle
{
    use ConnectionTrait;

    /**
     * Calculating who is winner in each round
     *
     * @param array $roundsHistory
     *            all rounds of the battle
     */
    public static function calculateWinners(array &$roundsHistory): void
    {
        foreach ($roundsHistory as $i => $round) {
            if ($round['usera_move'] == 'stone' && $round['userb_move'] == 'stone') {
                $roundsHistory[$i]['winner'] = 'none';
            } elseif ($round['usera_move'] == 'stone' && $round['userb_move'] == 'paper') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'stone' && $round['userb_move'] == 'scissors') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'stone' && $round['userb_move'] == 'lizard') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'stone' && $round['userb_move'] == 'spok') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'paper' && $round['userb_move'] == 'stone') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'paper' && $round['userb_move'] == 'paper') {
                $roundsHistory[$i]['winner'] = 'none';
            } elseif ($round['usera_move'] == 'paper' && $round['userb_move'] == 'scissors') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'paper' && $round['userb_move'] == 'lizard') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'paper' && $round['userb_move'] == 'spok') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'scissors' && $round['userb_move'] == 'stone') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'scissors' && $round['userb_move'] == 'paper') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'scissors' && $round['userb_move'] == 'scissors') {
                $roundsHistory[$i]['winner'] = 'none';
            } elseif ($round['usera_move'] == 'scissors' && $round['userb_move'] == 'lizard') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'scissors' && $round['userb_move'] == 'spok') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'lizard' && $round['userb_move'] == 'stone') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'lizard' && $round['userb_move'] == 'paper') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'lizard' && $round['userb_move'] == 'scissors') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'lizard' && $round['userb_move'] == 'lizard') {
                $roundsHistory[$i]['winner'] = 'none';
            } elseif ($round['usera_move'] == 'lizard' && $round['userb_move'] == 'spok') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'spok' && $round['userb_move'] == 'stone') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'spok' && $round['userb_move'] == 'paper') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'spok' && $round['userb_move'] == 'scissors') {
                $roundsHistory[$i]['winner'] = 'usera';
            } elseif ($round['usera_move'] == 'spok' && $round['userb_move'] == 'lizard') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['usera_move'] == 'spok' && $round['userb_move'] == 'spok') {
                $roundsHistory[$i]['winner'] = 'none';
            } elseif ($round['usera_move'] == 'none') {
                $roundsHistory[$i]['winner'] = 'userb';
            } elseif ($round['userb_move'] == 'none') {
                $roundsHistory[$i]['winner'] = 'usera';
            }
        }
    }

    /**
     * Calculating amounts of wins for every player
     *
     * @param array $roundsHistory
     * @return array
     */
    public static function calculateWins(array &$roundsHistory): array
    {
        $winsA = $winsB = 0;

        foreach ($roundsHistory as $round) {
            if ($round['winner'] == 'usera') {
                $winsA ++;
            } elseif ($round['winner'] == 'userb') {
                $winsB ++;
            }
        }

        return [
            $winsA,
            $winsB
        ];
    }

    /**
     * Проверяем участвует ли уже пользователь в сражении
     *
     * @param int $userId
     *            идентификатор пользоваетля
     * @return bool true если участвует, иначе false
     */
    public function userInBattle(int $userId): bool
    {
        $battles = $this->getConnection()->select(
            'id, leave_user_id',
            'battle',
            'usera_id = ' . intval($userId) . ' OR userb_id = ' . intval($userId) . ' ORDER BY id DESC',
            0,
            1);

        if (count($battles) == 0) {
            return false;
        }

        if ($battles[0]['leave_user_id'] != 0) {
            return false;
        }

        $round = new Round();
        $roundsHistory = $round->getRoundsHistory($battles[0]['id']);

        self::calculateWinners($roundsHistory);
        list ($winsA, $winsB) = self::calculateWins($roundsHistory);

        if ($winsA < 5 && $winsB < 5) {
            return true;
        }

        return false;
    }

    /**
     * Выбираем битву, в которой участвует пользователь
     *
     * @param int $userId
     *            идентификатор пользователя
     * @return array битва
     */
    public function getUserBattle(int $userId): array
    {
        $result = $this->getConnection()->select(
            'id, usera_id, userb_id, leave_user_id',
            'battle',
            'usera_id = ' . intval($userId) . ' OR userb_id = ' . intval($userId) . ' ORDER BY id DESC',
            0,
            1);

        return $result[0];
    }

    /**
     * Метод создания сражения
     *
     * @param int $useraId
     *            id пользователя, для которого создаём сражение
     * @param int $userbId
     *            id пользователя, для которого создаём сражение
     */
    public function createBattle(int $useraId, int $userbId): void
    {
        $this->getConnection()->insert('battle', [
            'usera_id' => intval($useraId),
            'userb_id' => intval($userbId)
        ]);
    }

    /**
     * Помечаем, кто из игроков покинул битву
     *
     * @param int $userId
     *            id пользователя
     */
    public function leaveBattle(int $userId): void
    {
        $battle = $this->getUserBattle($userId);

        $this->getConnection()->update('battle', [
            'leave_user_id' => intval($userId)
        ], 'id = ' . $battle['id']);
    }
}
