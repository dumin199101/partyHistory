<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\NewspaperModel;
use Org\Util\XSUtil;
use Think\Model;

class IndexController extends Controller {
    /*首页展示*/
    public function index(){
        $catList=$this->categoryList(1,100);
        $this->assign('catList',$catList['data']);
        $this->assign('searchTextMark',1);
        $this->display();

    }

    public function leaderList(){
        $catModel=M('leader');
        $data=$catModel->field('n_id,v_title')->select();
        return $data;
    }

    /*首页展示*/
    public function bookList(){
        $catId=I('get.catId');
        $leaderId=I('get.leaderId');
        $leaderNames=$this->leaderList();
        $catList=$this->categoryList(1,100);
        $this->assign('catList',$catList['data']);
        $data=$this->bookListData(1,$catId,$leaderId);
        $this->assign('leaderNames',$leaderNames);
        $this->assign('data',$data['data']);
        $this->assign('dataCat',$data['catMark']);
        $this->assign('dataLeader',$data['leaderMark']);
        $this->assign('dimensionMark',$data['dimensionMark']);
        $this->assign('catId',$catId);
        $this->assign('leaderId',$leaderId);
        $this->assign('count',$data['count']);
        $this->assign('searchTextMark',2);
        $this->display();

    }
    public function bookListData($page,$catId='',$leaderId=''){
        $pageSize=C('PAGENUM');
        $bookModel=M('book_info');
        if($catId) {
            $map['n_cat_id'] = $catId;
        }
        if($leaderId){
            $map['n_leader_id'] = $leaderId;
        }
        $data['data']=$bookModel->alias('a')
            ->field('a.*,b.v_title as catName,c.v_title as leaderName')
            ->join('left join tb_category as b on a.n_cat_id=b.n_id')
            ->join('left join tb_leader as c on a.n_leader_id=c.n_id')
            ->where($map)->limit($pageSize*($page-1),$pageSize)->select();
        $data['catMark']='';
        if($catId){
            $data['catMark']=$data['data'][0]['catname'];
            $data['dimensionMark']='图书分类';
        }
        if($leaderId){
            $data['leaderMark']=$data['data'][0]['leadername'];
            $data['dimensionMark']='中央领导人';
        }
        $data['count']=$bookModel->where($map)->count();
        return $data;
    }

    public function  bookListAjax(){
        $catId=I('post.catId');
        $leaderId=I('post.leaderId');
        $page=I('post.page');
        $pageSize=C('PAGENUM');
        $data=$this->bookListData($page,$catId,$leaderId);
        $bookAjaxData['count']['total_count']=$data['count'];
        $bookAjaxData['data']=$data['data'];
        $bookAjaxData['pageSize']=$pageSize;
        $bookAjaxData['page']=$page;
        $this->ajaxReturn($bookAjaxData,'json');
    }


    public function bookDetails(){
        $leaderNames=$this->leaderList();
        $bookId=I('get.id');
        $bookModel=M('book_info');
        $bookInfo=$bookModel->alias('a')->field('a.*,b.v_title as catName')->where('a.n_id='.$bookId)->join('tb_category as b on a.n_cat_id= b.n_id')->find();
        $catList=$this->categoryList(1,100);
        $this->assign('leaderNames',$leaderNames);
        $this->assign('catList',$catList['data']);
        $this->assign('bookInfo',$bookInfo);
        $this->assign('searchTextMark',2);
        $this->display();
    }

    public function categoryList($page,$pageSize=20){
        $catModel=M('category');
        $data['data']=$catModel->field('n_id,v_title')->limit($pageSize*($page-1),$pageSize)->select();
        $data['count']=$catModel->count();
        $data['page']=$page;
        $data['moreMark']=0;
        if($page*20<$data['count']){
            $data['moreMark']=1;
        }
        return $data;
    }

    public function  categoryListMoreAjax(){
        $page=I('post.page',1);
        $data=$this->categoryList($page);
        $this->ajaxReturn($data);
    }


    public function bookCatalogAjax(){
        $v_global_guid=I('post.bookId','');
        $page=I('post.page',1);
        $pageSize=C("PAGESIZE");
        $cateLogModel=M('resource_info');
        $cateLog=$cateLogModel->where('v_parent_guid="'.$v_global_guid.'"')->limit($pageSize*($page-1),$pageSize)->order('v_title_sort asc')->select();
        $count=$cateLogModel->where('v_parent_guid="'.$v_global_guid.'"')->count();
        $data=array();
        $data['data']=$cateLog;
        $data['count']['total_count']=$count;
        $data['pageSize']=$pageSize;
        $data['page']=$page;
        $this->ajaxReturn($data,'json');
    }

