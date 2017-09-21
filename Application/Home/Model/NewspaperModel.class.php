<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/25
 * Time: 12:09
 */

namespace Home\Model;


use Think\Model;

class NewspaperModel extends Model
{
    public function getNewspaperData($map,$params = array()){
        $resourceModel=M('resource_info');
        $NewsPaperList=$this->field('v_title,n_id,d_pubtime_year,v_global_guid,v_publish_time')->where($map)->limit($params['offset'],C('PAGENUM'))->select();
        foreach ($NewsPaperList as $key=>&$val){
            $NewsPaperList[$key]['recommend'] = $resourceModel->field('v_title as r_title,n_id as r_id')->where('n_newspaper_id = '.$val['n_id'])->order('RAND()')->limit(4)->select();
            $NewsPaperList[$key]['path']=get_resource_path($val['v_global_guid']);
        }
        $NewspaperCatData=$this->field('d_pubtime_year')->where($map)->group('d_pubtime_year')->order('n_id asc')->select();
        return array(
            'NewsPaperList'=>$NewsPaperList,
            'NewsPaperCatData'=>$NewspaperCatData,
        );
    }

    public function getReadData($map){
        return $this->field('n_total_pages,n_height,n_width,v_title,v_global_guid')->where($map)->find();
    }

}