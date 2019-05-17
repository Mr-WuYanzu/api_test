<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class CartController extends Controller
{

    //加入购物车
    public function cart(Request $request){
        $goods_id=intval($request->input('goods_id'));
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
        $goods_info=DB::table('goods')->where('goods_id',$goods_id)->first();
        $userInfo=DB::table('user')->where('id',$uid)->first();
        if(!$goods_info){
            $response=[
                'errno'=>'50034',
                'msg'=>'商品不存在'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }else if(!$userInfo){
            $response=[
                'errno'=>'50035',
                'msg'=>'用户不存在'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $where=[
            'goods_id'=>$goods_id,
            'uid'=>$uid,
            'status'=>0
        ];
        $res=DB::table('cart')->where($where)->first();
        if($res){
            $r=DB::table('cart')->where('id',$res->id)->update(['buy_num'=>$res->buy_num+1]);
            if($r){
                $response=[
                    'errno'=>'0',
                    'msg'=>'添加成功'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }else{
                $response=[
                    'errno'=>'50031',
                    'msg'=>'添加失败'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $data=[
                'uid'=>$uid,
                'goods_id'=>$goods_id,
                'buy_num'=>1,
                'add_time'=>time(),
            ];
            $r=DB::table('cart')->insert($data);
            if($r){
                $response=[
                    'errno'=>'0',
                    'msg'=>'添加成功'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }else{
                $response=[
                    'errno'=>'50031',
                    'msg'=>'添加失败'
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //购物车列表
    public function cartlist(Request $request){
        $uid=intval($request->input('uid'));
        if(!$uid){
            $response=[
                'errno'=>'50033',
                'msg'=>'请登录'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $cart_info=DB::table('cart')
                    ->join('goods','goods.goods_id','=','cart.goods_id')
                    ->where(['uid'=>$uid,'cart.status'=>0])
                    ->get();
        $response=[
            'errno'=>'0',
            'data'=>$cart_info
        ];
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }
}
