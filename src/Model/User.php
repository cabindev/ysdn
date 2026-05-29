<?php

namespace App\Model;

use App\Database\Db;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PDO;
use PDOException;
class User extends Db
{
    public function createUser($user)
    {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        $user['token'] = $this->generateToken();
        $user['reset_token'] = null;
        $user['member_code'] = $this->generateMemberCode();

        if (!isset($user['zipcode'])) {
            $user['zipcode'] = '';
        }

        $sql = "
            INSERT INTO users (
                member_code,
                name,
                email,
                password,
                token,
                reset_token,
                firstname,
                lastname,
                nickname,
                dob,
                citizen_id,
                gender_id,
                address,
                district,
                amphoe,
                province,
                zipcode,
                province_code,
                type,
                phone,
                avatar,
                level,
                religion,
                blood_type
            ) VALUES (
                :member_code,
                :name,
                :email,
                :password,
                :token,
                :reset_token,
                :firstname,
                :lastname,
                :nickname,
                :dob,
                :citizen_id,
                :gender_id,
                :address,
                :district,
                :amphoe,
                :province,
                :zipcode,
                :province_code,
                :type,
                :phone,
                :avatar,
                :level,
                :religion,
                :blood_type
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($user);

        session_start();
        $id = $this->pdo->lastInsertId();
        $_SESSION['id'] = $id;
        $_SESSION['member_code'] = $user['member_code'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'member';
        $_SESSION['login'] = true;
        $_SESSION['avatar'] = $user['avatar'];

        return true;
    }
    
        // อัพเดทเฉพาะข้อมูล user คนเดียว
        public function updateUser($userData)
    {
        $sql = "
            UPDATE users SET
                firstname = :firstname,
                lastname = :lastname,
                nickname = :nickname,
                dob = :dob,
                gender_id = :gender_id,
                avatar = :avatar,
                phone = :phone,
                address = :address,
                district = :district,
                amphoe = :amphoe,
                province = :province,
                zipcode = :zipcode,
                province_code = :province_code,
                type = :type,
                member_code = :member_code,
                level = :level,
                religion = :religion,
                blood_type = :blood_type
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'firstname' => $userData['firstname'],
            'lastname' => $userData['lastname'],
            'nickname' => $userData['nickname'],
            'dob' => $userData['dob'],
            'gender_id' => $userData['gender_id'],
            'avatar' => $userData['avatar'],
            'phone' => $userData['phone'],
            'address' => $userData['address'],
            'district' => $userData['district'],
            'amphoe' => $userData['amphoe'],
            'province' => $userData['province'],
            'zipcode' => $userData['zipcode'],
            'province_code' => $userData['province_code'],
            'type' => $userData['type'],
            'member_code' => $userData['member_code'],
            'id' => $userData['id'],
            'level' => $userData['level'],
            'religion' => $userData['religion'],
            'blood_type' => $userData['blood_type']
        ]);

        return true;
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return true;
    }

    public function getUserById($id)
    {
        $sql = "
            SELECT
                users.id,
                users.name,
                users.email,
                users.password,
                users.member_code,
                users.role,
                users.token,
                users.reset_token,
                users.firstname,
                users.lastname,
                users.nickname,
                users.citizen_id,
                users.dob,
                gender_id,
                users.address,
                users.district,
                users.amphoe,
                users.province,
                users.zipcode,
                users.province_code,
                users.type,
                users.phone,
                users.avatar,
                users.level,
                users.religion,
                users.blood_type
            FROM
                users
            WHERE
                users.id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch();
        return $userData;
    }
        
        public function getUserCount(){
        $sql = "SELECT COUNT(*) AS userCount FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            return $result['userCount'];
        }

