<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // <-- THÊM IMPORT NÀY

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // === THÊM ĐOẠN CODE NÀY VÀO ===
        $middleware->redirectGuestsTo(function (Request $request) {
            // Nếu request (yêu cầu) KHÔNG mong muốn JSON (ví dụ: là trình duyệt web)
            if (! $request->expectsJson()) {
                // Chuyển hướng đến trang login (cho web)
                return route('login');
            }
            
            // Nếu là API (như Flutter), nó sẽ tự động trả về lỗi 401 JSON
            // (Không cần trả về gì cả)
        });
        // === KẾT THÚC THÊM ===

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
