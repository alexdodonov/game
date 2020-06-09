<?php
namespace Game;

use Mezon\Application\AjaxApplication;
use Mezon\TemplateEngine\TemplateEngine;
require_once (__DIR__ . '/../conf/conf.php');

class AjaxKernel extends AjaxApplication
{

    protected function validateAuthorizationForAjaxRequests()
    {
        if (isset($_SESSION['user-id']) === false) {
            throw (new \Exception('User must be authorized'));
        }
    }

    public function actionLogin(): void
    {
        $user = new Models\User();

        if ($user->userWithEmailAndPasswordExists($_POST['email'], $_POST['password'])) {
            $_SESSION['user-id'] = $user->getUserIdByLogin($_POST['email']);

            $this->ajaxRequestResult("ok");
        } else {
            $this->ajaxRequestResult("User was not found");
        }
    }

    public function actionRegister(): void
    {
        $user = new Models\User();
        $user->getConnection()->lock([
            'user'
        ], [
            'WRITE'
        ]);

        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
            $user->getConnection()->unlock();
            $this->ajaxRequestResult('Invalid email');
        }

        if ($user->userWithEmailExists($_POST['email'])) {
            $user->getConnection()->unlock();
            $this->ajaxRequestResult('User already exists');
        }

        if (strlen($_POST['password']) < 6) {
            $user->getConnection()->unlock();
            $this->ajaxRequestResult('Password can not be less then 6 symbols');
        }

        if (strlen($_POST['email']) > 128) {
            $user->getConnection()->unlock();
            $this->ajaxRequestResult('Email is too long');
        }

        $id = $user->createUser($_POST['email'], $_POST['password']);

        $_SESSION['user-id'] = $id;

