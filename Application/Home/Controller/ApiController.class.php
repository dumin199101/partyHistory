<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/3
 * Time: 10:50
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller
{
    public function SimplifiedToTraditional(){
        $word=I('post.word','');
        if(!empty($word))
            echo SimplifiedToTraditional($word);
    }
}