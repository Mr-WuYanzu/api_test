<?php

namespace App\Http\Middleware;

use Closure;

class CrossHttp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->getMethod() == "OPTIONS") {
            $allowOrigin = [
                'http://192.168.136.131',
                'http://localhost',
            ];
            $Origin = $request->header("Origin");
            if(in_array($Origin, $allowOrigin)){
                return response()->json('ok', 200, [
                    # 下面参数视request中header而定
                    'Access-Control-Allow-Origin' => $Origin,
                    'Access-Control-Allow-Headers' => 'x-requested-with',
                    'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS']);
            } else {
                return response()->json('fail', 405);
            }
        }

        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
