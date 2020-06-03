<?php
namespace Game\Models;

use Mezon\PdoCrud\ConnectionTrait;

class Round
{
    use ConnectionTrait;

    /**
     * Метод создания раунда
     *
     * @param int $battleId
     *            id сражения
     */
    public function createRound(int $battleId): void
    {
        $this->getConnection()->insert('round', [
            'battle_id' => intval($battleId)
        ]);
    }

    /**
     * Пытаемся вернуть последнйи раунд
     *
     * @param int $battleId
     *            ид битвы
     * @return array раунд или пустой массив
     */
    protected function getTopMostRounds(int $battleId): array
    {
        return $this->getConnection()->select(
            'id, usera_move, userb_move, creation_date',
            'round',
            'battle_id = ' . intval($battleId) . ' ORDER BY id DESC',
            0,
            1);
    }

    /**
     * Метод возвращает текущий раунд.
     * Если надо создать новый, то создаёт его.
     *
     * @param int $battleId
     *            id сражения
     * @return array раунд
     */
    public function getCurrentRound(int $battleId): array
    {
        $round = $this->getTopMostRounds($battleId);

        if (count($round) === 0 || ($round[0]['usera_move'] !== 'none' && $round[0]['userb_move'] !== 'none')) {
            $this->createRound($battleId);
            $round = $this->getTopMostRounds($battleId);
        }

        return $round[0];
    }

    /**
     * Игрок А делает ход
     *
     * @param int $roundId
     *            ид раунда
     * @param string $move
     *            ход
     */
    public function setUserAMove(int $roundId, string $move): void
    {
        $this->getConnection()->update('round', [
            'usera_move' => $move
        ], 'id = ' . intval($roundId));
    }

    /**
     * Игрок Б делает ход
     *
     * @param int $roundId
     *            ид раунда
     * @param string $move
     *            ход
     */
    public function setUserBMove(int $roundId, string $move): void
    {
        $this->getConnection()->update('round', [
            'userb_move' => $move
        ], 'id = ' . intval($roundId));
    }

    /**
     * История битвы
     *
     * @param int $battleId
     *            ид битвы
     * @return array история битвы
     */
    public function getRoundsHistory(int $battleId): array
    {
        $rounds = $this->getConnection()->select(
            'id, usera_move, userb_move',
            'round',
            'battle_id = ' . intval($battleId) . ' ORDER BY id ASC');

        $length = count($rounds);
        if ($length > 0 &&
            ($rounds[$length - 1]['usera_move'] == 'none' || $rounds[$length - 1]['userb_move'] == 'none')) {
            array_pop($rounds);
        }

        return $rounds;
    }

    /**
     * Перезапускаем раунд
     *
     * @param int $roundId
     *            идентификатор раунда
     */
    public function restartRound(int $roundId): void
    {
        $this->getConnection()->update('round', [
            'creation_date' => 'NOW()'
        ], 'id = ' . intval($roundId));
    }
}
