<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/2
 * Time: 10:40
 */

namespace Home\Controller;


use Think\Controller;

class LoginController extends Controller
{
    public $loginMarked;

    /**
     * +----------------------------------------------------------
     * 初始化
     * +----------------------------------------------------------
     */
    public function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
        $loginMarked = C("TOKEN");
        $this->loginMarked = md5($loginMarked['member_marked']);
    }

    /**
     * +----------------------------------------------------------
     * LoginAction中的index方法，主要进行登录验证
     * +----------------------------------------------------------
     */
    public function index()
    {
        //判断是否存在登录标识，有直接定位到前台首页
        if (isset($_COOKIE["$this->loginMarked"])) {
            $this->redirect("Index/index");
        }
        /**
         * +----------------------------------------------------------
         * IP地址是特殊IP，直接定位到前台首页
         * +----------------------------------------------------------
         */
        $isnouserip = isInIpRange();
        //特殊IP验证通过，跳转到首页
        if ($isnouserip) {
            //登陆成功：用户名为空，特殊IP验证通过
            $loginMarked = C("TOKEN");
            $loginMarked = md5($loginMarked['member_marked']);
            $shell = 'Super-ip User';
            //存储到session中
            $_SESSION["$this->loginMarked"] = $shell;
            $shell .= "_" . time();
            //存取cookie后不会立即生效，在下个页面才会正式生效
            setcookie($loginMarked, $shell, 0, "/");
            //把用户信息存储到session中一份，再前台主页中会用到
            $v_user_info = get_user_info();
            $v_user_nick = $v_user_info['v_company'];
            $_SESSION['valid_user_id'] = $v_user_info['n_user_id'];
            //记录日志：
            write_loginlog('', '', 1, 0, 1, 0, 0, $_SESSION['valid_user_id']);
            $_SESSION['homeinfo']['v_user_nick'] = $v_user_nick;
            $this->redirect('Index/index');
        }
        //展示网站标题信息；
        $this->assign("site", C('SITE_INFO.name'));
        //特殊IP未通过，跳转到登陆页
        $this->display();
    }

    /**
     * +----------------------------------------------------------
     * LoginAction中的check方法，主要进行登录验证
     * +----------------------------------------------------------
     */
    public function check()
    {
        if (!isset($_POST['info'])) {
            print_r("无效参数");
            exit;
        }
        //执行一次登录验证;
        $checkuser = $this->login();
        $userinfo = $_POST['info'];
        //用户信息大小写转换
        $userinfo['v_user_name'] = strtolower($userinfo['v_user_name']);
        //>>1.0用户名为空
        if ($userinfo['v_user_name'] == '') {
            //>>2.执行是否为特殊ip验证
            $isnouserip = isInIpRange();
            if ($isnouserip) {
                //登陆成功：用户名为空，特殊IP验证通过
                $loginMarked = C("TOKEN");
                $loginMarked = md5($loginMarked['member_marked']);
                $shell = 'Super-ip User';
                //存储到session中
                $_SESSION["$this->loginMarked"] = $shell;
                $shell .= "_" . time();
                //连接time（）后，再存储到cookie中
                setcookie($loginMarked, "$shell", 0, "/");
                //把用户信息存储到session中一份，再前台主页中会用到
                $v_user_info = get_user_info();
                $v_user_nick = $v_user_info['v_company'];
                $_SESSION['valid_user_id'] = $v_user_info['n_user_id'];
                //记录日志：
                write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 1, 0, 1, 0, 0, $_SESSION['valid_user_id']);
                $_SESSION['homeinfo']['v_user_nick'] = $v_user_nick;
                echo json_encode(array("status" => 1, "info" => '', "url" => U('Index/index')));
            } else {
                //失败：用户名为空，非特殊IP
                write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 0, 0, 0, 1, 0, -1);
                session_destroy();
                echo json_encode(array("status" => 0, 'msg' => '非特殊IP,输入用户名密码', 'url' => ''));
                exit;
            }
        } else {
            //登录验证后，根据返回值判断是否登录成功：1.登录成功：判断是否是超级账户跟非超级账户 2.登录失败
            if (is_array($checkuser)) {
                //说明登录验证成功：
                //判断是否为超级测试账户,如果是超级测试账户，不用进行IP验证
                if ($_SESSION['valid_user_type'] != 0) {
                    //登录成功，用户名非空，超级测试账户，用户验证通过
                    //记录日志：
                    write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 1, 1, 0, 0, 0, $_SESSION['valid_user_id']);
                    $loginMarked = C("TOKEN");
                    $loginMarked = md5($loginMarked['member_marked']);
                    $shell = $checkuser['n_user_id'] . md5($checkuser['v_user_pwd'] . C('AUTH_CODE'));
                    //如果记录符合，存储到session中
                    $_SESSION[$loginMarked] = "$shell";
                    $shell .= "_" . time();
                    //连接time（）后，再存储到cookie中
                    setcookie($loginMarked, "$shell", 0, "/");
                    //把用户信息存储到session中一份，再前台主页中会用到
                    $_SESSION['homeinfo'] = $checkuser;
                    echo json_encode(array("status" => 1, "info" => '', "url" => U('Index/index')));
                } else {
                    //说明是非特权用户，需要额外验证IP
                    //START
                    $isipallow = isInIpRange();
                    if ($isipallow) {
                        //用户名非空，IP验证通过
                        //登录成功：用户名非空，IP验证通过，用户验证通过
                        $loginMarked = C("TOKEN");
                        $loginMarked = md5($loginMarked['member_marked']);
                        $shell = $checkuser['n_user_id'] . md5($checkuser['v_user_pwd'] . C('AUTH_CODE'));
                        //如果记录符合，存储到session中
                        $_SESSION[$loginMarked] = "$shell";
                        $shell .= "_" . time();
                        //连接time（）后，再存储到cookie中
                        setcookie($loginMarked, "$shell", 0, "/");
                        //把用户信息存储到session中一份，再前台主页中会用到
                        $v_user_info = get_user_info();
                        $v_user_nick = $v_user_info['v_company'];
                        write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 1, 0, 0, 0, 0, $_SESSION['valid_user_id']);
                        $_SESSION['homeinfo']['v_user_nick'] = $v_user_nick;
                        echo json_encode(array("status" => 1, "info" => '', "url" => U('Index/index')));
                    } else {
                        //失败：用户名非空，IP未授权或者账户被禁用
                        write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 0, 0, 0, 1, 0, -1);
                        session_destroy();
                        echo json_encode(array("status" => 0, "msg" => 'IP未授权或账户被禁用', 'url' => ''));
                        exit;
                    }
                    //END 非特殊IP判断
                } //END 非特权用户判断：
            } else {
                //登录验证失败：
                if ($checkuser == 1 || $checkuser == 2) {
                    //登录失败，用户名非空，超级测试账户，用户验证未通过
                    write_loginlog($userinfo['v_user_name'], $userinfo['v_user_pwd'], 0, 0, 0, 0, 1, -1);
                    session_destroy();
                    echo json_encode(array("status" => 0, "msg" => '用户名或密码错误', 'url' => ''));
                    exit;
                }
            } //END 用户验证失败的分支判断
        } //END用户名不为空的分支判断
    } //END checkfunction

    /**
     * +----------------------------------------------------------
     * LoginAction中的login方法，主要进行用户身份登录验证
     * +----------------------------------------------------------
     */
    public function login()
    {
        if (!isset($_POST['info'])) {
            print_r("无效参数");
            exit;
        }
        date_default_timezone_set("PRC");
        $riqi = date("Y-m-d");
        $member_model = M('user');
        $data = $_POST['info'];
        //用户信息大小写转换
        $data['v_user_name'] = strtolower($data['v_user_name']);
        //查询用户名是否能查询的到查询数量即可
        $emailarr = $member_model->where("v_user_name = '{$data['v_user_name']}'")->count();
        //如果用户名存在，那么匹配该用户名密码
        if ($emailarr) {
            //检查密码是否正确
            $data['v_user_pwd'] = encrypt($data['v_user_pwd']);
            $checkpwdarr = $member_model->where("`v_user_name` = '{$data['v_user_name']}' AND v_user_pwd = '{$data['v_user_pwd']}' AND n_status=1 AND d_date>='{$riqi}'")->find();
            //如果存在记录,用户名密码都匹配，登录验证通过
            if ($checkpwdarr) {
                $_SESSION['valid_user'] = $data['v_user_name'];
                $_SESSION['valid_user_id'] = $checkpwdarr['n_user_id'];
                $_SESSION['valid_user_type'] = $checkpwdarr['n_user_type'];
                return $checkpwdarr;
            } else {
                //密码不匹配
                return 1;
            }
        } else {
            //用户名不匹配
            return 2;
        }
    }


}