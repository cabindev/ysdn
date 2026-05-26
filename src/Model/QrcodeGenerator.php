<?php

namespace App\Model;

use App\Database\Db;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrcodeGenerator extends Db
{
    public function generateQrCode($data)
    {
        $qrCode = new QrCode($data);
        $qrCode->setSize(300);

        $writer = new PngWriter();
        $qrCodeString = $writer->write($qrCode)->getString();

        $qrCodeBase64 = base64_encode($qrCodeString);

        return $qrCodeBase64;
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
                users.token,
                users.reset_token,
                users.firstname,
                users.lastname,
                users.nickname,
                users.citizen_id,
                users.dob,
                refs.ref_title AS gender_id,
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
            LEFT JOIN refs ON users.gender_id = refs.ref_id
            WHERE
                users.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch();

        if ($userData) {
            $qrCodeImage = $this->generateQrCode(json_encode($userData));
            $userData['qrCodeImage'] = $qrCodeImage;
        }

        return $userData;
    }
}

?>