    public function searchInBookAjax(){
        $config=C('SEARCH.PARTYLITERATURE');
        $name=I('post.searchInBookWord','');
        $page=I('post.page',1);
        $bookId=I('post.bookId',1);

        $searchInBookIsAccurate=I('post.searchInBookIsAccurate','');

        $accurate=false;
        if($searchInBookIsAccurate==1){
            $accurate=true;
            $name='"'.$name.'"';
        }
        $filter=array(

        );
        $bookModel=M('book_info');
        $bookIdArr=$bookModel->field('v_title')->where('v_global_guid="'.$bookId.'"')->find();
        if($bookIdArr['v_title']){
            $filter['v_parent_guid']= $bookIdArr['v_title'];
        }
        $timeRange=array();
        $sort=array();
        $params=array(
            'pageSize'=>C('PAGENUM'),
            'page'=>$page,
            'filter'=>$filter,
            'sort'=>$sort,
            'recommend'=>false,
            'timeRange'=>$timeRange,
            'accurate'=>$accurate,
            'display_keys'=>array(
                'v_title'=>1,
                'v_resource_content'=>1,
                'd_publication_time'=>0,
                'v_author'=>1,
                'v_parent_guid'=>0,
                'v_global_guid'=>0,
                'v_parent_global'=>0,
                'n_browser'=>0,
                'v_download_src'=>0,
                'n_page'=>0,
                'n_id'=>0
            )
        );
        $data=XSUtil::search($config,$name,$params);
        $this->ajaxReturn($data,'json');
    }

    public function adcencedDataDeal($post){
         $num=getSearchwordNum($post);
         $searchStr=get_search_list($post,$num);
        return $searchStr;
    }

    public function timeDataDeal($timeStart,$timeEnd){
        if(empty($timeEnd) && empty($timeStart)){
            return null;
        }elseif(!empty($timeEnd) && !empty($timeStart)){
            $timeStart=$this->timeCheck($timeStart);
            $timeEnd=$this->timeCheck($timeEnd);
            return $timeRange=array(0=>'d_publication_time',1=>$timeStart,2=>$timeEnd);
        }elseif (!empty($timeEnd)){
            $timeEnd=$this->timeCheck($timeEnd);
            return $timeRange=array(0=>'d_publication_time',1=>null,2=>$timeEnd);
        }elseif(!empty($timeStart)){
            $timeStart=$this->timeCheck($timeStart);
            return $timeRange=array(0=>'d_publication_time',1=>$timeStart,2=>null);
        }

    }

    public function timeCheck($time){
        $timeStr=strtotime($time);
        if($timeStr!==false){
            return date('Y-m-d',$timeStr);
        }else{
            var_dump(2222);die;
        }
    }

    public function searchList(){
        $config=C('SEARCH.PARTYLITERATURE');
        $author=I('post.author');
        $v_publication_year=I('post.publicationYear');
        $parentGuid=I('post.parentGuid');
        $catName=I('post.catName','');
        $timeEnd=I('post.timeEnd','');
        $timeStart=I('post.timeStart');
        $isAdcenced=I("post.adcencedMark",0);
//        $adcencedSearWord='';
        $searchWordArr='';
        $catList=$this->categoryList(1,100);
        $this->assign('catList',$catList['data']);
        if($isAdcenced==1){
            $searchData=$this->dealAdcencedSearData($catList);
        }else{
            $searchData=$this->dealCommendSearData();
        }
        $data=$this->searchData($config,$searchData['name'],$searchData['page'],$author,$searchData['accurate'],$v_publication_year,$parentGuid,$searchData['timeRange'],$catName);
        $relatedWord=XSUtil::getRelatedQuery($config,$searchData['searchWord']);
        $this->assign('isAdcenced',$isAdcenced);
        $this->assign('searchWordArr',$searchData['searchArr']);
        if($data['count']['total_count']>0){
            $this->assign('data',$data['data']);
            $this->assign('count',$data['count']['total_count']);
            $this->assign('pageSize',C('PAGESIZE'));
            $this->assign('page',$searchData['page']);
            $this->assign('authorList',$data['count']['v_author_count']);
            $this->assign('yearList',$data['count']['v_publish_year_count']);
            $this->assign('parentList',$data['count']['v_parent_guid_count']);
            $this->assign('categoryList',$data['count']['v_cat_name_count']);
            $this->assign('relatedWord',$relatedWord);
            $this->assign('recommendWord',$data['count']['recommend_count']);
            $this->assign('searchWord',$searchData['searchWord']);
            $this->assign('searchAreaMark',$searchData['searchArea']);
            $this->assign('searchTextMark',1);
            $this->assign('timeStart',$timeStart);
            $this->assign('timeEnd',$timeEnd);
            $this->assign('adcencedSearWord',$searchData['adcencedSearWord']);
            $this->display();
        }else{
            var_dump(11111);die;
        }


    }

