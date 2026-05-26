<?php

namespace App\Model;

use App\Database\Db;
use PDO;
use PDOException;
class Activitycms extends Db {

    // public function createActivity($activityData) {
    //     $sql = "
    //         INSERT INTO ysdn_activity (name, date, description, byname, coverimage, category_activity)
    //         VALUES (:name, :date, :description, :byname, :coverimage, :category_activity)
    //     ";
    
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->execute([
    //         "name" => $activityData["name"],
    //         "date" => $activityData["date"],
    //         "description" => $activityData["description"],
    //         "byname" => $activityData["byname"],
    //         "coverimage" => $activityData["coverimage"],
    //         "category_activity" => $activityData["category_activity"]
    //     ]);
    
    //     return true;
    // }
    public function createActivity($activityData) {
        try {
            $sql = "
                INSERT INTO ysdn_activity (name, date, description, byname, coverimage, category_activity)
                VALUES (:name, :date, :description, :byname, :coverimage, :category_activity)
            ";
        
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "name" => $activityData["name"],
                "date" => $activityData["date"],
                "description" => $activityData["description"],
                "byname" => $activityData["byname"],
                "coverimage" => $activityData["coverimage"],
                "category_activity" => $activityData["category_activity"]
            ]);
        
            return true;
        } catch (\PDOException $e) {
            // Log this error to a file or show it somehow
            return false;
        }
    }
    
    public function getActivityById($activityId) {
        $sql = "
            SELECT * FROM ysdn_activity WHERE id = :activityId
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['activityId' => $activityId]);
        $activityData = $stmt->fetch();

        return $activityData;
    }

    public function updateActivity($activityId, $activityData) {
        try {
            $sql = "
                UPDATE ysdn_activity
                SET name = :name, date = :date, description = :description, byname = :byname, coverimage = :coverimage, category_activity = :category_activity
                WHERE id = :activityId
            ";
    
            $stmt = $this->pdo->prepare($sql);
            $activityData['activityId'] = $activityId;
            $stmt->execute($activityData);
    
            return true;
        } catch (\PDOException $e) {
            // Log this error to a file or show it somehow
            return false;
        }
    }
    
    

    public function deleteActivity($activityId) {
        $sql = "DELETE FROM ysdn_activity WHERE id = :activityId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['activityId' => $activityId]);

        return true;
    }
// เช็คสถานะกิจกรรมว่าเปิดรับหรือปืดรับแล้ว
    public function getActivityStatus($is_registration_open) {
        return ($is_registration_open == 1) ? "เปิด" : "ปิด";
    }

    public function getAllActivities($currentPage = 1, $itemsPerPage = 10) {
        $offset = ($currentPage - 1) * $itemsPerPage;
    
        $sql = "SELECT ysdn_activity.*, category_activity.name AS category_name, users.nickname
                FROM ysdn_activity
                LEFT JOIN category_activity ON ysdn_activity.category_activity = category_activity.id
                LEFT JOIN users ON ysdn_activity.id = users.id
                ORDER BY ysdn_activity.id DESC
                LIMIT :offset, :itemsPerPage";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->execute();
        $activityData = $stmt->fetchAll();
    
        return $activityData;
    }

    public function getTotalActivities() {
        $sql = "SELECT COUNT(*) FROM ysdn_activity";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $totalActivities = $stmt->fetchColumn();
        
        return $totalActivities;
    }
 
    public function getActivityIdsByActivityId($activityId)
    {
        $sql = "SELECT DISTINCT activity_id FROM ysdn_activity_registration WHERE activity_id = :activityId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':activityId' => $activityId]);
        $activityIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return $activityIds;
    }

    public function getAllActivitiesCount() {
        $sql = "SELECT COUNT(*) AS total FROM ysdn_activity";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
    
        if ($result) {
            return $result['total'];
        }
    
        return 0;
    }
