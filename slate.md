Want to make 3 languages website. English, Spanish, France.
When 


https://laraveldaily.com/course/multi-language-laravel?mtm_campaign=search-results-course


```php file="langt/lang/en/messages.php"
return [
  'welcome' => 'En Welcome to our website',
  'newMessageIndicator' => '{0} EN You have no new messages|{1} EN You have 1 new message|[2,*] EN You have :count new messages',

];
```


```php file="config/app.php"
'available_locales' => [
    'en',
    'es',
    'fr',
],
```

## Date
```php file=app/Providers/AppServiceProvider.php

use Carbon\Carbon;
 
class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ...
 
        Carbon::setLocale(app()->getLocale());
 
        //...
    }
}
```


## Middleware

```php
php artisan make:middleware SetLocale

```


```php file="app/Http/Middleware/SetLocale.php"

use URL;
use Carbon\Carbon;
 
// ...
 
public function handle(Request $request, Closure $next): Response
{
    app()->setLocale($request->segment(1)); // <-- Set the application locale
    Carbon::setLocale($request->segment(1)); // <-- Set the Carbon locale
 
    URL::defaults(['locale' => $request->segment(1)]); // <-- Set the URL defaults
    // (for named routes we won't have to specify the locale each time!)
 
    return $next($request);
}
```

```php file=app/Http/Kernel.php
protected $middlewareAliases = [
  'setlocale' => \App\Http\Middleware\SetLocale::class,
];
```

```php file="routes/web.php"
Route::get('/', function () {
    return redirect(app()->getLocale()); // <-- Handles redirect with no locale to the current locale
});
 
Route::prefix('{locale}') // <-- Add the locale segment to the URL
    ->where(['locale' => '[a-zA-Z]{2}']) // <-- Add a regex to validate the locale
    ->middleware('setlocale') // <-- Add the middleware
    ->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
 
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
 
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 
        // ...
    });
 
    require __DIR__ . '/auth.php';
});
```



```php file=resources/views/layouts/navigation.blade.php
@foreach(config('app.available_locales') as $locale)
    <x-nav-link
            :href="route(\Illuminate\Support\Facades\Route::currentRouteName(), array_merge(Route::current()->parameters(),['locale' => $locale]))"
            :active="app()->getLocale() == $locale">
        {{ strtoupper($locale) }}
    </x-nav-link>
@endforeach
<x-dropdown align="right" width="48">

```



```php file=app/Http/Controllers/Auth/AuthenticatedSessionController.php

public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
 
    $request->session()->regenerate();
 
    return redirect()->intended(app()->getLocale() . RouteServiceProvider::HOME);
}
```


```php file= app/Http/Controllers/Auth/RegisteredUserController.php
// ...
 
public function store(Request $request): RedirectResponse
{
    // ...
 
    - return redirect(RouteServiceProvider::HOME); 
    + return redirect(app()->getLocale() . RouteServiceProvider::HOME); 
}

```



