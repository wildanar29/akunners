<?php  
  
   namespace App\Http\Middleware;  
  
   use Closure;  
   use Tymon\JWTAuth\Facades\JWTAuth;  
   use Tymon\JWTAuth\Exceptions\JWTException;  
  
   class Authenticate  
   {  
       public function handle($request, Closure $next)  
       {  
           try {  
               if (! $user = JWTAuth::parseToken()->authenticate()) {  
                   return response()->json(['message' => 'User not found'], 404);  
               }  
           } catch (JWTException $e) {  
               return response()->json(['message' => 'Token is invalid'], 401);  
           }  
  
           return $next($request);  
       }  
   }  
