<?php

namespace App\Models;

use PDO;

class Payform extends \Core\Model
{
    public static function getNamePayform($user_id)
    {
        $sql = 'SELECT payment_methods_assigned_to_users.id, payment_methods_assigned_to_users.name
                FROM payment_methods_assigned_to_users
                WHERE payment_methods_assigned_to_users.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addPaymentCategory($userId, $paymentCategoryName)
    {
        $response = static::validateCategory($userId, $paymentCategoryName);
        $paymentCategoryName = ucfirst($paymentCategoryName); 

        if ($response["message_type"] != "error") {
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $paymentCategoryName, PDO::PARAM_STR);

            $stmt->execute();

            $response["message_type"] = "success";
            $response["message"] = "Kategoria dodana.";
        }

        return $response;
    }

    public static function editPaymentCategory($userId, $categoryId, $newCategoryName)
    {
        $response = static::validateCategory($userId, $newCategoryName);
        $newCategoryName = ucfirst($newCategoryName); 

        if ($response["message_type"] != "error") {
            $sql = 'UPDATE `payment_methods_assigned_to_users`
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

    private static function validateCategory($userId, $paymentCategoryName)
    {
        $response = ["message_type" => "", "message" => ""];
        $categoryToUpper = strtoupper($paymentCategoryName);

        $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $categoryToUpper);

        if ($result == 1) {
            $response["message_type"] = "error";
            $response["message"] = "Niedozwolone znaki w nazwie.";
        }

        if (static::checkIfPaymentcategoryExists($userId, $categoryToUpper)) {
            $response["message_type"] = "error";
            $response["message"] = "Nazwa tej kategorii jest zajęta.";
        }

        return $response;
    }

    private static function checkIfPaymentcategoryExists($userId, $paymentCategoryName)
    {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id) a WHERE name = :payment_method';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':payment_method', $paymentCategoryName, PDO::PARAM_STR);

        $stmt->execute();

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        return true;
    }

    public static function checkRemovePaymentCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'SELECT id FROM expenses
                WHERE user_id = :userId AND payment_method_assigned_to_user_id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($fetchArray)) {
            return static::removePaymentCategory($userId, $categoryId);
        }

        $response["message_type"] = "warning";
        $response["message"] = "Kategoria metod płatności zawiera wartości.";

        return $response;
    }

    public static function removePaymentCategory($userId, $categoryId)
    {
        $response = ["message_type" => "", "message" => ""];

        $sql = 'DELETE FROM `payment_methods_assigned_to_users`
                WHERE `user_id` = :userId AND `id` = :categoryId
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        static::removePaymentCategoryExpenses($userId, $categoryId);

        $response["message_type"] = "success";
        $response["message"] = "Kategoria usunięta.";

        return $response;
    }

    private static function removePaymentCategoryExpenses($userId, $categoryId)
    {
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :userId AND `payment_method_assigned_to_user_id` = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();
    }
}
