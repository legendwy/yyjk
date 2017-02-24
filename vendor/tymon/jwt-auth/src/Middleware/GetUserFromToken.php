<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tymon\JWTAuth\Middleware;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Controllers\Api\BaseController;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $base = new BaseController();
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $base->returnMsg(false, 400004, '用户未登录', [], 401);
            } else {
                $user_info = JWTAuth::toUser();
                if (!\DB::table('users')->where('id', $user_info->id)->value('status')) {
                    return $base->returnMsg(false, 400005, '用户已被禁用', [], 401);
                }
            }

        } catch (TokenExpiredException $e) {
            return $base->returnMsg(false, 400001, 'token 过期', [], 401);

        } catch (TokenInvalidException $e) {
            return $base->returnMsg(false, 400003, 'token 无效', [], 401);

        } catch (JWTException $e) {
            return $base->returnMsg(false, 400002, 'token 缺失', [], 401);

        }

        return $next($request);
    }
}
