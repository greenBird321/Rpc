<?php
/**
 * 公会controller.
 * User: lihe
 * Date: 2020/6/12
 * Time: 7:37 PM
 */
namespace Xt\Rpc\Controllers;

class GuildController extends ControllerBase {

    public function getGuild() {
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['guild_id'] = $this->request->query->get('guild_id');
        $parameter['guild_name'] = $this->request->query->get('guild_name');

        return $this->api('Guild', __FUNCTION__, $parameter);
    }
}