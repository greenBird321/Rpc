<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2020/6/15
 * Time: 4:52 PM
 */
namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;

class Guild extends Model {

    public function getRoleByRolesId($role_id, $db)
    {
        if (is_array($role_id)) {
            $roles_id = implode(',', $role_id);
        } else {
            $roles_id = $role_id;
        }
        // 目前只查出玩家的name
        $sql = "SELECT `RoleID`,`name`, `PlayerLv`, `MPower`,`LogoutTime`
FROM
	BasicRes 
WHERE
	RoleID IN (
		{$roles_id}
	)";
        $result = $db->fetchAll($sql);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

}