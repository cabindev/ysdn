<?php
namespace App\Model;

use App\Database\Db;

class Ref extends Db {

	public function getRefsByGroupId($groupId) {
		$sql = "
			SELECT
				refs.ref_id,
				refs.ref_title
			FROM 
				refs
			WHERE
				refs.ref_group_id = '{$groupId}'
			ORDER BY
				ref_id
		";
		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		return $data;
	}

}
?>