        return 0;
    }
    // เพิ่มเงื่อนไขการ เช็คข้อมูลด้วย type ที่แยกประเภทภาค ในหน้า Dashboard datatable
    public function getAllUser($type = '')
    {
        $sql = "
            SELECT
                users.id,
                users.name,
                users.email,
                users.password,
                users.member_code,
                users.role,
                users.token,
                users.reset_token,
                users.firstname,
                users.lastname,
                users.nickname,
                users.citizen_id,
                users.dob,
                gender_id,
                users.address,
                users.district,
                users.amphoe,
                users.province,
                users.zipcode,
                users.province_code,
                users.type,
                users.phone,
                users.avatar
            FROM
                users
            WHERE 1=1";

        if (!empty($type)) {
            $sql .= " AND users.type = :type";
        }
        $sql .= " ORDER BY users.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        
        if (!empty($type)) {
            $stmt->bindParam(':type', $type);
        }
        
        $stmt->execute();
        $userData = $stmt->fetchAll();
        return $userData;
    }
    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    private function generateMemberCode()
    {
        $prefix = 'YSDN';
        $date = date('ymd');
        $sql = "SELECT MAX(member_code) AS max_member_code FROM users";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetch();
        $maxMemberCode = $data['max_member_code'];
        $currentDate = date('ymd');

        if ($maxMemberCode && strpos($maxMemberCode, $currentDate) !== false) {
            $currentMemberCount = intval(substr($maxMemberCode, -4));
            $newMemberCount = $currentMemberCount + 1;
        } else {
            $newMemberCount = 1;
        }

        $newMemberCode = str_pad($newMemberCount, 4, '0', STR_PAD_LEFT);

        return $prefix . $currentDate . $newMemberCode;
    }
    // เช็ค email ไม่ให้ซ้ำในระบบ
    public function checkUserByEmail($email)
    {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data !== false;
    }
    //เช็ค ชืื่อ หรือ อีเมลย์ ก็ได้ ใช้ในการ login 
    public function checkUserByEmailOrName($emailOrName, $password)
    {
        $sql = "
            SELECT
                id,
                member_code,
                name,
                nickname,
                email,
                role,
                password,
                reset_token
            FROM
                users
            WHERE
                users.email = :emailOrName
                OR users.name = :emailOrName
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();
        
        // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        if (empty($data)) {
            return false;
        }
        
        $userDB = $data[0];
    
        if (password_verify($password, $userDB['password'])) {
            session_start();
            $_SESSION['id'] = $userDB['id'];
            $_SESSION['name'] = $userDB['name'];
            $_SESSION['nickname'] = $userDB['nickname'];
            $_SESSION['member_code'] = $userDB['member_code'];
            $_SESSION['email'] = $userDB['email'];
            $_SESSION['role'] = $userDB['role'];
            $_SESSION['login'] = true;
            $_SESSION['reset_token'] = $userDB['reset_token'];
    
            return true;
        } else {
            return false;
        }
    }
    //เช็ค ชืื่อ หรือ อีเมลย์ ก็ได้ ใช้ในการ login แต่ถ้าเช็คไม่มีในฐานข้อมูลให้เด้งออก
    public function checkEmailOrNameExists($emailOrName)
    {
        $sql = "
        SELECT
            id,
            member_code,
            name,
            nickname,
            email,
            role,
            password,
            reset_token
        FROM
            users
        WHERE
            users.email = :emailOrName
            OR users.name = :emailOrName
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();

        // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }
    }

    // การ resetpassword เมื่อรีเซตแล้วจะเป็น null
 public function resetPassword($token, $password)
{
    $sql = "
        SELECT id FROM users 
        WHERE reset_token = :token 
          AND reset_token_created_at >= NOW() - INTERVAL 1 HOUR
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $data = $stmt->fetch();

    if ($data) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "
            UPDATE users
            SET password = :password, reset_token = NULL
            WHERE id = :id AND reset_token = :token
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'password' => $hashedPassword,
            'id' => $data['id'],
            'token' => $token
        ]);

        return true;
    } else {
        return false;
    }
}

    
    public function sendPasswordResetLink($email)
    {
        $token = $this->generateToken();
        $this->updateResetToken($email, $token);
        $this->sendResetEmail($email, $token);
    }

    private function updateResetToken($email, $token)
{
    $sql = "
        UPDATE users
        SET reset_token = :resetToken, reset_token_created_at = NOW()
        WHERE email = :email
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'resetToken' => $token,
        'email' => $email
    ]);
}

// ในกรณีทำการทดสอบการส่ง $link email อย่าลือมเปลี่ยน จาก localhost เป็น host
private function sendResetEmail($email, $token)
{
    $to = $email;
    $subject = 'Password Reset Request';
    $link = "http://ysdnthailand.com/ysdn/auth/changPassword.php?token=$token";
    $message = "Click the link below to change your password:<br><a href=\"$link\">กดที่นี่ เพื่อเปลี่ยนรหัสผ่าน</a>";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_FROM'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = "UTF-8";

        $mail->setFrom($_ENV['MAIL_FROM']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo 'Password reset link has been sent to your email.';
    } catch (Exception $e) {
        echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
    }
}

    // เช็คสถานะ  admin
    public function getAllAdmins(){
        $sql = "SELECT avatar, nickname FROM users WHERE role = 'admin'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $admins = $stmt->fetchAll();
    
        return $admins;
    }

    public function updateUserStatus($userId, $newStatus)
    {
        try {
            $sql = "UPDATE users SET role = :newStatus WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute(); // อัปเดตฐานข้อมูลทันที
            return true;
        } catch (PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            return false;
        }
    }
    public function checkUserByCitizenId($citizenId)
    {
        $sql = "SELECT id FROM users WHERE citizen_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$citizenId]);
        $data = $stmt->fetch();

        return $data !== false;
    }

    
}

?>