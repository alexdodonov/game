<?php
namespace Game\Views;

use Mezon\Application\View;
use Mezon\TemplateEngine\TemplateEngine;
use Game\Models;

class User extends View
{

    public function viewUsersList(): string
    {
        $userModel = new Models\User();
        $users = $userModel->getOnlineUsers();

        if (count($users) > 0) {
            $content = $this->getTemplate()->getBlock('users-list');
            return TemplateEngine::printRecord($content, [
                'users' => $users
            ]);
        } else {
            return $this->getTemplate()->getBlock('empty-users-list');
        }
    }
}
