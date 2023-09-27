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

}
