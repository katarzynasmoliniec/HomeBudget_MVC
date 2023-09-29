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
        $this->user_id = $_SESSION['user_id'];
    }

    protected function after()
    {
        unset($_SESSION['e_period']);
    }

    public function showAction()
    {  
        View::renderTemplate('Bilans/show.html', [
            'user_id' => $this->user_id,
            'active' => 'balance'
        ]);
    }

    public function editAction()
    {
        
        if (!isset($_POST['period'])) {
            $this->redirect('/bilans/show');
        }

        $user_id = $_SESSION['user_id'];

        $currentYear = date('Y');
        $currentMonth = date('m');

        $period = $_POST['period'];

        $dateRange = DateValidator::validateDate($period, $currentYear, $currentMonth);

        if (empty($dateRange)) {
            $this->redirect('/bilans/show');

            exit;
        }

        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        $incomesOfDates = Income::getIncomesofDates($user_id, $start_date, $end_date);
        $expensesOfDates = Expense::getExpensesofDate($user_id, $start_date, $end_date);

        $income_balanceOfDate = Income::getBalanceCategorizedOfDate($user_id, $start_date, $end_date);
        $expense_balanceOfDate = Expense::getBalanceCategorizedOfDate($user_id, $start_date, $end_date);
        

        View::renderTemplate('Bilans/show.html', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'incomesOfDates' => $incomesOfDates,
            'expensesOfDates' => $expensesOfDates,
            'income_balanceOfDate' => $income_balanceOfDate,
            'expense_balanceOfDate' => $expense_balanceOfDate
        ]);

    }

    public function removeIncomeAction()
    {
        $user_id = $_SESSION['user_id'];
        $id = $this->route_params['id'];

        echo json_encode(Income::removeIncome($user_id, $id), JSON_UNESCAPED_UNICODE);
    }

    public function removeExpenseAction()
    {
        $user_id = $_SESSION['user_id'];
        $id = $this->route_params['id'];

        echo json_encode(Expense::removeExpense($user_id, $id), JSON_UNESCAPED_UNICODE);
    }

}
