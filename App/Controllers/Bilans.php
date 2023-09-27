<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use App\Models\Expense;
use App\DateValidator;

class Bilans extends Authenticated
{   
    protected function before()
    {
        parent::before();
        $user_id = $_SESSION['user_id'];
        $this->incomes = Income::getIncomes($user_id);
        $this->expenses = Expense::getExpenses($user_id);
        $this->expense_balances = Expense::getExpensesCategorizedCurrentDate($user_id);
    }

    protected function after()
    {
        unset($_SESSION['e_period']);
    }

    public function showAction()
    {  
        View::renderTemplate('Bilans/show.html', [
            'incomes' => $this->incomes,
            'expenses' => $this->expenses,
            'expense_balances' => $this->expense_balances
        ]);
    }

    public function editAction()
    {
        if (!isset($_POST['period'])) {
            $this->redirect('/bilans/show');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $currentYear = date('Y');
        $currentMonth = date('m');

        $period = $_POST['period'];

        $dateRange = DateValidator::validateDate($period, $currentYear, $currentMonth);

        if (empty($dateRange)) {
            $this->redirect('/bilanse/show');
            exit;
        }

        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        $incomesOfDates = Income::getIncomesofDates($user_id, $start_date, $end_date);
        $expensesOfDates = Expense::getExpensesofDate($user_id, $start_date, $end_date);
        

        View::renderTemplate('Bilans/show.html', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'incomesOfDates' => $incomesOfDates,
            'expensesOfDates' => $expensesOfDates
        ]);

    }

}
