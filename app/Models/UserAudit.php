<?php
namespace TopBetta;

class UserAudit extends \Eloquent {
	protected $table = 'tbdb_user_audit';

	protected $guarded = array();

	public static $rules = array();

	public static function getRecentUserAuditByUserIDAndFieldName($user_id, $field_name) {

		if (!is_array($field_name)) {
			$field_name = array($field_name);
		}

		$query = 'SELECT
				id,
				user_id,
				admin_id,
				field_name,
				old_value,
				new_value,
				update_date
			FROM tbdb_user_audit
			WHERE
				user_id = "' . $user_id . '"
			AND
				field_name IN("' . implode('", "', $field_name) . '")
			ORDER BY
				update_date DESC
			LIMIT 1';

		$result = \DB::select($query);

		if (count($result)) {
			
			return $result[0];					
			
		} else {
			
			return null;
			
		}

	}

}
