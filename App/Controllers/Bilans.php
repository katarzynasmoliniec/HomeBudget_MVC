<?php

namespace App\Controllers;

use App\Models\Expense;
use \Core\View;
use \App\Models\Income;

class Bilans extends Authenticated
{   
    
    
    protected function before()
    {
        parent::before();
        $user_id = $_SESSION['user_id'];
        $this->incomes = Income::getIncomes($user_id);
        $this->expenses = Expense::getExpenses($user_id);
    }
    
    public function showAction()
    {
        View::renderTemplate('Bilans/show.html', [
            'incomes' => $this->incomes,
            'expenses' => $this->expenses
        ]);
    }

    public function createAction()
    {
       
    }
}
