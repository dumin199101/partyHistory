<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/2
 * Time: 10:27
 */

namespace Common\Controller;


use Think\Controller;

class BaseController extends Controller
{
    public $loginMarked;

    public function _initialize(){
        header("Content-Type:text/html; charset=utf-8");
//        header('Content-Type:application/json; charset=utf-8');
        //判断用户身份是否还在有效期内

        $userlogin = C('SITE_INFO.USERS_LOGIN_ON');
        if($userlogin)
        {
            $this->loginMarked = md5(C('TOKEN.member_marked'));
            $this->checkLogin();
        }
        $uid=get_uid();
        $url=get_current_url();
        $query_str=array();
        if(IS_GET){
            array_push($query_str,I('get.'));
        }
        if(IS_POST){
             array_push($query_str,I('post.'));
        }
        if(!empty($query_str)){
            write_weblog($url,$uid,$query_str);
        }else{
            write_weblog($url,$uid);
        }

        $this->assign(
            array(
                'homeinfo'=>$_SESSION['homeinfo'],
            )
        );
    }


    public function checkLogin() {
        if (isset($_COOKIE[$this->loginMarked])){
            $cookie = explode("_", $_COOKIE[$this->loginMarked]);
            $timeout = C("TOKEN");
            if (time() > (end($cookie) + $timeout['member_timeout'])) {
                setcookie("$this->loginMarked", NULL, -3600, "/");
                unset($_SESSION[$this->loginMarked], $_COOKIE[$this->loginMarked]);
                $this->redirect("Login/index");
            } else {
                if ($cookie[0] == $_SESSION[$this->loginMarked]) {
                    setcookie("$this->loginMarked", $cookie[0] . "_" . time(), 0, "/");
                } else {
                    setcookie("$this->loginMarked", NULL, -3600, "/");
                    unset($_SESSION[$this->loginMarked], $_COOKIE[$this->loginMarked]);
                    $this->redirect("Login/index");
                }
            }
        } else {
            $this->redirect("Login/index");
        }
        return TRUE;
    }

}