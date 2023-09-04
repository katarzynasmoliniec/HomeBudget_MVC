<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Login extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {

            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user->id;
            $this->redirect('/');

        } else {

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
            ]);
        }
    }

    public function destroyAction()
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $this->redirect('/');
    }
}
