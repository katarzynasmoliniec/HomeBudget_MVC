<?php

namespace App\Models;

class Category extends \Core\Model
{

    public static function getStartCategory()
    {
        $sql = 'INSERT INTO  incomes_category_assigned_to_users (user_id, name)
                SELECT users.id, incomes_category_default.name 
                FROM users, incomes_category_default where users.id = users.id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        return $stmt->execute();
    }

    
}
