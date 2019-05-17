<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class PayController extends Controller
{


    //去支付
    public function ali_pay(Request $request){
        $oid=$request->input('oid');
        $order_info=DB::table('order')->where(['oid'=>$oid,'pay_status'=>0,'is_del'=>0])->first();
        $appid=2016092500594759;
        $url='https://openapi.alipay.com/gateway.do';
        $notify_url = 'http://api.zhbcto.com/notify_url';
        $return_url = 'http://api.zhbcto.com/return_url';
    }
    //计算签名
    public function sign(){

    }
    //异步回调
    public function notify_url(){

    }
    //同步回调
    public function return_url(){

    }
}
