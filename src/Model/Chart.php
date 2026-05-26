<?php
namespace App\Model;

namespace App\Model;

use App\Database\Db;
class Chart extends Db {
    public function getAllType($region = '')
    {
        $sql = "
        SELECT
            users.type,
            COUNT(*) AS count
        FROM
            users
        WHERE 1=1
    ";

        if (!empty($region)) {
            $sql .= " AND users.region = :region";
        }

        $sql .= "
        GROUP BY
            users.type
        ORDER BY
            COUNT(*) DESC
    ";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($region)) {
            $stmt->bindParam(':region', $region);
        }

        $stmt->execute();
        $typeData = $stmt->fetchAll();
        return $typeData;
    }

    public function getAllGender()
    {
        $sql = "
        SELECT
            gender_id,

            COUNT(*) AS count
        FROM
            users

        GROUP BY
            gender_id
    
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $genderData = $stmt->fetchAll();

        return $genderData;
    }

    public function getLevelsStatistics()
    {
        $sql = "
        SELECT
            level,
            COUNT(*) AS count
        FROM
            users
        GROUP BY
            level
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $levelData = $stmt->fetchAll();

        return $levelData;
    }
    
    public function getReligionStatistics()
    {
        $sql = "
        SELECT
            religion,
            COUNT(*) AS count
        FROM
            users
        GROUP BY
            religion
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $religionData = $stmt->fetchAll();

        return $religionData;
    }
    
    public function getBloodTypeStatistics()
    {
        $sql = "
        SELECT
            blood_type,
            COUNT(*) AS count
        FROM
            users
        GROUP BY
            blood_type
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $bloodData = $stmt->fetchAll();

        return $bloodData;
    }
    
    
}

?>