        $user->getConnection()->unlock();
        $this->ajaxRequestResult("ok");
    }

    public function actionTick(): void
    {
        $this->validateAuthorizationForAjaxRequests();

        $tick = new Models\Tick();
        $tick->createTick($_SESSION['user-id']);

        $this->ajaxRequestResult("ok");
    }

    public function actionUsersTable(): void
    {
        $userModel = new Models\User();
        $users = $userModel->getOnlineUsers();

        $content = file_get_contents(__DIR__ . '/../res/blocks/users-table.tpl');

        $this->ajaxRequestResult(
            count($users) > 0 ? TemplateEngine::printRecord($content, [
                'users' => $users
            ]) : 'No online users');
    }

    public function actionInvite(): void
    {
        $this->validateAuthorizationForAjaxRequests();

        $inviteModel = new Models\Invite();
        $inviteModel->getConnection()->lock([
            'invite'
        ], [
            'WRITE'
        ]);

        if ($inviteModel->haveCreatedInviteFor($_SESSION['user-id'], $_POST['user-id'])) {
            $inviteModel->getConnection()->unlock();
            $this->ajaxRequestResult('You have already created invite for this user');
        } else {
            // create invite
            $inviteModel->createInvite($_SESSION['user-id'], $_POST['user-id']);
            $inviteModel->getConnection()->unlock();
            $this->ajaxRequestResult('ok');
        }
    }

    public function actionPickInvite(): void
    {
        $inviteModel = new Models\Invite();

        if ($inviteModel->inviteForUserExists($_SESSION['user-id'])) {
            $inviteId = $inviteModel->getInviteIdForUser($_SESSION['user-id']);

            $this->ajaxRequestResult($inviteId);
        } else {
            $this->ajaxRequestResult('no invites');
        }
    }

    public function actionDeclineInvite(): void
    {
        $inviteModel = new Models\Invite();
        $inviteModel->getConnection()->lock([
            'invite'
        ], [
            'WRITE'
        ]);

        $inviteModel->deleteInvite($_POST['invite-id']);

        $inviteModel->getConnection()->unlock();
        $this->ajaxRequestResult('ok');
    }

    public function actionAcceptInvite(): void
    {
        $inviteModel = new Models\Invite();
        $inviteModel->getConnection()->lock([
            'invite'
        ], [
            'WRITE'
        ]);

        $inviteId = $_POST['invite-id'];

        if ($inviteModel->inviteWithIdExists($inviteId) === false) {
            $inviteModel->getConnection()->unlock();
            $this->ajaxRequestResult('Invite was not found');
        }

        $battle = new Models\Battle();
        if ($battle->userInBattle($_SESSION['user-id']) === true) {
            $inviteModel->getConnection()->unlock();
            $this->ajaxRequestResult('You are already in battle');
        }

        $invite = $inviteModel->getInviteById($_POST['invite-id']);
        $battle->createBattle($invite['usera_id'], $invite['userb_id']);

        $inviteModel->deleteInvite($_POST['invite-id']);

        $inviteModel->getConnection()->unlock();
        $this->ajaxRequestResult('ok');
    }

    public function actionBattleStarted(): void
    {
        $battle = new Models\Battle();

        if ($battle->userInBattle($_SESSION['user-id']) === true) {
            $this->ajaxRequestResult('ok');
        } else {
            $this->ajaxRequestResult('no');
        }
    }

    private function checkMove(string $move): void
    {
        $moves = [
            'stone',
            'paper',
            'scissors',
            'lizard',
            'spok'
        ];

        if (in_array($move, $moves) === false) {
            $this->ajaxRequestResult('Invalid move');
        }
    }

    public function actionMakeMove(): void
    {
        $this->checkMove($_POST['move']);

        $battle = new Models\Battle();
        if ($battle->userInBattle($_SESSION['user-id']) === false) {
            $this->ajaxRequestResult('Battle was not found');
        }

        $userBattle = $battle->getUserBattle($_SESSION['user-id']);

        $round = new Models\Round();
        $round->getConnection()->lock([
            'round'
        ], [
            'WRITE'
        ]);
        $currentRound = $round->getCurrentRound($userBattle['id']);

        if ($userBattle['usera_id'] == $_SESSION['user-id']) {
            if ($currentRound['usera_move'] != 'none') {
                $round->getConnection()->unlock();
                $this->ajaxRequestResult('Wait for your opponent\'s move');
            } else {
                $round->setUserAMove($currentRound['id'], $_POST['move']);
                $round->getConnection()->unlock();
                $this->ajaxRequestResult('ok');
            }
        } else {
            if ($currentRound['userb_move'] != 'none') {
                $round->getConnection()->unlock();
                $this->ajaxRequestResult('Wait for your opponent\'s move');
            } else {
                $round->setUserBMove($currentRound['id'], $_POST['move']);
                $round->getConnection()->unlock();
                $this->ajaxRequestResult('ok');
            }
        }
    }

    public function actionBattleRunner(): void
    {
        $battle = new Models\Battle();

        $userBattle = $battle->getUserBattle($_SESSION['user-id']);

        $round = new Models\Round();
        $roundsHistory = $round->getRoundsHistory($userBattle['id']);

        $currentRound = $round->getCurrentRound($userBattle['id']);
        $round->getConnection()->lock([
            'round'
        ], [
            'WRITE'
        ]);

        $remainingTime = 30 - time() + strtotime($currentRound['creation_date']) - TIMEZONE_SHIFT;
        if ($remainingTime <= 0 && $currentRound['usera_move'] == 'none' && $currentRound['userb_move'] == 'none') {
            $round->restartRound($currentRound['id']);
            $remainingTime = 30;
        } elseif ($remainingTime <= 0 && ($currentRound['usera_move'] == 'none' || $currentRound['userb_move'] == 'none')) {
            $round->createRound($userBattle['id']);
            $remainingTime = 30;
        }

        $round->getConnection()->unlock();

        Models\Battle::calculateWinners($roundsHistory);
        list ($winsA, $winsB) = Models\Battle::calculateWins($roundsHistory);

        $user = new Models\User();

        $this->ajaxRequestResult(
            [
                'history' => $roundsHistory,
                'you_are' => $userBattle['usera_id'] == $_SESSION['user-id'] ? 'usera' : 'userb',
                'remaining_time' => $remainingTime,
                'usera_login' => $user->getUserLoginById($userBattle['usera_id']),
                'userb_login' => $user->getUserLoginById($userBattle['userb_id']),
                'usera_wins' => $winsA,
                'userb_wins' => $winsB,
                'one_user_left_the_battle' => $userBattle['leave_user_id'] != 0
            ]);
    }

    public function actionLeaveBattle(): void
    {
        $battle = new Models\Battle();
        $battle->getConnection()->lock([
            'battle'
        ], [
            'WRITE'
        ]);
        $battle->leaveBattle($_SESSION['user-id']);
        $battle->getConnection()->unlock();
        $this->ajaxRequestResult('ok');
    }
}

$app = new AjaxKernel();
$app->run();