    public function ajaxSearchData()
    {
        $config=C('SEARCH.PARTYLITERATURE');
//        $name=I('post.searchWord');
        $isAdcenced=I("post.adcencedMark");
        $author=I('post.author');
        $v_publication_year=I('post.publicationYear');
        $parentGuid=I('post.parentGuid');
        $catName=I('post.catName');
        $timeRange=I('post.timeRange','');
        $sort=I('post.sort');
        if($isAdcenced==1){
            $searchData=$this->dealAdcencedSearData();
        }else{
            $searchData=$this->dealCommendSearData();
        }
        $data=$this->searchData($config,$searchData['name'],$searchData['page'],$author,$searchData['accurate'],$v_publication_year,$parentGuid,$searchData['timeRange'],$catName,$sort);
        $this->ajaxReturn($data,'json');
    }

    public function dealAdcencedSearData($catList){
        $post=I('post.');
        $data['adcencedSearWord']=get_search_arr($post);
        $searchArr=$this->adcencedDataDeal($post);
        $data['name']=$searchArr['searchStr'];
        $searchWordArr=$searchArr['searchArr'];
        $timeRange=$this->timeDataDeal($post['timeStart'],$post['timeEnd']);

        if($timeRange){
            $from=$timeRange[1]?$timeRange[1]:'1800-01-01';
            $to=$timeRange[2]?$timeRange[2]:'至今';
            $str=$from.'至'.$to;
            if(count($searchArr['searchArr'])>=1){
                $fieldRelation='并且';
            }else{
                $fieldRelation=null;
            }
            $timeWordArr=array('fieldRelation'=>$fieldRelation,'searchField'=>'时间','wordStr'=>$str);
            array_push($searchWordArr,$timeWordArr);
        }
        if(isset($catList)){
            $literatureCat=I('post.literatureCat',0);
            if($literatureCat){
                $str=$catList['data'][$literatureCat-1]['v_title'];
                $data['catName']=$str;
                if(count($searchArr['searchArr'])>=1){
                    $fieldRelation='并且';
                }else{
                    $fieldRelation=null;
                }
                $timeWordArr=array('fieldRelation'=>$fieldRelation,'searchField'=>'分类','wordStr'=>$str);
                array_push($searchWordArr,$timeWordArr);
            }
        }

        $data['page']=I('post.page',1);
        $data['accurate']=false;
        $data['searchArea']=I('post.searchAreaMark','searchAreaAll');
        $data['searchArr']=$searchWordArr;
        $data['searchWord']='';
        return $data;
    }

    public function dealCommendSearData(){
        $data['searchWord']=$searchWord=I('post.searchWord','');
        $data['page']=I('post.page',1);
        $data['searchArea']=I('post.searchAreaMark','searchAreaAll');
        $data['accurate']=false;
        switch ($data['searchArea']){
            case 'searchAreaTitle':
                $data['name']='v_title:'.$searchWord;
                break;
            case 'searchAreaContent':
                $data['name']='v_resource_content:'.$searchWord;
                break;
            case 'searchAreaAuthor':
                $data['name']='v_author:'.$searchWord;
                break;
            default:
                $data['name']=$searchWord;
                break;
        }
        $data['searchArr']='';
        $data['adcencedSearWord']='';
        return $data;
    }


