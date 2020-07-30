<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2020/6/12
 * Time: 7:41 PM
 */

namespace Xt\Rpc\Services\XT_app;

use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Guild;

class GuildService extends Service
{
    private $guildModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->guildModel = new Guild();
    }

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
	* 
FROM
	(
	SELECT
		gc.GuildID,
		gc.CreaterID,
		gc.GuildName,
		gc.GuildLeaderID,
		gc.GuildDeclaration,
		gc.GuildLevel,
		gc.GuildContributionPoint,
		gc.GuildJoinLevel,
		gc.CreateTime,
		gc.GuildLevelExp,
		gm.RoleID,
		gm.ServerID,
		gm.GuildGrade,
		gm.RecentActivityPoint 
	FROM
		GuildCommon AS gc
		LEFT JOIN GuildMember AS gm ON gc.ServerID = gm.ServerID 
		AND gc.GuildID = gm.GuildID 
	) gg 
WHERE
	gg.GuildID = {$parameter['guild_id']}";
            $r   = $this->gameDb($parameter['zone'] . '_guild')->fetchAll($sql);
        } elseif (!empty($parameter['guild_name'])) {
            $sql = "SELECT
	* 
FROM
	(
	SELECT
		gc.GuildID,
		gc.CreaterID,
		gc.GuildName,
		gc.GuildLeaderID,
		gc.GuildDeclaration,
		gc.GuildLevel,
		gc.GuildContributionPoint,
		gc.GuildJoinLevel,
		gc.CreateTime,
		gc.GuildLevelExp,
		gm.RoleID,
		gm.ServerID,
		gm.GuildGrade,
		gm.RecentActivityPoint 
	FROM
		GuildCommon gc
		LEFT JOIN GuildMember  gm ON gc.ServerID = gm.ServerID 
		AND gc.GuildID = gm.GuildID 
	) gg 
WHERE
	gg.GuildName = '{$parameter['guild_name']}'";
            $r   = $this->gameDb($parameter['zone'] . '_guild')->fetchAll($sql);
        }

        if (empty($r)) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $role_id = [];
        $creater_id = [];
        foreach ($r as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k == 'RoleID') {
                    $role_id[] = $v;
                }
                if ($k == 'CreaterID') {
                    $creater_id = $v;
                }
            }
            $r[$key]['CreateTime'] = date('Y-m-d H:i:s');
        }
        // 通过role_id查询玩家详细信息
        $role = $this->guildModel->getRoleByRolesId($role_id, $this->gameDb($parameter['zone']));
        // 通过role_id查询创建人信息
        $creater = $this->guildModel->getRoleByRolesId($creater_id, $this->gameDb($parameter['zone']));
        if (!$role) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        $player = [];
        // 拼装公会成员属性
        foreach ($r as $k => $v) {
            foreach ($role as $i => $j) {
                if ($v['RoleID'] == $j['RoleID']) {
                    $player[$k]['PlayerName'] = trim($j['PlayerName'], ';');
                    $player[$k]['PlayerLv'] = explode(';', $j['PlayerLevel'])[0];
                    $player[$k]['MPower'] = empty($j['FightPow5Hero']) ? '' : explode(';', $j['FightPow5Hero'])[0];
                    $player[$k]['Create_time'] = date('Y-m-d H:i:s', trim($j['CreateTime'], ';'));
                    $player[$k]['GuildGrade'] = $v['GuildGrade'];
                }

                if ($v['CreaterID'] == $creater[0]['RoleID']) {
                    $r[$k]['CreaterName'] = trim($creater[0]['PlayerName'], ';');
                }
            }
        }
        
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'guildData' => $r,
                'playerData' => $player
            ]
        ];
    }


    public function getGuildNews($parameter)
    {
        if (!isset($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        try{
            $sql = "SELECT NewsType, NewsParam, `Timestamp` FROM `GuildNews` WHERE GuildID={$parameter['guild_id']} ORDER BY `Timestamp` DESC";
            $news = $this->gameDb($parameter['zone'].'_guild')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $news
        ];
    }
}