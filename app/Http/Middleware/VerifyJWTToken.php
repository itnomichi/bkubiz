<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerifyJWTToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->cookie('Authorization');
            if ($token != null) {
                $request->headers->set('Authorization', "Bearer $token");
            }
            $this->checkForToken($request);
            $this->auth->parseToken()->authenticate();
        } catch (JWTException $e) {
            if ($e instanceof TokenExpiredException) {
                $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
                $key = 'block_refresh_token_for_user_' . $payload['sub'];
                $cachedBefore = (int)Cache::has($key);
                if ($cachedBefore) { // If a token alredy was refreshed and sent to the client in the last JWT_BLACKLIST_GRACE_PERIOD seconds.
                    \Auth::onceUsingId($payload['sub']); // Log the user using id.
                    return $next($request); // Token expired. Response without any token because in grace period.
                }
                try {
                    $token = $this->auth->refresh(); // Get new token.
                    $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                    $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                    Cache::put($key, $token, $expiresAt);
                } catch (JWTException $e) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Token has been blacklisted']);
                    } else {
                        if ($request->is('login')) {
                            return $next($request);
                        } else {
                            return redirect('login')->cookie('Authorization', '');
                        }
                    }
                }
            } else if ($e instanceof TokenInvalidException) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Token is invalid']);
                } else {
                    if ($request->is('login')) {
                        return $next($request);
                    } else {
                        return redirect('login')->cookie('Authorization', '');
                    }
                }
            } else {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Token is not provided']);
                } else {
                    if ($request->is('login')) {
                        return $next($request);
                    } else {
                        return redirect('login')->cookie('Authorization', '');
                    }
                }
            }
        } catch (UnauthorizedHttpException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            } else {
                if ($request->is('login')) {
                    return $next($request);
                } else {
                    return redirect('login')->cookie('Authorization', '');
                }
            }
        }
        if (!$request->ajax() && $request->is('login')) {
            return redirect('/');
        }
        $response = $next($request);
        return $response->cookie('Authorization', $token); // Response with new token on cookie Authorization.
    }
}
