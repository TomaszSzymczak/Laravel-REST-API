<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class CacheApiGetRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        if (!$this->checkIfCacheApplies($request)) {
            return $next($request);
        }
        
        $key = $this->makeCacheKey($request);
        
        // we return response if its already in cache
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        
        // not using Illuminate\Http\Response because it could be also JsonResponse
        // or other derivatives
        
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);
        
        if (!$this->shouldResponseBeCached($response)) {
            return $response;
        }
        
        $this->cacheResponse($response, $key);
        
        // for now we're returning original response
        return $response;
    }
    
    /**
     * @param \App\Http\Middleware\Request $request
     * @return boolean
     */
    public function checkIfCacheApplies(Request $request)
    {
        if (
            'GET' !== $request->getMethod()
            ||
            (
                'local' === getenv('APP_ENV')
                &&
                $request->has('XDEBUG_SESSION_START')
            )
        ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param \App\Http\Middleware\Request $request
     * @return string
     */
    protected function makeCacheKey(Request $request)
    {
        $qsa = $request->all();
        ksort($qsa);
        
        return md5($request->path() . implode(';', $qsa));
    }
    
    protected function createResponseToSaveInCache(Response $response)
    {
        $responseForCache = clone $response;
        
        /*
         * otherwise won't be saved in cache due to problem
         * with serializing clousures which are elements of exception field
         */
        if ($responseForCache->exception) {
            $responseForCache->exception = null;
        }
        
        $responseForCache->headers->add([
            'X-API-Cache' => now()->format('Y-m-d H:i:s')
        ]);
        
        return $responseForCache;
    }
    
    protected function shouldResponseBeCached(Response $response)
    {
        if ($response->isClientError()) {
            return false;
        }
        
        // TODO: maybe add API version to Response or Request
        // or create different middlewares to further versions of API
        if (
            $response->isServerError()
            &&
            false === Config::get('cache.api.v1.cacheServerErrors'))
        {
            return false;
        }
        
        return true;
    }
    
    protected function cacheResponse(Response $response, string $key)
    {
        // adding cache headers and preparing obj. for serialization
        $responseForCache = $this->createResponseToSaveInCache($response);
        Cache::put($key, $responseForCache);
    }
}
