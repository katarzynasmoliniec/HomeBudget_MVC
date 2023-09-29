<?php

namespace App\Models;

use App\Flash;
use PDO;

class Income extends \Core\Model
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

    public static function getIncomesofDates($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT incomes_category_assigned_to_users.name AS name, incomes.id, incomes.amount, incomes.date_of_income, incomes.income_comment
                FROM incomes
                    LEFT JOIN incomes_category_assigned_to_users ON 
                        incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                    INNER JOIN users ON incomes.user_id = users.id
                WHERE users.id = :user_id AND incomes.date_of_income BETWEEN :startDate AND :endDate';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getBalanceCategorizedOfDate($user_id, $start_date, $end_date) {
        $sql = 'SELECT incomes_category_assigned_to_users.name AS name, SUM(incomes.amount) AS sumincome
                FROM incomes
            LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
            INNER JOIN users ON incomes.user_id = users.id
            WHERE users.id = :user_id AND incomes.date_of_income BETWEEN :startDate AND :endDate
            GROUP BY name';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function removeIncome($user_id, $id)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'DELETE FROM `incomes`
                WHERE `user_id` = :user_id AND `id` = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $response["message_type"] = "success";
        $response["message"] = "Zmiany zachowane!";

        return $response;
    }

}