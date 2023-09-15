<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Income;
use \App\Models\User;

class Incomes extends Authenticated
{
  
    public function newAction()
    {
        View::renderTemplate('Incomes/new.html');
    }

    public function createAction()
    {
        $income = new Income($_POST);

        if ($income->save()) {

            Flash::addMessage('Przychód dodany!');
            View::renderTemplate('Incomes/new.html');

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
                'income' => $this->incomes
            ]);

        }
    }
}
