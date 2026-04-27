<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\API\Dashboard\StripeCheckoutController;
use App\Http\Controllers\Frontend\TenantRegistrationController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\TenantDashboardController;
use App\Http\Controllers\SuperAdmin\TenantProfileController;
use App\Http\Controllers\SuperAdmin\TenantSubscriptionController;
use App\Http\Controllers\LaunchGateController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use Stancl\Tenancy\Resolvers\DomainTenantResolver;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// routes/web.php

// Route::middleware([
//     // Apply tenancy only if tenant exists
//     \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
// ])->group(function () {


//     if (tenancy()->initialized) {
//         Route::get('/{any}', function () {
//             return view('app'); // Your Vue SPA entry
//         })->where('any', '.*');
//     }
// });
Route::middleware(['web'])->group(function () {
    // Launch gate: password required until LAUNCH_GATE_ENABLED=false (easy to remove at launch)
    Route::get('/launch-gate', [LaunchGateController::class, 'show'])->name('launch-gate');
    Route::post('/launch-gate/unlock', [LaunchGateController::class, 'unlock'])->name('launch-gate.unlock');

    Route::get('/stripe/success', [StripeCheckoutController::class, 'handleSuccess'])->name('stripe.success');
    Route::get('/stripe/cancel', [StripeCheckoutController::class, 'handleCancel'])->name('stripe.cancel');

    Route::get('/auth/google/redirect', function () {
        $clientId = config('services.google.client_id');
        if (! $clientId) {
            return redirect('/login');
        }
        $redirectUri = config('services.google.redirect');
        if (! $redirectUri || ! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $redirectUri = url('/auth/google/callback');
        }
        $state = bin2hex(random_bytes(16));
        session(['google_oauth_state' => $state]);
        $qs = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$qs);
    })->name('auth.google.redirect');

    Route::get('/auth/google/callback', function (Request $request) {
        $expected = session()->pull('google_oauth_state');
        $state = $request->query('state');
        if (! $expected || ! $state || ! hash_equals((string) $expected, (string) $state)) {
            return redirect('/login');
        }
        $code = $request->query('code');
        if (! $code) {
            return redirect('/login');
        }
        $redirectUri = config('services.google.redirect');
        if (! $redirectUri || ! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $redirectUri = url('/auth/google/callback');
        }
        $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);
        if (! $tokenRes->successful()) {
            return redirect('/login');
        }
        $access = $tokenRes->json('access_token');
        if (! $access) {
            return redirect('/login');
        }
        $profile = Http::withToken($access)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if (! $profile->successful()) {
            return redirect('/login');
        }
        $email = $profile->json('email');
        if (! $email || ! is_string($email)) {
            return redirect('/login');
        }

        return redirect('/login?email='.rawurlencode($email));
    })->name('auth.google.callback');

    Route::get('/login', fn() => view('app'))->name('login');
    Route::get('/{any}', fn() => view('app'))->where('any', '.*');
});

