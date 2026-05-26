<?php
namespace App\Model;

use App\Database\Db;

class Province extends Db {

	public function getAllSub() {
		
		$sql = "SELECT persons.sub_district FROM persons ";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	
	}
	public function getAllAmphures() {
		
		$sql = "SELECT * FROM amphures ";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	
	}
	public function getAllDistr() {
		$sql = "
			SELECT
				*
			FROM 
				districts
		
			
		";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	
	}

}

?>