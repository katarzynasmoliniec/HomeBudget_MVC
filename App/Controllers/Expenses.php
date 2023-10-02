<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use App\Models\Category;
use \App\Models\Expense;
use App\Models\Payform;

class Expenses extends Authenticated
{
    protected function before()
    {
        parent::before();
        $user_id = $_SESSION['user_id'];
        $this-> categs = Category::getNameCategoryExpense($user_id);
        $this-> payforms = Payform::getNamePayform($user_id);
    }
    
    
    public function newAction()
    {

        View::renderTemplate('Expenses/new.html', [
            'categs' => $this->categs,
            'payforms' => $this ->payforms
        ]);
    }

    public function createAction()
    {
        $expense = new Expense($_POST);

        if ($expense->save()) {

            Flash::addMessage('Wydatek dodany!');
            View::renderTemplate('Expenses/new.html', [
                'categs' => $this->categs,
                'payforms' => $this ->payforms
            ]);

        } else {
            View::renderTemplate('Expenses/new.html', [
                'categs' => $this->categs,
                'payforms' => $this ->payforms
            ]);
        }
    }

    public function editAction()
    {
        View::renderTemplate('Expenses/edit.html', [
            'expense' => $this->expense
        ]);
    }

    public function updateAction()
    {
        if ($this->expense->updateExpense($_POST)) {

            Flash::addMessage('Zmiany zachowane!');

            $this->redirect('/expenses/new');

        } else {

            View::renderTemplate('Expenses/new.html', [
                'expense' => $this->expense
            ]);

        }
    }
}
