<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/12
 * Time: 16:10
 */

namespace Home\Model;


use Think\Model;

class ResourceInfoModel extends Model
{
    public function getDownloadDataByGuid($map){
            return $this->table('tb_resource_info as a,tb_newspaper as b')->field('a.n_id,a.v_newspaper_name,a.d_publish_time,a.v_version,a.n_page,a.n_download_times,b.v_global_guid')->where($map)->where('a.n_newspaper_id=b.n_id')->find();
    }

    public function updateDownloadData($map,$data)
    {
        return $this->where($map)->save($data);
    }

}