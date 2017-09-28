<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/17
 * Time: 12:23
 */

/**
 * XunSearch搜索工具类
 */
namespace Org\Util;


class XSUtil
{
    /**
     *  实例化xunsearch对象
     * @var
     */
    private static $instance;

    /**
     * 过滤条件
     * @var
     */
    public $filter;

    private static $xs;

    private static $where = '';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 生成xunsearch对象：单例实现
     */
    public static function getInstance($config_name)
    {
        vendor("XS.vendor.autoload");
        if (!self::$instance instanceof XSUtil) {
            self::$instance = new \XS($config_name);
          }
        return self::$instance;
    }

    /**
     *  查询结果集
     * @param $config 配置文件
     * @param $name 关键词
     * @param array $params 配置参数
     * @return array 查询结果集+结果集数量
     */
    public static function search($config,$name='',$params=array()){
        self::$xs = self::getInstance($config);
        $searchObj = self::$xs->search;
        if(!empty($params['filter'])&&is_array($params['filter'])){
            foreach ($params['filter'] as $key=>$val){
                if(!empty($val)){
                    self::$where.=" {$key}:{$val}";
                }
            }
        }
        if(!empty($params['sort']&&is_array($params['sort']))){
            $searchObj->setSort($params['sort'][0],$params['sort'][1]=='desc'?false:true);
        }

        $page = empty($params['page'])?1:intval($params['page']);
        $pageSize=isset($params['pageSize'])?$params['pageSize']:C('PAGESIZE');
        $searchObj->setLimit($pageSize,$pageSize * ($page-1));
        if($params['accurate']!=true){
            $searchObj->setFuzzy();
        }
        $searchObj->setQuery($name)->setFacets(array_keys($params['filter']),true)->addQueryString(self::$where,0);
//        var_dump($params['timeRange']);die;
        if(!empty($params['timeRange']) && is_array($params['timeRange']) ){
            $searchObj->addRange($params['timeRange'][0],$params['timeRange'][1],$params['timeRange'][2]);
        }
        $docs = $searchObj ->search();
        $data=$count=array();

        if(!empty($docs)&&is_array($docs)){
            if(!empty($params['filter'])&&is_array($params['filter'])) {
                foreach ($params['filter'] as $key => $val) {
//                    var_dump($searchObj->getFacets($key));die;
//                    $count[$key . "_count"] =arraySequence($searchObj->getFacets($key));
                    $count[$key . "_count"] =$searchObj->getFacets($key);
                    if($key=='v_parent_guid'){
                        ksort($count[$key . "_count"],SORT_NATURAL);
                    }
                }
            }
            foreach ($docs as $k=>$v){

                if(!is_array($params['display_keys'])||empty($params['display_keys'])){
                    return;
                }
                foreach ($params['display_keys'] as $key=>$value){
                    $data[$k][$key] = $value==1?$searchObj->highlight($v->$key): $v->$key;
                }
                $data[$k]['v_keywords']=dealKeywords($docs[$k]['v_keywords']);
            }

        }
        $count['total_count'] = $searchObj->lastCount;
        if($params['recommend']==true){
            $count['recommend_count']=$searchObj->getHotQuery(5,'currnum');
        }
        return array(
            'data'=>$data,
            'count'=>$count,
            'page'=>$page,
            'pageSize'=>$pageSize
        );
    }

    /**
     * 获取分词结果
     * @param $config xunseearch项目名
     * @param $text  要分词的字符串
     */
    public static function getResult($config,$text){
        self::$xs = self::getInstance($config);
        $tokenizer=new \XSTokenizerScws;
        $words = $tokenizer->getResult($text);
        dump($words);
    }

//    public function getCustomDict($config){
//        self::$xs = self::getInstance($config);
//        $tokenizer=new \XSTokenizerScws;
//        XSIndex::getCustomDict
//    }

    /**
     *获取建议词
     * @param $config xunseearch项目名
     * @param $searchWord 查询的字符串
     * @return array
     */
    public static function getExpandedQuery($config,$searchWord)
    {
        self::$xs = self::getInstance($config);
        return self::$xs->search->getExpandedQuery($searchWord,10);
    }

    public static function getRelatedQuery($config,$searchWord){
        self::$xs = self::getInstance($config);
        $words=self::$xs->search->getRelatedQuery($searchWord,20);
        $array=array();
        foreach($words as $k=>$word){
            if(mb_strlen($word,'utf-8')>2 && count($array)<10){
                $array[]=$word;
            }
        }
        return $array;
    }


    public function getExpertResult($config,$searchStr){
        self::$xs = self::getInstance($config);
        dump($searchStr);
        $search=self::$xs->search;
        return $search->search('八路军');
    }




}