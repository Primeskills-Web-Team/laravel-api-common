<?php

namespace Primeskills\ApiCommon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Primeskills\ApiCommon\Exceptions\PrimeskillsException;
use Primeskills\ApiCommon\Surrounding\PrimeskillsHttpRequestService;
use Primeskills\ApiCommon\Surrounding\RedisService;
use Symfony\Component\HttpFoundation\Response;

class PrimeskillsMiddlewareToken
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->header('Authorization')) throw new PrimeskillsException(401, "Invalid token");

        $tokenBearer = $request->header('Authorization');
        if (!str_contains($tokenBearer, 'Bearer')) throw new PrimeskillsException(401, "Invalid token, Token must be Bearer type");

        $tokenKeyExplode = explode(" ", $tokenBearer);
        $cacheAccess = RedisService::get($tokenKeyExplode[1]);
        if (!$cacheAccess) {
            $urlAuth = sprintf("%s/auth/token-info",env("URL_AUTHORIZATION_SERVICE", ""));
            $response = (new PrimeskillsHttpRequestService())
                ->setUrl($urlAuth)
                ->setBody([])
                ->setHeaders(['Authorization' => $tokenBearer])->post();
            if ($response->getStatusCode() >= 300) throw new PrimeskillsException(401, "Invalid token");

            $bodyResponse = json_decode($response->getBody());

            $lastLogin = strtotime($bodyResponse->data->last_login_at);
            $currentDate = strtotime(date('Y-m-d H:i:s'));

            $diff = ($currentDate - $lastLogin);
            if ($diff < 0) throw new PrimeskillsException(401, "Invalid token");

            RedisService::setEx($tokenKeyExplode[1], json_encode($bodyResponse->data), $diff - 60);
            $request->attributes->add(['user' => $bodyResponse->data]);
        } else {
            $request->attributes->add(['user' => json_decode($cacheAccess)]);
        }

        return $next($request);
    }
}
