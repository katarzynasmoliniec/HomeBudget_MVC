<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

class Income extends \Core\Model
{
    public $errors = [];

    public $amount;
    public $date_of_income;
    public $income_category_assigned_to_user_id;
    public $user_id;
    public $cats = [];

        
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

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income)
                    VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->income_category_assigned_to_user_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', (float) $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', $this->date_of_income, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate()
    {
        if ($this->amount == '') {
            $this->errors[] = 'Proszę wpisać kwotę przychodu';
        }

        if ($this->date_of_income == '') {
            $this->errors[] = 'Proszę podać datę';
        }

    }

}