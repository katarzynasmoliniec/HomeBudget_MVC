<?php

namespace App\Controllers;

use \Core\View;

class Items extends Authenticated
{
    public function indexAction()
    {
        View::renderTemplate('Items/index.html');
    }

    public function newAction()
    {
        echo "new action";
    }
    
    public function showAction()
    {
        echo "show action";
    }
}
