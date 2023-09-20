<?php

namespace App\Models;

use PDO;

class Expense extends \Core\Model
{
    public $errors = [];
    public $cats = [];
    public $payforms = [];

    public $amount;
    public $date;
    public $category_id;
    public $name_category;
    public $pay;
    public $user_id;
    public $expense_id;
    public $comment;

        
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save()
    {
        $this->validate();
        $user_id = $_SESSION['user_id'];

        if (empty($this->errors)) {

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment )
                    VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->pay, PDO::PARAM_INT);
            $stmt->bindValue(':amount', (float) $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate()
    {
        if ($this->amount == '') {
            $this->errors[] = 'Proszę wpisać kwotę wydatku';
        }

        if ($this->date == '') {
            $this->errors[] = 'Proszę podać datę';
        }

        if ($this->pay == '') {
            $this->errors[] = 'Proszę podać sposób płatności';
        }

    }

    public static function getExpenses() {

        $user_id = $_SESSION['user_id'];

        $sql = 'SELECT expenses_category_assigned_to_users.name AS name, expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment,
                        payment_methods_assigned_to_users.name AS payname
                FROM expenses
                    LEFT JOIN expenses_category_assigned_to_users ON 
                        expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                    INNER JOIN users ON expenses.user_id = users.id
                    INNER JOIN payment_methods_assigned_to_users 
                    ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id 
                WHERE users.id = :user_id AND MONTH(expenses.date_of_expense) = MONTH(CURDATE())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $expenses = [];
        $previous_id = null;

        foreach ($results as $row) {

            $expense_id = $row['id'];

            if ($expense_id != $previous_id) {
                $row['names'] = [];
                $row['paynames'] = [];
                $expenses[$expense_id] = $row;
            }

            $expenses[$expense_id]['names'][] = $row['name'];
            $expenses[$expense_id]['paynames'][] = $row['payname'];
            $previous_id =$expense_id;
        }

        return $expenses;
    }
}