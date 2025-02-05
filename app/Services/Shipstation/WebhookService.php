<?php

namespace App\Services\Shipstation;

use GuzzleHttp\Exception\RequestException;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WebhookService
{
    protected $oauthService;

    public function __construct(OAuthClientCredentialsService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function changeStatusOrder($code, $status)
    {
        $host = config('services.apihub.apihub_host');
        $token = config('services.apihub.apihub_token');
        $nameapi = config('services.oauthapiendpointshipstation.shipstation_apiname');

        $client = new Client(['verify' => false]);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        try {
            $response = $client->request('PUT', $host . '/api/1office/order/status/' . $code . '/' . $status, [
                'headers' => $headers,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                // Ghi lại audit log với context là "api" và third_party_unit là "mockup API"

                // $order = Order::where('code', $code)->first();
                // $order->update(['orderStatus' => $status]);
                // $body = json_decode((string) $response->getBody(), true);
                Log::info('Đã cập nhật trạng thái Order thành công. [' . $status . ']');
            } else {
                Log::error('Không thể cập nhật trạng thái đơn hàng qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API ' . $nameapi . ': ' . $e->getMessage());
        }
    }

    public function get_resource_url($resource_url)
    {
        Log::info('Bắt đầu lấy thông tin chi tiết của khách hàng.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();
        try {
            $url = $resource_url;

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của khách hàng thành công từ API của KiotViet.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của khách hàng: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của khách hàng', 'message' => $e->getMessage()], 500);
        }
    }

    private function getAccessToken()
    {
        $apiname = config('services.oauthapiendpointshipstation.shipstation_apiname', '');
        $cacheKey = 'oauth_access_token_'.$apiname;
        $accessToken = Cache::get($cacheKey);
        if(Cache::has($cacheKey)){
            Log::info('Lấy access token từ cache.');
            return $accessToken;
        }
        else {
            // Định nghĩa các tham số cần thiết
            $clientId = config('services.oauthapiendpointshipstation.shipstation_client_id');
            $clientSecret = config('services.oauthapiendpointshipstation.shipstation_client_secret');            

            // Log các tham số
            Log::info('Thử lấy access token mới');

            // Sử dụng service để lấy accessToken mới
            $accessTokenData = $this->oauthService->getAccessToken_BasicAuth($clientId, $clientSecret, $apiname);
            if (isset($accessTokenData['error'])) {
                Log::error('Lỗi khi lấy access token: ' . $accessTokenData['error']);
                return false;
            }

            $accessToken = $accessTokenData['access_token'];
            return $accessToken;
        }
    }
}