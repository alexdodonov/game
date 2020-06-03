<?php
namespace Game;

class Kernel extends \Mezon\Application\CommonApplication
{

    /**
     * Главная страница
     *
     * @return array скомилированная страница
     */
    public function actionIndex(): array
    {
        if (isset($_SESSION['user-id'])) {
            $battle = new Models\Battle();
            if ($battle->userInBattle($_SESSION['user-id']) === true) {
                $tick = new Models\Tick();
                $tick->createTick($_SESSION['user-id']);

                return [
                    'title' => 'Battle page',
                    'main' => new Views\Battle($this->getTemplate(), 'battlePage')
                ];
            }
            else{
            
            $tick = new Models\Tick();
            $tick->createTick($_SESSION['user-id']);

            return [
                'title' => 'Main page',
                'main' => new Views\User($this->getTemplate(), 'usersList')
            ];
            }
        } else {
            return [
                'title' => 'Main page',
                'main' => $this->getTemplate()->getBlock('index')
            ];
        }
    }

    /**
     * Форма регистрации
     *
     * @return array скомилированная страница
     */
    public function actionRegistration(): array
    {
        return [
            'title' => 'Registration form',
            'main' => new Views\User($this->getTemplate(), 'registrationForm')
        ];
    }
}
