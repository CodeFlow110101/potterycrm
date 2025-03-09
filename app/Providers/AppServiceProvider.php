<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\CouponPolicy;
use App\Policies\DatePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\UserPolicy;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-any-product', [ProductPolicy::class, 'viewAny']);
        Gate::define('update-product', [ProductPolicy::class, 'update']);
        Gate::define('create-product', [ProductPolicy::class, 'create']);

        Gate::define('view-any-booking', [BookingPolicy::class, 'viewAny']);
        Gate::define('view-customer-detail-columns-booking', [BookingPolicy::class, 'viewCustomerDetailColumns']);
        Gate::define('update-booking', [BookingPolicy::class, 'update']);

        Gate::define('update-date', [DatePolicy::class, 'update']);
        Gate::define('create-date', [DatePolicy::class, 'create']);

        Gate::define('view-customer-detail-columns-purchase', [PurchasePolicy::class, 'viewCustomerDetailColumns']);
        Gate::define('view-any-purchase', [PurchasePolicy::class, 'viewAny']);

        Gate::define('view-customer-detail-columns-order', [OrderPolicy::class, 'viewCustomerDetailColumns']);
        Gate::define('view-any-order', [OrderPolicy::class, 'viewAny']);

        Gate::define('view-any-coupon', [CouponPolicy::class, 'viewAny']);
        Gate::define('update-coupon', [CouponPolicy::class, 'update']);
        Gate::define('create-coupon', [CouponPolicy::class, 'create']);
        Gate::define('view-customer-detail-columns-coupon', [CouponPolicy::class, 'viewCustomerDetailColumns']);

        Gate::define('register-user', [UserPolicy::class, 'register']);
        Gate::define('update-user', [UserPolicy::class, 'update']);
        Gate::define('hardware-checkout-user', [UserPolicy::class, 'terminalCheckout']);
        Gate::define('online-checkout-user', [UserPolicy::class, 'terminalCheckout']);

        Gate::define('valid-phone-number', function (?User $user, $number) {
            return Str::startsWith(trim($number), env('TWILIO_PHONE_COUNTRY_CODE')) && strlen(trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', trim($number)))) === strlen(trim(env('PHONE_NUMBER_VALIDATION_PATTERN')));
        });

        Gate::define('android', function (?User $user) {
            $userAgent = Request::header('User-Agent');
            return stripos($userAgent, 'android') !== false;
        });

        Gate::define('ios', function (?User $user) {
            $userAgent = Request::header('User-Agent');
            return stripos($userAgent, 'iphone') !== false || stripos($userAgent, 'ipad') !== false || stripos($userAgent, 'ipod') !== false;
        });

        Gate::define('mobile-device', function (?User $user) {
            $userAgent = Request::header('User-Agent');
            return stripos($userAgent, 'android') !== false ||
                stripos($userAgent, 'iphone') !== false ||
                stripos($userAgent, 'ipad') !== false ||
                stripos($userAgent, 'ipod') !== false;
        });

        Gate::define('pc', function (?User $user) {
            $userAgent = Request::header('User-Agent');
            return stripos($userAgent, 'windows') !== false ||
                stripos($userAgent, 'macintosh') !== false ||
                stripos($userAgent, 'linux') !== false;
        });
    }
}
