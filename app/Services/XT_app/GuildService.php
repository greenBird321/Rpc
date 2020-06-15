<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2020/6/12
 * Time: 7:41 PM
 */
namespace Xt\Rpc\Services\XT_app;

use Xt\Rpc\Core\Service;

class GuildService extends Service {

    public function getGuild($parameter)
    {
        if (!isset($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        if (!empty($parameter['guild_id'])) {
            $sql = "SELECT
	gc.GuildID,
	gc.CreaterID,
	gc.GuildName,
	gc.GuildLeaderID,
	gc.GuildDeclaration,
	gc.GuildLevel,
	gc.GuildContributionPoint,
	gc.GuildJoinLevel,
	gm.RoleID,
	gm.ServerID,
	gm.GuildGrade,
	gm.RecentActivityPoint 
FROM
	GuildCommon AS gc,
	GuildMember AS gm 
WHERE
	gc.ServerID = gm.ServerID 
	AND gc.GuildID = gm.GuildID 
	AND gc.GuildID = {$parameter['guild_id']}";
            $r = $this->gameDb($parameter['zone'].'_guild')->fetchAll($sql);
        } elseif (!empty($parameter['guild_name'])) {
            $sql = "SELECT
	gc.GuildID,
	gc.CreaterID,
	gc.GuildName,
	gc.GuildLeaderID,
	gc.GuildDeclaration,
	gc.GuildLevel,
	gc.GuildContributionPoint,
	gc.GuildJoinLevel,
	gm.RoleID,
	gm.ServerID,
	gm.GuildGrade,
	gm.RecentActivityPoint 
FROM
	GuildCommon AS gc,
	GuildMember AS gm 
WHERE
	gc.ServerID = gm.ServerID 
	AND gc.GuildID = gm.GuildID 
	AND ( SELECT GuildID FROM GuildCommon WHERE GuildName = '{$parameter['guild_name']}' ) = gm.GuildID";
            $r = $this->gameDb($parameter['zone'].'_guild')->fetchAll($sql);
        }


    }
}