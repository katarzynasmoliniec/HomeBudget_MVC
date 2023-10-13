<?php

namespace App\Models;

use PDO;
use App\DateValidator;

class Category extends \Core\Model
{

    public static function getNameCategoryIncome($user_id)
    {
        $sql = 'SELECT incomes_category_assigned_to_users.id, incomes_category_assigned_to_users.name
                FROM incomes_category_assigned_to_users
                WHERE incomes_category_assigned_to_users.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNameCategoryExpense($user_id)
    {
        $sql = 'SELECT id, name, cash_limit, is_limit_active FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function addIncomeCategory($userId, $incomeCategory)
    {
        $response = static::validateCategoryIncomes($userId, $incomeCategory);
        $incomeCategory = ucfirst($incomeCategory); 

        if ($response["message_type"] != "error") {
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $incomeCategory, PDO::PARAM_STR);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria dodana.";
        }

        return $response;
    }

    private static function validateCategoryIncomes($userId, $incomeCategory)
    {
        $response = ["message_type" => "", "message" => ""];
        $categoryToUpper = strtoupper($incomeCategory);

        $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $categoryToUpper);

        if ($result == 1) {
            $response["message_type"] = "error";
            $response["message"] = "Niedozwolone znaki w nazwie.";
        }

        if (static::checkIfCategoryIncomeExists($userId, $categoryToUpper)) {
            $response["message_type"] = "error";
            $response["message"] = "Nazwa tej kategorii jest zajęta.";
        }

        return $response;
    }

