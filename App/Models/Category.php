<?php

namespace App\Models;

use PDO;

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
        $sql = 'SELECT expenses_category_assigned_to_users.id, expenses_category_assigned_to_users.name
                FROM expenses_category_assigned_to_users
                WHERE expenses_category_assigned_to_users.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    private static function validateCategoryExpenses($userId, $expenseCategory)
    {
        $response = ["message_type" => "", "message" => ""];
        $categoryToUpper = strtoupper($expenseCategory);

        $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $categoryToUpper);

        if ($result == 1) {
            $response["message_type"] = "error";
            $response["message"] = "Niedozwolone znaki w nazwie.";
        }

        if (static::checkIfCategoryExpenseExists($userId, $categoryToUpper)) {
            $response["message_type"] = "error";
            $response["message"] = "Nazwa tej kategorii jest zajęta.";
        }

        return $response;
    }

    private static function checkIfCategoryIncomeExists($userId, $categoryToUpper)
    {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM incomes_category_assigned_to_users
                WHERE user_id = :userId) a WHERE name = :category';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':category', $categoryToUpper, PDO::PARAM_STR);

        $stmt->execute();

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        return true;
    }

    private static function checkIfCategoryExpenseExists($userId, $categoryToUpper)
    {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM expenses_category_assigned_to_users
                WHERE user_id = :userId) a WHERE name = :category';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':category', $categoryToUpper, PDO::PARAM_STR);

        $stmt->execute();

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        return true;
    }

    public static function editIncomeCategory($userId, $categoryId, $newCategoryName)
    {
        $response = static::validateCategoryIncomes($userId, $newCategoryName);
        $newCategoryName = ucfirst($newCategoryName); 

        if ($response["message_type"] != "error") {
            $sql = 'UPDATE `incomes_category_assigned_to_users`
                    SET `name`= :newCategory WHERE `user_id` = :userId AND `id` = :categoryId
                    LIMIT 1';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':newCategory', $newCategoryName, PDO::PARAM_STR);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria edytowana.";
        }
        return $response;
    }

    public static function addExpenseCategory($userId, $expenseCategory)
    {
        $response = static::validateCategoryExpenses($userId, $expenseCategory);
        $expenseCategory = ucfirst($expenseCategory);

        if ($response["message_type"] != "error") {
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
                    VALUES (:userId, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria dodana.";
        }

        return $response;
    }

    public static function editExpenseCategory($userId, $categoryId, $newCategoryName)
    {
        $response = static::validateCategoryExpenses($userId, $newCategoryName);
        $newCategoryName = ucfirst($newCategoryName); 

        if ($response["message_type"] != "error") {
                $sql = 'UPDATE `expenses_category_assigned_to_users`
                        SET `name` = :newCategory WHERE `user_id` = :userId AND `id` = :categoryId
                        LIMIT 1';


            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':newCategory', $newCategoryName, PDO::PARAM_STR);

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

    public static function removeExpenseCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'DELETE FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :userId AND `id` = :categoryId
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        static::removeCategoryExpenses($userId, $categoryId);

        $response["message_type"] = "success";
        $response["message"] = "Kategoria usunięta.";

        return $response;
    }

    private static function removeCategoryExpenses($userId, $categoryId)
    {
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :userId AND `expense_category_assigned_to_user_id` = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();
    }

}
