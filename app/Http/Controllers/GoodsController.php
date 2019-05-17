<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class GoodsController extends Controller
{

    //商品列表
    public function goods(){
        $data=DB::table('goods')->get();
        return json_encode($data);
    }
    //商品详情
    public function detail(Request $request){
        $goods_id=$request->input('goods_id');
        if($goods_id) {
            $data = DB::table('goods')->where('goods_id', $goods_id)->first();
            $response=[
                'errno'=>'0',
                'data'=>$data
            ];
            return json_encode($response);
        }else{
            $response=[
                'errno'=>'60001',
                'msg'=> 'null data'
            ];
            return json_encode($response);
        }
    }
}
