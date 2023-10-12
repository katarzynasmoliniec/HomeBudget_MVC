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
        $this-> categories = Category::getNameCategoryExpense($user_id);
        $this-> payforms = Payform::getNamePayform($user_id);
    }

    protected function after()
    {
        unset($_SESSION['s_expense']);
        unset($_SESSION['e_expense']);
        unset($_SESSION['e_expense_comment']);
    }
    
    
    public function newAction()
    {
        View::renderTemplate('Expenses/new.html', [
            'categories' => $this->categories,
            'payforms' => $this ->payforms
        ]);
    }

    public function createAction()
    {
        $expense = new Expense($_POST);

        if ($expense->save()) {

            Flash::addMessage('Wydatek dodany!');
            View::renderTemplate('Expenses/new.html', [
                'categories' => $this->categories,
                'payforms' => $this ->payforms
            ]);

        } else {
            View::renderTemplate('Expenses/new.html', [
                'categories' => $this->categories,
                'payforms' => $this ->payforms
            ]);
        }
    }

    public function limitAction()
    {
        $user_id = $_SESSION['user_id'];
        $categoryId = $this->route_params['category'];

        echo json_encode(Category::expenseGetLimit($user_id, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function expenseMonthSumAction()
    {
        $user_id = $_SESSION['user_id'];
        $categoryId = $this->route_params['category'];
        $date = $this->route_params['date'];

        echo json_encode(Category::getMonthlyCategoryExpense($user_id, $categoryId, $date), JSON_UNESCAPED_UNICODE);
    }

    
}
