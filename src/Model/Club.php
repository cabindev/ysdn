<?php
namespace App\Model;

use App\Database\Db;

class Club extends Db {

	public function getClub() {
		$sql = "
			SELECT
                *
			FROM 
               clubs
		
		";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	}

}
?>