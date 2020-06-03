<?php
namespace Game\Views;

use Mezon\Application\View;
use Mezon\TemplateEngine\TemplateEngine;
use Game\Models;

class Battle extends View
{

    public function viewBattlePage(): string
    {
        return $this->getTemplate()->getBlock('battle-page');
    }
}
