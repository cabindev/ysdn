<?php
namespace App\Model;

use App\Database\Db;

class Geo extends Db {

	public function getAllGeo() {
		$sql = "
			SELECT
				geography.GEO_ID,
				geography.GEO_NAME
			FROM 
				geography
			ORDER BY
			
				geography.GEO_NAME
		";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	}

}

?>