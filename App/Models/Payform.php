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
}
