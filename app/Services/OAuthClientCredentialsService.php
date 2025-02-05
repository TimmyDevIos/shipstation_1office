<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OAuthClientCredentialsService
{

    public function getAccessToken_BasicAuth($clientId, $clientSecret,$apiname)
    {
        // Tạo cache key dựa trên client_id để duy trì tính duy nhất cho mỗi client
        $cacheKey = 'oauth_access_token_'.$apiname;

        // Kiểm tra xem token có trong cache không
        if (Cache::has($cacheKey)) {
            $accessToken = Cache::get($cacheKey);
            Log::info('Token được lấy từ Cache.');
            Log::info('getAccessToken_BasicAuth: '.'Basic '.$accessToken);

            // Trả về token từ cache
            return [
                'access_token' => $accessToken,
                'source' => 'cache'
            ];
        }

        // Nếu không có token trong cache hoặc token đã hết hạn
        return $this->refreshToken_BasicAuth($clientId, $clientSecret,$apiname);

    }

    public function refreshToken_BasicAuth($clientId, $clientSecret,$apiname)
    {
        // Tạo cache key dựa trên client_id để duy trì tính duy nhất cho mỗi client
        $cacheKey = 'oauth_access_token_'.$apiname;
        $credentials = base64_encode($clientId . ':' . $clientSecret);
        Log::info('refreshToken_BasicAuth: '.'Basic '.$credentials);
        Cache::put($cacheKey, 'Basic '.$credentials);
        return [
            'access_token' => 'Basic '.$credentials,
            'token_type' => 'BasicAuth',
            'source' => 'api_'.$apiname
        ];
    }


    public function getAccessToken_OAUTH2_ClientCredentials($tokenUrl, $clientId, $clientSecret, $scopes, $apiname)
    {
        // Tạo cache key dựa trên client_id để duy trì tính duy nhất cho mỗi client
        $cacheKey = 'oauth_access_token_'.$apiname;

        // Kiểm tra xem token có trong cache không
        if (Cache::has($cacheKey)) {
            $accessToken = Cache::get($cacheKey);
            Log::info('Token được lấy từ Cache.');

            // Trả về token từ cache
            return [
                'access_token' => $accessToken,
                'source' => 'cache'
            ];
        }

        // Nếu không có token trong cache hoặc token đã hết hạn
        return $this->refreshToken_OAUTH2_ClientCredentials($tokenUrl, $clientId, $clientSecret, $scopes, $apiname);
    }

    // Phương thức để lấy lại token mới khi hết hạn
    public function refreshToken_OAUTH2_ClientCredentials($tokenUrl, $clientId, $clientSecret, $scopes, $apiname)
    {
        $client = new Client();

        try {
            $response = $client->post($tokenUrl, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => $scopes,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            // Tạo cache key dựa trên client_id để duy trì tính duy nhất cho mỗi client
            $cacheKey = 'oauth_access_token_'.config('services.oauthapiendpoint.kiotviet_apiname');
            Log::info('refreshToken :: ' .$cacheKey);
            Cache::put($cacheKey, $body['access_token'], $body['expires_in']);

            Log::info('Token được lấy và lưu trữ trong Cache', [
                'access_token' => $body['access_token'],
                'expires_in' => $body['expires_in'],
            ]);

            return [
                'access_token' => $body['access_token'],
                'expires_in' => $body['expires_in'],
                'token_type' => $body['token_type'],
                'source' => 'api'
            ];
        } catch (\Exception $e) {
            Log::error('Không thể lấy được token', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);

            return ['error' => 'Không thể lấy lại token'];
        }
    }
}
