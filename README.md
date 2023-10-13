<p align="center"><img src="https://www.primeskills.id/wp-content/uploads/2021/12/logo-primeskills-01.png" width="200"></p>

Laravel Package For API Common
### Feature Ready
- [x] Middleware integrate with core Petra Service
- [x] General exception handle
- [x] Pagination for collection
- [x] General response
- [x] Traits UUID and Primeskills print log on console
- [x] Surrounding service Primeskills Request use Guzzle

### How to Use
Add this line inside repositories in `composer.json` file and run command `composer update`
```json
{
    "require": {
        "primeskills/web": "^1.3.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/MuhammadSuryono/primeskills-module"
        }
    ]
}

```
---
### 1. Middleware integrate with core Petra Service
Add `'token.auth' => EnsureTokenValid::class` in `App\Http\Kernel` inside `$routeMiddleware`.
This middleware use Redis for save last token, you must setup in composer json for redis with `composer install "predis/predis"` and then you can set for `REDIS_CLIENT` in config/database 
`'client' => env('REDIS_CLIENT', 'predis')`

### 2. General exception handle
Set exception general response in exceptions Handler Class.
Add this line in exceptions handler class
```php
namespace App\Exceptions;

use Primeskills\Web\Response\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
    }

    public function render($request, Throwable $e)
    {
        return Response::builder()->instanceException($e)->buildJson();
    }
}
```
