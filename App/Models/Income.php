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
    public $comment;
    public $name_category;
    public $user_id;
    public $income_id;
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

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment )
                    VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->income_category_assigned_to_user_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', (float) $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', $this->date_of_income, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

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

    public static function getIncomes() {

        $user_id = $_SESSION['user_id'];

        $sql = 'SELECT incomes_category_assigned_to_users.name AS name, incomes.id, incomes.amount, incomes.date_of_income, incomes.income_comment
                FROM incomes
                    LEFT JOIN incomes_category_assigned_to_users ON 
                        incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                    INNER JOIN users ON incomes.user_id = users.id
                WHERE users.id = :user_id AND MONTH(incomes.date_of_income) = MONTH(CURDATE())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $incomes = [];
        $previous_id = null;

        foreach ($results as $row) {

            $income_id = $row['id'];

            if ($income_id != $previous_id) {
                $row['names'] = [];
                $incomes[$income_id] = $row;
            }

            $incomes[$income_id]['names'][] = $row['name'];
            $previous_id =$income_id;
        }

        return $incomes;
    }
}