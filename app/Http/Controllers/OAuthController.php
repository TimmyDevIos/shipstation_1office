<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    public function getAccessToken()
    {
        $client = new Client();

        try {
            $response = $client->post('https://id.kiotviet.vn/connect/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'd3e1fce9-90d4-483e-957d-3ec06abf1c5f',
                    'client_secret' => '755A3F02C92877BB3C570A4F28D3E0B35D77B82C',
                    'scopes' => 'PublicApi.Access',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            // Tính toán thời gian hết hạn của token và chuyển đổi sang phút
            $expiresInMinutes = $body['expires_in'] / 60; // Chuyển đổi giây sang phút

            // Lưu access token vào cache với thời gian hết hạn đã tính toán
            Cache::put('kiotviet_access_token', $body['access_token'], $expiresInMinutes);

            // Ghi log token được lưu vào cache
            Log::info('Kiotviet access token retrieved and stored in cache.', [
                'access_token' => $body['access_token'],
                'expires_in_minutes' => $expiresInMinutes // Ghi log thời gian hết hạn theo phút
            ]);

            return response()->json([
                'access_token' => $body['access_token'],
                'expires_in' => $body['expires_in'], // Giữ nguyên giá trị giây cho phản hồi JSON
                'token_type' => $body['token_type'],
            ]);
        } catch (\Exception $e) {
            // Ghi log lỗi khi không thể lấy access token
            Log::error('Failed to retrieve Kiotviet access token.', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);

            return response()->json(['error' => 'Failed to retrieve access token'], 500);
        }
    }
}