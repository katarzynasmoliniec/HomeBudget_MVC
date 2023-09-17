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
    public $pay;
    public $user_id;
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
}