<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use App\Models\Category;
use \App\Models\Income;

class Incomes extends Authenticated
{
    protected function before()
    {
        parent::before();
        $user_id = $_SESSION['user_id'];
        $this-> cats = Category::getNameCategoryIncome($user_id);
    }
    
    
    public function newAction()
    {

        View::renderTemplate('Incomes/new.html', [
            'cats' => $this->cats
        ]);
    }

    public function createAction()
    {
        $income = new Income($_POST);

        if ($income->save()) {

            Flash::addMessage('Przychód dodany!');
            View::renderTemplate('Incomes/new.html', [
                'cats' => $this->cats
            ]);

        } else {
            View::renderTemplate('Incomes/new.html', [
                'cats' => $this->cats
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
