<?php
namespace App\Model;

use App\Database\Db;

class Person extends Db {

	public function getAllPersons($filters=[]) {

		$where = ""; //$where รับค่ามาจาก $fillter เป็นค่าว่างก่อน เมื่อถูกเรียกใช้ค่อยเข้าเงื่อนไข  if ด้านล่าง

		if (isset($filters['search'])) {
			$where .= " AND ( 
				persons.firstname LIKE :search 
				OR persons.nickname LIKE :search
			) ";
			$filters['search'] = "%{$filters['search']}%";
		}else{
			unset($filters['search']);
		}
			// User ต้องการดูว่าสมาชิก เพศ อะไรเมื่อเลือกแล้วจะส่ง $_REQUES มาเก็บที่ $fillter ดำเนินการต่อด้วย $where
		if (isset($filters['gender_id'])) {
			$where .= " AND persons.gender_id = :gender_id ";
		}else{
			unset($filters['gender_id']);
		}
			// User ต้องการดูว่าสมาชิก จากภาคใหน เมื่อเลือกแล้วจะส่ง $_REQUES มาเก็บที่ $fillter ดำเนินการต่อด้วย $where
		if (isset($filters['geographies.id'])) {
			$where .= " AND persons.geo = :geo";
		}else{
			unset($filters['geo']);
		}
			//เมื่อไม่ได้เลือกอะไรเลยให้เป็นค่าว่าง แล้ว unset ออกไป
		$sql = "
			SELECT
				persons.id,
				persons.firstname,
				persons.lastname,
				persons.nickname,
				persons.dob,
				persons.phone,
				persons.avatar,
				refs.ref_title AS gender,
				persons.address,
				persons.district,
				persons.amphoe,
				persons.province,
				persons.zipcode,
				persons.member_code,
				persons.province_code


			FROM 
				persons
				LEFT JOIN refs ON persons.gender_id = refs.ref_id
				
				

			
			WHERE
				persons.id > 0
				{$where}
			ORDER BY 
				persons.id DESC
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($filters);
		$data = $stmt->fetchAll();
		
		return $data;
		
	}

	public function addPerson($person) {
		$sql = "
			INSERT INTO persons (
				firstname, 
				lastname, 
				nickname, 
				dob, 
				gender_id, 
				avatar, 
				phone,
				address,
				district,
				amphoe,
				province,
				zipcode,
				province_code,
				member_code
			) VALUES (
				:firstname, 
				:lastname, 
				:nickname, 
				:dob, 
				:gender_id, 
				:avatar, 
				:phone,
				:address,
				:district,
				:amphoe,
				:province,
				:zipcode,
				:province_code,
				:member_code
		
			
			)
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($person);
		return $this->pdo->lastInsertId();
	}


	// เช็ค member_code เพื่อนำรูปไปแสดงที่ Profile
	public function checkPerson($person) {
		$sql = "
			SELECT 
				persons.id,
				persons.firstname,
				persons.lastname,
				persons.nickname,
				persons.dob,
				persons.phone,
				persons.avatar,
				refs.ref_title AS gender,
				persons.address,
				persons.district,
				persons.amphoe,
				persons.province,
				persons.zipcode,
				persons.member_code,
				persons.province_code,
				users.email
			FROM persons
				LEFT JOIN refs ON persons.gender_id = refs.ref_id
				LEFT JOIN users ON persons.member_code = users.member_code
			WHERE persons.member_code = :member_code
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':member_code', $person['member_code']);
		$stmt->execute();
		$data = $stmt->fetchAll();
		
		if (count($data) > 0) {
			$personDB = $data[0];

			if ($person['member_code'] === $personDB['member_code']) {
				session_start();
				$_SESSION['id'] = $personDB['id'];
				$_SESSION['avatar'] = $personDB['avatar'];
				$_SESSION['member_code'] = $personDB['member_code'];

				return $personDB;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	
	public function updatePerson($person) {
		$sql = "
			UPDATE persons SET
				firstname = :firstname, 
				lastname = :lastname, 
				nickname = :nickname, 
				dob = :dob,
				gender_id = :gender_id, 
				avatar = :avatar, 
				phone = :phone,
				address = :address,
				district =  :district,
				amphoe = :amphoe,
				province = :province,
				zipcode =  :zipcode,
				province_code =  :province_code,
				member_code =  :member_code

			WHERE id = :id
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($person);
		return true;
	}

	public function deletePerson($id) {
		$sql = "
			DELETE FROM persons WHERE id = ?
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$id]);
		return true;
	}
//ดึงข้อมูลคนเดียวเพื่อนำมาแก้ไข ให้ค่าเดิมยังคงอยู่ โดยใช้ class Person
	public function getPersonById($id) {
		$sql = "
			SELECT
				persons.id,
				persons.firstname,
				persons.lastname,
				persons.nickname,
				persons.dob,
				persons.phone,
				persons.gender_id,
				persons.avatar,
				persons.address,
				persons.district,
				persons.amphoe,
				persons.province,
				persons.zipcode,
				persons.province_code,
				persons.member_code
			
			FROM 
				persons
			WHERE 
				persons.id = ?
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$id]);
		$data = $stmt->fetchAll();
		return $data[0];
	}
}
?>