<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class OrderController extends Controller
{

    //商品结算生成订单
    public function order(Request $request){
        $goods_id=$request->input('goods_id');
        $uid=intval($request->input('uid'));
        if(!$goods_id){
            $response=[
                'errno'=>'50032',
                'msg'=>'商品id不能为空'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }else if(!$uid){
            $response=[
                'errno'=>'50033',
                'msg'=>'请登录'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $goods_id=explode(',',$goods_id);
        foreach($goods_id as $k=>$v){
            //验证商品和用户是否存在
            $goods_info=DB::table('goods')->where('goods_id',$v)->first();
            if(!$goods_info){
                $response=[
                    'errno'=>'50034',
                    'msg'=>'商品不存在'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }
        }
        //验证用户是否存在
        $userInfo=DB::table('user')->where('id',$uid)->first();
        if(!$userInfo){
            $response=[
                'errno'=>'50035',
                'msg'=>'用户不存在'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        //查询商品信息并求出订单总金额
        $order_amount=0;
        $goods_info=DB::table('goods')->WhereIn('goods_id',$goods_id)->get();
        foreach($goods_info as $k=>$v){
            $cart_info=DB::table('cart')
                ->join('goods','goods.goods_id','=','cart.goods_id')
                ->where(['uid'=>$uid,'cart.goods_id'=>$v->goods_id,'cart.status'=>0])
                ->first();
            if(!$cart_info){
                $response=[
                    'errno'=>'50037',
                    'msg'=>'购物车订单不存在'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }else{
                $order_amount+=$cart_info->buy_num*$cart_info->goods_price;
            }
        }
        $order_no='jd'.substr((date('ymd').time().str::random(16)),0,21);
        $order_data=[
            'uid'=>$uid,
            'order_no'=>$order_no,
            'order_amount'=>$order_amount,
            'add_time'=>time()
        ];
        $oid=DB::table('order')->insertGetId($order_data);
        if($oid<=0){
            $response=[
                'errno'=>'50038',
                'msg'=>'生成订失败'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
//        写入订单详情
        $cart_info=DB::table('cart')
            ->join('goods','goods.goods_id','=','cart.goods_id')
            ->where(['uid'=>$uid,'cart.status'=>0])
            ->whereIn('cart.goods_id',$goods_id)
            ->get();
        foreach($cart_info as $k=>$v){
            $detail_info=[
                'oid'=>$oid,
                'uid'=>$uid,
                'order_no'=>$order_no,
                'goods_id'=>$v->goods_id,
                'goods_name'=>$v->goods_name,
                'goods_price'=>$v->goods_price,
                'buy_num'=>$v->buy_num
            ];
            $res=DB::table('order_detail')->insert($detail_info);
            $r=DB::table('cart')->where('id',$v->id)->update(['status'=>1]);
            if($res&&$r){
                $response=[
                    'errno'=>'0',
                    'oid'=>$oid,
                    'msg'=>'生成订单成功'
                ];
            }else{
                $response=[
                    'errno'=>'50038',
                    'msg'=>'生成订单失败'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }

        }
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }
    //订单列表
    public function orderlist(){
        $uid=intval($_GET['uid']);
        if(!$uid){
            $response = [
                'errno' => '50033',
                'msg' => '请登录'
            ];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
            die;
        }
        $data=DB::table('order')->where(['uid'=>$uid,'pay_status'=>0])->get();
        if($data) {
            $response = [
                'errno' => '0',
                'data' => $data
            ];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
            die;
        }else{
            $response = [
                'errno' => '50039',
                'msg' => '没有订单'
            ];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
            die;
        }
    }
}
