<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OAuthClientCredentialsService;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(OAuthClientCredentialsService::class, function ($app) {
            // Giả sử bạn muốn truyền các tham số vào constructor của service
            // Bạn có thể lấy các giá trị này từ config hoặc bất cứ đâu
            $tokenUrl = config('services.oauth.token_url');
            $clientId = config('services.oauth.client_id');
            $clientSecret = config('services.oauth.client_secret');
            $scopes = config('services.oauth.scopes', '');

            return new OAuthClientCredentialsService($tokenUrl, $clientId, $clientSecret, $scopes);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
