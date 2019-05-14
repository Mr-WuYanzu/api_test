<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
//use \Illuminate\Support\Facades\Cache::put('test-redis','REDIS',10);
class IndexController extends Controller
{

    public function index(Request $request){
//        $iv='d89fb057f6d4f03g';
//        $key='zhb';
        $sign=$_GET['sign']??'';
        if(empty($sign)){
            echo "参数不完整";
            die;
        }

        $data=file_get_contents('php://input');
          print_r($this->testsign($data,$sign));

    }
//    凯撒加密解密
//    function decrypt($user_name,$n){
//        $pass='';
//        for($i=0;$i<strlen($user_name);$i++){
//            $p=ord($user_name[$i])-$n;
//            $pass .= chr($p);
//        }
//        return $pass;
//    }
//对称加密解密
    function decode($strEncryptCode,$key,$iv) {
        $decode=base64_decode($strEncryptCode);
        return openssl_decrypt($decode,'AES-256-CBC',$key,OPENSSL_RAW_DATA,$iv);
    }
//非对称加密解密
    public function decrypt($data){
        $data=base64_decode($data);
        $p=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($data,$decode_str,$p);
        return json_decode($decode_str,true);
    }
    //非对称加密签名
    public function testsign($data,$sign){
        $key=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        $res=openssl_verify($data,base64_decode($sign),$key);
        if($res==0){
            die('验证签名失败');
        }else if($res=='-1'){
            die('内部错误');
        }
    }
    //用户注册
    public function reg(Request $request){
        $data=file_get_contents('php://input');
        $data=$this->decrypt($data);
        if($data){
            $data['password']=encrypt($data['password']);
            $res=DB::table('user')->where('email',$data['email'])->first();
            if($res){
                $response=[
                    'errno'=>'41001',
                    'msg'=>'邮箱 已经注册',
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }else{
                $data=DB::table('user')->insert($data);
                if($data){
                    $response=[
                        'errno'=>'0',
                        'msg'=>'注册成功',
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);
                    die;
                }else{
                    $response=[
                        'errno'=>'40010',
                        'msg'=>'注册失败',
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);die;
                }
            }

        }else{
            $response=[
                'errno'=>'40001',
                'msg'=>'没有数据',
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
    }
    //new用户注册
    public function register(Request $request){
        $user_name=$request->user_name??'';
        $email=$request->email??'';
        $password=$request->password??'';
        if(empty($user_name)||empty($email)||empty($password)){
            $response=[
                'errno'=>'42001',
                'msg'=>'缺少参数'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $data=[
            'user_name'=>$user_name,
            'email'=>$email,
            'password'=>$password
        ];

            $data['password']=encrypt($data['password']);
            $res=DB::table('user')->where('email',$data['email'])->first();
            if($res){
                $response=[
                    'errno'=>'41001',
                    'msg'=>'邮箱 已经注册',
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }else{
                $data=DB::table('user')->insertGetId($data);
                if($data){
                    $response=[
                        'errno'=>'0',
                        'msg'=>'注册成功',
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);
                    die;
                }else{
                    $response=[
                        'errno'=>'40010',
                        'msg'=>'注册失败',
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);die;
                }
            }

    }
    //用户登录
    public function login(){
        $data=file_get_contents('php://input');
        $data=$this->decrypt($data);
        if($data){
            $res=DB::table('user')->where('user_name',$data['user_name'])->first();
            if($res){
                if(decrypt($res->password)!=$data['password']){
                    echo '密码错误';
                }else{
                    echo '登录成功';
                    $key='login:id:'.$res->id;
                    $token=substr((md5($res->id).time()),5,15);
                    Redis::set($key,$token);
                    Redis::expire($key,60*60*24*7);
                }
            }else{
                echo '用户不存在';die;
            }
        }else{
            echo '没有数据';
        }


    }
    //new登录
    public function logindo(Request $request){
        $email=$request->email??'';
        $password=$request->password??'';
        if(empty($email)||empty($password)){
            $response=[
                'errno'=>'42001',
                'msg'=>'缺少参数'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $data=[
            'email'=>$email,
            'password'=>$password
        ];
            $res=DB::table('user')->where('email',$data['email'])->first();
            if($res){
                if(decrypt($res->password)!=$data['password']){
                    $response=[
                        'errno'=>'42002',
                        'msg'=>'密码错误'
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);die;
                }else{
                    $response=[
                        'errno'=>'0',
                        'msg'=>'登录成功',
                        'uid'=>$res->id,
                        'token'=>substr(md5($res->id.time()),5,15)
                    ];
                    return json_encode($response,JSON_UNESCAPED_UNICODE);die;
                }
            }else{
                $response=[
                    'errno'=>'42001',
                    'msg'=>'账号不存在'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }


    }
    public function test(){
        header('Access-Control-Allow-Origin:http://client.1809a.com/login');
        $info='哈哈';
        echo "$('#text').val('".$info."')";
    }
}
