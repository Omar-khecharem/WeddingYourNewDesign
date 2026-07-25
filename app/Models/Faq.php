<?php
namespace App\Models;

use App\Core\Model;

class Faq extends Model
{
    protected static string $table = 'sg_faq';
    protected static string $primaryKey = 'id';

    public static function getActive(): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM sg_faq WHERE status = 1 ORDER BY sort_order ASC, created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
