<?php
namespace App\Helper;

class ImageHelper {
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    private const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    /**
     * Validate an uploaded file array ($_FILES entry).
     * Returns the real MIME type on success, throws on failure.
     */
    public static function validateUpload(array $file): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload error code: ' . $file['error']);
        }

        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException('ขนาดไฟล์เกิน 5 MB');
        }

        // ตรวจ MIME จาก content จริง ไม่ใช่นามสกุล
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mime, self::ALLOWED_MIME)) {
            throw new \RuntimeException('ประเภทไฟล์ไม่ถูกต้อง รองรับ: jpg, png, gif, webp');
        }

        return $mime;
    }

    /**
     * Generate a safe unique filename with the correct extension from MIME.
     */
    public static function uniqueFilename(string $mime): string {
        return md5(uniqid('', true)) . '.' . self::ALLOWED_MIME[$mime];
    }

    public static function compress(string $source, string $destination, string $mime, int $quality = 75): bool {
        $image = self::createFromFile($source, $mime);
        if ($image === false) return false;

        self::saveImage($image, $destination, $mime, $quality);
        imagedestroy($image);
        return true;
    }

    public static function resize(string $source, string $destination, string $mime, int $maxWidth, int $maxHeight): bool {
        $image = self::createFromFile($source, $mime);
        if ($image === false) return false;

        [$width, $height] = getimagesize($source);
        $ratio = $width / $height;

        if ($maxWidth / $maxHeight > $ratio) {
            $maxWidth = (int) round($maxHeight * $ratio);
        } else {
            $maxHeight = (int) round($maxWidth / $ratio);
        }

        $new = imagecreatetruecolor($maxWidth, $maxHeight);

        // preserve transparency for PNG/GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $image, 0, 0, 0, 0, $maxWidth, $maxHeight, $width, $height);
        self::saveImage($new, $destination, $mime, 75);

        imagedestroy($image);
        imagedestroy($new);
        return true;
    }

    private static function createFromFile(string $source, string $mime) {
        return match($mime) {
            'image/jpeg' => imagecreatefromjpeg($source),
            'image/png'  => imagecreatefrompng($source),
            'image/gif'  => imagecreatefromgif($source),
            'image/webp' => imagecreatefromwebp($source),
            default      => false,
        };
    }

    private static function saveImage($image, string $destination, string $mime, int $quality): void {
        match($mime) {
            'image/jpeg' => imagejpeg($image, $destination, $quality),
            'image/png'  => imagepng($image, $destination, (int) round($quality / 10)),
            'image/gif'  => imagegif($image, $destination),
            'image/webp' => imagewebp($image, $destination, $quality),
            default      => null,
        };
    }
}
