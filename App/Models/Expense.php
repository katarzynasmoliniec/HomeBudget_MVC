<?php

namespace App\Models;

use PDO;

class Expense extends \Core\Model
{
    public $errors = [];
        
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
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
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

    public static function getExpensesofDate($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT expenses_category_assigned_to_users.name AS name, expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment,
                        payment_methods_assigned_to_users.name AS payname
                FROM expenses
                    LEFT JOIN expenses_category_assigned_to_users ON 
                        expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                    INNER JOIN users ON expenses.user_id = users.id
                    INNER JOIN payment_methods_assigned_to_users 
                    ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id 
                WHERE users.id = :user_id AND expenses.date_of_expense BETWEEN :startDate AND :endDate';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function getBalanceCategorizedOfDate($user_id, $start_date, $end_date) {

        $sql = 'SELECT expenses_category_assigned_to_users.name AS name, SUM(expenses.amount) AS sumexpense
                FROM expenses
            LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
            INNER JOIN users ON expenses.user_id = users.id
            WHERE users.id = :user_id AND expenses.date_of_expense BETWEEN :startDate AND :endDate
            GROUP BY name';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function removeExpense($user_id, $id)
    {
        $response = ["message_type" => "", "message" => ""];
        
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :user_id AND `id` = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $response["message_type"] = "success";
        $response["message"] = "Kategoria usunięta.";

        return $response;
    }
}