// ดึงข้อมูลการลงทะเบียนแต่ละกิจกรรมไปแสดง หน้า all_activity.php
    public function getRegisteredUsersCountForAllActivities() {
        try {
            // สมมติว่า ysdn_activity_registrations คือตารางที่ถูกต้อง
            $sql = "SELECT activity_id, COUNT(*) as total FROM ysdn_activity_registration GROUP BY activity_id";
            
            $stmt = $this->pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $counts = [];
            foreach ($results as $result) {
                $counts[$result['activity_id']] = $result['total'];
            }
            
            return $counts;
            
        } catch (PDOException $e) {
            // จัดการกับข้อผิดพลาด
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    
    // ตารางการลงทะเบียนเข้าร่วมกิจกรรมของสมาชิก YSDN
    public function isUserRegistered($userId, $activityId) {
        $sql = "SELECT * FROM ysdn_activity_registration WHERE user_id = :userId AND activity_id = :activityId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId, ':activityId' => $activityId]);
        $registrationData = $stmt->fetch();
    
        return ($registrationData !== false);
    }
    public function registerUserForActivity($userId, $activityId, $registrationData) {
        // ตรวจสอบว่าผู้ใช้ลงทะเบียนกิจกรรมนี้แล้วโดยใช้ฟังก์ชัน isUserRegistered
        if ($this->isUserRegistered($userId, $activityId)) {
            return false; // ผู้ใช้ลงทะเบียนกิจกรรมนี้แล้ว
        }
    
        // ถ้ายังไม่ลงทะเบียน ให้ทำการเพิ่มข้อมูลการลงทะเบียนใหม่
        $sql = "
            INSERT INTO ysdn_activity_registration (user_id, activity_id, food_preference, food_type, medication_type, medical_condition, guardian_fullname, guardian_relationship, guardian_phone)
            VALUES (:userId, :activityId, :foodPreference, :food_type, :medicationType, :medicalCondition, :guardianFullname, :guardianRelationship, :guardianPhone)
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'activityId' => $activityId,
            'foodPreference' => $registrationData['food_preference'],
            'food_type' => $registrationData['food_type'],
            'medicationType' => $registrationData['medication_type'],
            'medicalCondition' => $registrationData['medical_condition'],
            'guardianFullname' => $registrationData['guardian_fullname'],
            'guardianRelationship' => $registrationData['guardian_relationship'],
            'guardianPhone' => $registrationData['guardian_phone'],
        ]);
    
        return true; // ลงทะเบียนสำเร็จ
    }
    // ดึงข้อมูล user ที่เคยลงทะเบียนเข้าร่วมกิจกรรม
    public function getActivitiesByUserId($userId) {
        $sql = "
            SELECT 
                ysdn_activity.*, 
                ysdn_activity_registration.status
            FROM ysdn_activity
            JOIN ysdn_activity_registration ON ysdn_activity.id = ysdn_activity_registration.activity_id
            WHERE ysdn_activity_registration.user_id = :userId
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }
// เช็คสถานะการเข้าร่วมกิจกรรม
public function getUserApprovalStatus($userId) {
    $sql = "
        SELECT 
            ysdn_activity.name AS activity_name,
            ysdn_activity_registration.status
        FROM ysdn_activity_registration
        JOIN ysdn_activity ON ysdn_activity.id = ysdn_activity_registration.activity_id
        WHERE ysdn_activity_registration.user_id = :userId
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $registrationData = $stmt->fetchAll();

    $approvalStatus = [];

    foreach ($registrationData as $data) {
        $activityName = $data['activity_name'];
        $status = $data['status'];
        
        if ($status === 'อนุมัติ') {
            $approvalStatus[$activityName] = 'อนุมัติ';
        } else if ($status === 'ไม่อนุมัติ') {
            $approvalStatus[$activityName] = 'ไม่อนุมัติ';
        }
    }

    return $approvalStatus;
}