    private static function checkIfCategoryIncomeExists($userId, $categoryToUpper)
    {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM incomes_category_assigned_to_users
                WHERE user_id = :user_id) a WHERE name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $categoryToUpper, PDO::PARAM_STR);

        $stmt->execute();

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        return true;
    }

    private static function checkIfCategoryExpenseExists($userId, $categoryToUpper, $id)
    {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM expenses_category_assigned_to_users
                WHERE user_id = :userId AND id != :id) a WHERE name = :category';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':category', $categoryToUpper, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        $stmt->execute();

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        return true;
    }

    public static function editIncomeCategory($user_id, $categoryId, $newCategoryName)
    {
        $response = static::validateCategoryIncomes($user_id, $newCategoryName);
        $newCategoryName = ucfirst($newCategoryName); 

        if ($response["message_type"] != "error") {
            $sql = 'UPDATE `incomes_category_assigned_to_users`
                    SET `name`= :name WHERE `user_id` = :user_id AND `id` = :id
                    LIMIT 1';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $newCategoryName, PDO::PARAM_STR);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria edytowana.";
        }
        return $response;
    }

    public static function addExpenseCategory($userId, $expenseCategory, $categoryLimit)
    {
        $response = static::validateCategoryExpenses($userId, $expenseCategory, 0);

        if ($categoryLimit !== "" && $response["message_type"] != "error") {
            $response = static::validateLimit($categoryLimit);
        }
        
        $expenseCategory = ucfirst($expenseCategory);

        if ($response["message_type"] != "error") {
            
            if ($categoryLimit === ""){
                $is_limit_active = 0;
            } else {
                $is_limit_active = 1;
            }
            
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name, cash_limit, is_limit_active)
                    VALUES (:userId, :name, :cashLimit, :is_limit_active)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);
            $stmt->bindParam(':cashLimit', $categoryLimit, PDO::PARAM_INT);
            $stmt->bindParam(':is_limit_active', $is_limit_active, PDO::PARAM_INT);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = ["Kategoria została dodana."];
        }
        return $response;
    }

    public static function editExpenseCategory($userId, $categoryId, $newCategoryName, $categoryLimit)
    {
        $response = static::validateCategoryExpenses($userId, $newCategoryName, $categoryId);

        if ($categoryLimit !== "" && $response["message_type"] != "error") {
            $response = static::validateLimit($categoryLimit);
        }

        $newCategoryName = ucfirst($newCategoryName); 

        if ($response["message_type"] != "error") {

            if ($categoryLimit === ""){
                $is_limit_active = 0;
                $categoryLimit = 0;
                
            } else {
                $is_limit_active = 1;
            }

            if (!empty($newCategoryName) && $categoryLimit === "") {
                $sql = 'UPDATE `expenses_category_assigned_to_users`
                        SET `name` = :newCategoryName
                        WHERE `user_id` = :userId AND `id` = :categoryId
                        LIMIT 1';
            } else {
                $sql = 'UPDATE `expenses_category_assigned_to_users`
                        SET `name` = :newCategoryName, `cash_limit` = :categoryLimit, `is_limit_active` = :is_limit_active
                        WHERE `user_id` = :userId AND `id` = :categoryId
                        LIMIT 1';
            }

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

            if (!empty($newCategoryName)) {
                $stmt->bindParam(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
            }

            if ($categoryLimit !== "") {
                $stmt->bindParam(':categoryLimit', $categoryLimit, PDO::PARAM_INT);
                $stmt->bindParam(':is_limit_active', $is_limit_active, PDO::PARAM_INT);
            }

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria edytowana.";
        }

        return $response;
    }

    public static function checkRemoveIncomeCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'SELECT id FROM incomes
                WHERE user_id = :userId AND income_category_assigned_to_user_id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($fetchArray)) {
            return static::removeIncomeCategory($userId, $categoryId);
        }

        $response["message_type"] = "warning";
        $response["message"] = "Kategoria przychodu zawiera wartości.";

        return $response;
    }

    public static function removeIncomeCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'DELETE FROM `incomes_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `id` = :categoryId
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        static::removeCategoryIcomes($userId, $categoryId);

        $response["message_type"] = "success";
        $response["message"] = "Kategoria usunięta.";

        return $response;
    }

    private static function removeCategoryIcomes($user_id, $categoryId)
    {
        $sql = 'DELETE FROM `incomes`
                WHERE `user_id` = :user_id AND `income_category_assigned_to_user_id` = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function checkRemoveExpenseCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'SELECT id FROM expenses
                WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($fetchArray)) {
            return static::removeExpenseCategory($userId, $categoryId);
        }

        $response["message_type"] = "warning";
        $response["message"] = "Kategoria wydatku zawiera wartości.";

        return $response;
    }

    public static function removeExpenseCategory($user_id, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'DELETE FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `id` = :categoryId
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        static::removeCategoryExpenses($user_id, $categoryId);

        $response["message_type"] = "success";
        $response["message"] = "Kategoria usunięta.";

        return $response;
    }

    private static function removeCategoryExpenses($user_id, $categoryId)
    {
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :user_id AND `expense_category_assigned_to_user_id` = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function expenseGetLimit($user_id, $id) {
        
        $sql = 'SELECT is_limit_active, cash_limit  FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `id` = :id
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result[0];
    }

    public static function getExpenseOfMonth($user_id, $categoryId, $date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        
        $firstDay = substr($date, 0, 8) . '01';
        $lastDay = substr($date, 0, 8) . DateValidator::findLastDayOfMonth($month, $year);

        $sql = 'SELECT SUM(amount) AS monthlySum FROM `expenses`
                WHERE `user_id` = :user_id AND `expense_category_assigned_to_user_id` = :category_id
                AND `date_of_expense` BETWEEN :start_date AND :end_date
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $firstDay, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $lastDay, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result[0]['monthlySum'];
    }

    private static function validateCategoryExpenses($userId, $expenseCategory, $id)
    {
        $response = ["message_type" => "", "message" => ""];
        $categoryToUpper = strtoupper($expenseCategory);

        $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $categoryToUpper);

        if ($result == 1) {
            $response["message_type"] = "error";
            $response["message"] = "Niedozwolone znaki w nazwie.";
        }

        if (static::checkIfCategoryExpenseExists($userId, $categoryToUpper, $id)) {
            $response["message_type"] = "error";
            $response["message"] = "Nazwa tej kategorii jest zajęta.";
        }

        return $response;
    }

    private static function validateLimit($categoryLimit)
    {
        $response = ["message_type" => "", "message" => ""];

        if (!is_numeric($categoryLimit)) {
            $response["message_type"] = "error";
            $response["message"] = "Limit has to be a number.";
        }

        if ($categoryLimit <= 0) {
            $response["message_type"] = "error";
            $response["message"] = "Limit nie może być mniejszy niż 0.01 PLN.";
        }
        return $response;
    }
}
