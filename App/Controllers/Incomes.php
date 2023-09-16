<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use App\Models\Category;
use \App\Models\Income;

class Incomes extends Authenticated
{

    public function newAction()
    {
        $user_id = $_SESSION['user_id'];
        $this-> category = Category::getNameCategory($user_id);
        

        View::renderTemplate('Incomes/new.html', [
            'category' => $this->category
        ]);
    }

    public function createAction()
    {
        $income = new Income($_POST);

        if ($income->save()) {

            Flash::addMessage('Przychód dodany!');
            View::renderTemplate('Incomes/new.html');

        } else {
            View::renderTemplate('Incomes/new.html', [
                'category' => $this->category
            ]);
        }
    }

    public function editAction()
    {
        View::renderTemplate('Incomes/edit.html', [
            'income' => $this->income
        ]);
    }

    public function updateAction()
    {
        if ($this->income->updateIncomes($_POST)) {

            Flash::addMessage('Zmiany zachowane!');

            $this->redirect('/incomes/new');

        } else {

            View::renderTemplate('Incomes/new.html', [
                'income' => $this->income
            ]);

        }
    }
}