    public function searchData($config,$name,$page,$author,$accurate=false,$v_publication_year,$parentGuid,$timeRange,$catName,$sort){

                $filter=array(
                    'v_author'=>'',
                    'v_publish_year'=>'',
                    'v_parent_guid'=>'',
                    'v_cat_name'=>'',
                );
                if(!empty($author)){
                    $filter['v_author'] = $author;
                }

                if(!empty($v_publication_year)){
                    $filter['v_publish_year'] = $v_publication_year;
                }

                if(!empty($parentGuid)){
                    $filter['v_parent_guid'] = $parentGuid;
                }

                if(!empty($catName)){
                    $filter['v_cat_name'] = $catName;
                }

                if(!empty($timeRange)){
                    $timeRange=$timeRange;
                }else{
                    $timeRange=array();
                }
                //var_dump($sort);die;
                if(!empty($sort)){
                    $sort=$sort;
                }else{
                    $sort=array();
                }
                $params=array(
                    'page'=>$page,
                    'filter'=>$filter,
                    'sort'=>$sort,
                    'recommend'=>true,
                    'timeRange'=>$timeRange,
                    'accurate'=>$accurate,
                    'display_keys'=>array(
                        'v_title'=>1,
                        'v_resource_content'=>1,
                        'd_publication_time'=>0,
                        'n_browser'=>0,
                        'v_download_src'=>0,
                        'v_author'=>1,
                        'v_parent_guid'=>0,
                        'v_global_guid'=>0,
                        'v_parent_global'=>0,
                        'n_page'=>0,
                        'n_id'=>0,
                        'bookId'=>0,
                    )
                );


         return $data=XSUtil::search($config,$name,$params);
    }

    public function literatureInfo(){
        $id=I('get.id','','intval');
        if(empty($id)){
            var_dump(2222);die;
        }
        $resourceModel=M('resource_info');
        $resourceInfo=$resourceModel->where('n_id ='.$id)->find();
        if($resourceInfo){
            $file_name=$resourceInfo['v_global_guid'].'.pdf';
            $file_dir=C('RESOURCE.PDF').$resourceInfo['v_parent_guid'].'/';
            $next_name=$resourceInfo['v_title'].'.pdf';
            $this->downloadPdf($file_name,$file_dir,$next_name);
        }else{
            var_dump(2222);die;
        }
    }

    /**
     * 下载相应PDF
     * @param $file_name 相应图片名称
     * @param $file_dir  图片所在路径
     * @param $next_name 图片新名称
     * @return bool|void
     */

    public function downloadPdf($file_name,$file_dir,$next_name)
    {
        //1.打开文件
        $file_path=$file_dir.$file_name;
        if(!file_exists($file_path)){
            echo "文件不存在!";
            return ;
        }else{
            $fp=fopen($file_path,"r");
            //2.处理文件
            //获取下载文件的大小
            $file_size=filesize($file_path);
            if($file_size>30000000){
                echo "<script language='javascript'>window.alert('过大')</script>";
                return ;
            }
            //返回的文件
            header("Content-type: application/octet-stream");
            //按照字节大小返回
            header("Accept-Ranges: bytes");
            //返回文件大小
            header("Accept-Length: $file_size");
            //这里客户端的弹出对话框，对应的文件名
            header("Content-Disposition: attachment; filename=".$next_name);

            //向客户端回送数据

            $buffer=1024;
            //为了下载的安全，我们最好做一个文件字节读取计数器
            $file_count=0;
            //这句话用于判断文件是否结束
            while(!feof($fp) && ($file_size-$file_count>0) ){
                $file_data=fread($fp,$buffer);
                //统计读了多少个字节
                $file_count+=$buffer;
                //把部分数据回送给浏览器;
                echo $file_data;
            }

            //关闭文件
            fclose($fp);
            return true;
        }
    }

    public function read(){
//        $src=I('get.src','');
//        $belong=I('get.belong','');
//        if(!$src){
//            var_dump(2222);die;
//        }
//        if(!$belong){
//            var_dump(2222);die;
//        }
//
//        $resourceModel=M('resource_info');
//        $resourceInfo=$resourceModel->where('v_global_guid ="'.$src.'"')->find();
//        $data=array();
//        $data['n_browser']=$resourceInfo['n_browser']+1;
//        $result=$resourceModel->where('v_global_guid ="'.$src.'"')->save($data);
//        if(false!==$result){
//            $file=C('RESOURCE.READPDF').$belong.'/'.$src.'.pdf';
////            var_dump($file);die;
////            if(!file_exists($file)) {
////                echo '文件不存在';
////                var_dump($file);
////                return;
////            }
//            header('Content-type: application/pdf');
//            header('filename='.$file);
//            readfile($file);
//            $this->assign('file',$file);
            $this->display();
//        }else{
//            var_dump(2222);die;
//        }
    }
}