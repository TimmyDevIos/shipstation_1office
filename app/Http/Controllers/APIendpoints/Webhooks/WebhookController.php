<?php

namespace App\Http\Controllers\APIendpoints\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Services\OAuthClientCredentialsService;

// Đảm bảo bạn đã import service

class WebhookController extends Controller
{

    protected $oauthService;

    public function __construct(OAuthClientCredentialsService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function getWebhook(Request $request)
    {
        $requestDataFormatted = json_encode($request->all(), JSON_PRETTY_PRINT);

        Log::info("Chuẩn bị ghi Log: " . $requestDataFormatted);

        // Log dữ liệu với channel 'webhook'
        Log::channel('webhook')->info("Request nhận được từ WEBHOOK:\n" . $requestDataFormatted);

        // Chuyển chuỗi JSON thành mảng associative
        $data = json_decode($requestDataFormatted, true);

        // Kiểm tra xem khóa "resource_url" có tồn tại trong mảng hay không
        if(array_key_exists("resource_url", $data)) {
            $data_resource_url = $this->resource_url($data['resource_url']);
            $resourceUrlDataFormatted = json_encode($data_resource_url, JSON_PRETTY_PRINT);
            Log::channel('webhook')->info("Request nhận được từ WEBHOOK ".$data['resource_type']." :\n" . $resourceUrlDataFormatted);
        }
        return response()->json(['message' => 'Thông tin đã được lưu'], 200);
    }

    public function showLogs()
    {
        $filePath = storage_path('logs/webhook.log');
    
        if (!File::exists($filePath)) {
            abort(404, 'File log không tồn tại.');
        }
    
        $logsContent = File::get($filePath);
    
        $pattern = '/(\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local.INFO:)/';
        $logs = preg_split($pattern, $logsContent, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    
        $combinedLogs = [];
        for ($i = 0; $i < count($logs); $i += 2) {
            if (isset($logs[$i + 1])) {
                $logEntry = $logs[$i] . $logs[$i + 1];
    
                // Mã hóa chuỗi thành JSON, sau đó giải mã để chuyển đổi các ký tự Unicode
                $encodedLogEntry = json_encode($logEntry);
                $decodedLogEntry = json_decode($encodedLogEntry);
    
                $combinedLogs[] = $decodedLogEntry;
            }
        }
    
        return view('logs.show', compact('combinedLogs'));
    }

    private function resource_url($resource_url)
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
            $response = $client->get($url, [
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
