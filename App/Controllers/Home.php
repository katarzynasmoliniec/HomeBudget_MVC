<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;

class Home extends \Core\Controller
{
    public function indexAction()
    {
        View::renderTemplate('Home/index.html');
    }
}