public function viewRegistrantsForActivity($activityId)
{
    $sql = "SELECT ysdn_activity.name AS activity_name, ysdn_activity.date AS activity_date,users.id,users.name,users.member_code,users.firstname,users.lastname,users.nickname,
            users.email,users.type, ysdn_activity_registration.status, ysdn_activity_registration.food_preference, 
            ysdn_activity_registration.food_type,ysdn_activity_registration.medication_type, ysdn_activity_registration.medical_condition, 
            ysdn_activity_registration.guardian_fullname, ysdn_activity_registration.guardian_relationship, 
            ysdn_activity_registration.guardian_phone
            FROM ysdn_activity_registration
            INNER JOIN ysdn_activity ON ysdn_activity_registration.activity_id = ysdn_activity.id
            INNER JOIN users ON ysdn_activity_registration.user_id = users.id";

    // ตรวจสอบว่ามีการส่งค่า $activityId มาหรือไม่
    if (!empty($activityId)) {
        $sql .= " WHERE ysdn_activity.id = :activityId";
    }

    $stmt = $this->pdo->prepare($sql);

    $params = array();
    
    // ในกรณีที่มีการส่งค่า $activityId มา
    if (!empty($activityId)) {
        $params['activityId'] = $activityId;
    }

    $stmt->execute($params);

    $result = $stmt->fetchAll();
    return $result;
}


    
    function getRegisteredUsersAllActivity()
{
        // แก้ไขคำสั่ง SQL เพื่อเลือกผู้ลงทะเบียนทุกกิจกรรม
        $sql = "
        SELECT 
            ysdn_activity_registration.id,
            users.member_code,
            users.name,
            users.email,
            users.firstname, 
            users.lastname, 
            users.nickname, 
            users.type, 
            ysdn_activity.name AS activity_name, -- เพิ่มคอลัมน์ชื่อกิจกรรม
            ysdn_activity_registration.food_preference,
            ysdn_activity_registration.food_type,
            ysdn_activity_registration.medication_type,
            ysdn_activity_registration.medical_condition,
            ysdn_activity_registration.guardian_fullname,
            ysdn_activity_registration.guardian_relationship,
            ysdn_activity_registration.guardian_phone,
            ysdn_activity_registration.created_at,
            ysdn_activity_registration.updated_at,
            ysdn_activity_registration.status
        FROM ysdn_activity_registration
        LEFT JOIN users ON ysdn_activity_registration.user_id = users.id
        LEFT JOIN ysdn_activity ON ysdn_activity_registration.activity_id = ysdn_activity.id -- JOIN กับตาราง ysdn_activity
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


public function getRegisteredUsersForActivity($activityId = null)
{
    $sql = "
        SELECT 
            ysdn_activity_registration.id,
            users.member_code,
            users.name,
            users.email,
            users.firstname, 
            users.lastname, 
            users.nickname, 
            users.type, 
            ysdn_activity.name AS activity_name, 
            ysdn_activity_registration.food_preference,
            ysdn_activity_registration.food_type,
            ysdn_activity_registration.medication_type,
            ysdn_activity_registration.medical_condition,
            ysdn_activity_registration.guardian_fullname,
            ysdn_activity_registration.guardian_relationship,
            ysdn_activity_registration.guardian_phone,
            ysdn_activity_registration.created_at,
            ysdn_activity_registration.updated_at,
            ysdn_activity_registration.status
        FROM ysdn_activity_registration
        LEFT JOIN users ON ysdn_activity_registration.user_id = users.id
        LEFT JOIN ysdn_activity ON ysdn_activity_registration.activity_id = ysdn_activity.id";

    // ตรวจสอบว่ามีการส่งค่า $activityId มาหรือไม่
    if (!empty($activityId)) {
        $sql .= " WHERE ysdn_activity.id = :activityId";
    }

    $stmt = $this->pdo->prepare($sql);

    $params = array();
    
    // ในกรณีที่มีการส่งค่า $activityId มา
    if (!empty($activityId)) {
        $params['activityId'] = $activityId;
    }

    $stmt->execute($params);

    return $stmt->fetchAll();
}


public function updateRegistrationStatus($userId, $activityId, $newStatus) {
    // ตรวจสอบว่าผู้ใช้ลงทะเบียนกิจกรรมนี้หรือไม่
    if (!$this->isUserRegistered($userId, $activityId)) {
        return false; // ผู้ใช้ไม่ได้ลงทะเบียนกิจกรรมนี้
    }

    // ตรวจสอบค่าสถานะใหม่ว่าเป็นค่าที่ถูกต้องหรือไม่
    if ($newStatus !== 'อนุมัติ' && $newStatus !== 'ไม่อนุมัติ') {
        return false; // ค่าสถานะใหม่ไม่ถูกต้อง
    }

    // อัพเดตสถานะในฐานข้อมูล
    $sql = "
        UPDATE ysdn_activity_registration
        SET status = :newStatus
        WHERE user_id = :userId AND activity_id = :activityId
    ";

    $stmt = $this->pdo->prepare($sql);
    $result = $stmt->execute([
        'userId' => $userId,
        'activityId' => $activityId,
        'newStatus' => $newStatus,
    ]);

    return $result; // อัพเดตสถานะและส่งผลลัพธ์ของการอัพเดตกลับ
}
// การอัพเดทสถานะกิจกรรม ส่ง1 และ 2 ไปเพื่อ เปิดหรือปิดกิจกรรม
public function updateActivityStatus($id, $newStatus) {
    try {
        $sql = "UPDATE ysdn_activity SET is_registration_open = :newStatus WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage()); 
        return false;
    }
}

// อัพเดทการอนุมัติ หรือ ไม่อนุมัติ การลงทะเบียนร่วมกิจกจกรรม
public function updateActivityStatusregistration($activityId, $newStatus) {
    try {
        $sql = "UPDATE ysdn_activity_registration SET status = :newStatus WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $activityId, PDO::PARAM_INT);
        $stmt->execute(); // อัปเดตฐานข้อมูลทันที
        return true;
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage());
        return false;
    }
}

// แก้ไข ได้ทั้งในตาราง users และ Activitycms แต่ยังไม่เปิดใช้ดีกว่า
public function updateRegistration($id, $updatedData) {
    $firstname = $updatedData['firstname'];
    $lastname = $updatedData['lastname'];

    $sql = "
        UPDATE users AS u
        INNER JOIN ysdn_activity_registration AS registration ON u.id = registration.user_id
        SET 
            u.firstname = :firstname,
            u.lastname = :lastname
        WHERE registration.id = :id
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

public function deleteRegistration($registrationId) {
    try {
        $this->pdo->beginTransaction();
        $sql = "DELETE FROM ysdn_activity_registration WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $registrationId, PDO::PARAM_INT);
        $stmt->execute();
        $this->pdo->commit();

        return true; // สำเร็จและคืนค่า true
    } catch (PDOException $e) {
        $this->pdo->rollback();
        error_log("Error deleting registration: " . $e->getMessage());
        return false; // ไม่สำเร็จและคืนค่า false
    }
}


public function getRegisteredUserById($id) {
    
    $sql = "
        SELECT *
        FROM ysdn_activity_registration
        WHERE id = :id
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    }
    
