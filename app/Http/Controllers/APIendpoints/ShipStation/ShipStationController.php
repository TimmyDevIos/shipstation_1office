<?php

namespace App\Http\Controllers\APIendpoints\ShipStation;

use App\Http\Controllers\Controller;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Đảm bảo bạn đã import service

class ShipStationController extends Controller
{
    protected $oauthService;

    public function __construct(OAuthClientCredentialsService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    private function updateCustomer($id, $data)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        $apiname= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt update thêm thông tin khách hàng qua API '.$apiname.'.');

        // Lấy access token
        $accessToken = $this->getAccessToken();


        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();
        Log::info($data);

        try {
            $url = $host.'/customers'.'/'.$id;

            $response = $client->put($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Update thông tin khách hàng mới thành công vào API của '.$apiname.'.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi update thông tin khách hàng mới vào API '.$apiname.': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi update thông tin khách hàng mới vào API '.$apiname, 'message' => $e->getMessage()], 500);
        }
    }

    private function deleteOrder($id)
    {
        $host=config('services.oauthapiendpointshipstation.shipstation_host');
        $apiname=config('services.oauthapiendpointshipstation.shipstation_apiname');
        Log::info('Bắt delete thông tin khách hàng qua API '.$apiname.'.');

        // Lấy access token
        $accessToken = $this->getAccessToken();


        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();

        try {
            $url = $host.'/orders'.'/'.$id;

            $response = $client->delete($url, [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Delete thông tin khách hàng thành công qua API của '.$apiname.'.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi delete thông tin khách hàng qua API '.$apiname.': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi delete thông tin khách hàng mới vào API '.$apiname, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteOrderShipStation($id){
        $host=config('services.oauthapiendpointshipstation.shipstation_host');
        $apiname=config('services.oauthapiendpointshipstation.shipstation_apiname');
        $phanhe='Order';
        // Gọi hàm createCustomer với dữ liệu nhận được
        $response = $this->deleteOrder($id);

        // Kiểm tra kết quả của hàm createCustomer
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Tạo '.$phanhe.' mới thành công.',
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo '.$phanhe.' mới qua API '.$apiname.'.',
                'details' => $response
            ], $response->status());
        }
    }

    public function deletelist(Request $request){
        // Lấy danh sách orders từ body của request
        $orders = $request->input('orders');

        // Lặp qua từng order để lấy orderId và gọi hàm deleteOrderShipStation
        foreach ($orders as $order) {
            $orderId = $order['orderId'];
            $result = $this->deleteOrder($orderId);
            Log::info("Đã xoá order có id ".$orderId);
        }
        return response()->json(['message' => 'Orders processed and logged.']);
    }


    // --------------------------------------------
    // API KHÁCH HÀNG 1OFFICE
    // --------------------------------------------


//    public function getCustomersList()
//    {
//        $host=config('services.oauthapiendpointshipstation.shipstation_host');
//        $apiname=config('services.oauthapiendpointshipstation.shipstation_apiname');
//        $phanhe='Order';
//
//        // Lấy access token
//        $accessToken = $this->getAccessToken();
//
//        if (!$accessToken) {
//            Log::error('Không thể lấy access token.');
//            return response()->json(['error' => 'Không thể lấy access token'], 500);
//        }
//
//
//        $client = new Client();
//
//        try {
//            Log::info('Gửi yêu cầu đến endpoint '.$phanhe.' của 1office.');
//            $response = $client->get($host.'/orders', [
//                'headers' => [
//                    'Authorization' => $accessToken,
//                ],
//                'query' => [
//                    'access_token' => $access_token,
//                ],
//            ]);
//
//            $body = json_decode((string) $response->getBody(), true);
//            Log::info('Lấy danh sách '.$phanhe.' từ API của '.$apiname.' thành công.');
//
//            return response()->json($body);
//        } catch (\Exception $e) {
//            Log::error('Lỗi khi lấy danh sách '.$phanhe.': ' . $e->getMessage());
//            return response()->json(['error' => 'Lỗi khi lấy danh sách '.$phanhe , 'message' => $e->getMessage()], 500);
//        }
//    }

    public function getCustomerCode($code = null)
    {
        $access_token=config('services.oauthapiendpoint1office.1office_access_token');
        $host=config('services.oauthapiendpoint1office.1office_host');
        $apiname=config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe='Customer';
        Log::info('Bắt đầu lấy thông tin chi tiết của '.$phanhe.'.');

        $client = new Client();
        try {
            $url = $host.'/api/customer/customer/item';
            $response = $client->get($url, [
                'query' => [
                    'access_token' => $access_token,
                    'code' => $code,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của '.$phanhe.' thành công từ API của '.$apiname.'.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của '.$phanhe.': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của '.$phanhe.'', 'message' => $e->getMessage()], 500);
        }
    }

    // --------------------------------------------
    // API ĐƠN HÀNG 1OFFICE
    // --------------------------------------------

    public function getOrdersList()
    {
        $host=config('services.oauthapiendpointshipstation.shipstation_host');
        $apiname=config('services.oauthapiendpointshipstation.shipstation_apiname');
        $phanhe='Order';

        $page=1;
        $pageSize=10;

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }


        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint '.$phanhe.' của 1office.');
            $response = $client->get($host.'/orders', [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
                'query' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách '.$phanhe.' từ API của '.$apiname.' thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách '.$phanhe.': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách '.$phanhe , 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrderCode($code = null)
    {
        $host=config('services.oauthapiendpointshipstation.shipstation_host');
        $apiname=config('services.oauthapiendpointshipstation.shipstation_apiname');
        $phanhe='Order';

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();
        try {
            $url = $host.'/orders/'.$code;
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
                'query' => [

                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của '.$phanhe.' thành công từ API của '.$apiname.'.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của '.$phanhe.': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của '.$phanhe.'', 'message' => $e->getMessage()], 500);
        }
    }

    // --------------------------------------------
    // API SẢN PHẨM 1OFFICE
    // --------------------------------------------

    public function getProductsList()
    {
        $access_token=config('services.oauthapiendpoint1office.1office_access_token');
        $host=config('services.oauthapiendpoint1office.1office_host');
        $apiname=config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe='Product';

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint '.$phanhe.' của 1office.');
            $response = $client->get($host.'/api/sale/purchase/gets', [
                'query' => [
                    'access_token' => $access_token,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách '.$phanhe.' từ API của '.$apiname.' thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách '.$phanhe.': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách '.$phanhe , 'message' => $e->getMessage()], 500);
        }
    }

    public function getProductCode($code = null)
    {
        $access_token=config('services.oauthapiendpoint1office.1office_access_token');
        $host=config('services.oauthapiendpoint1office.1office_host');
        $apiname=config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe='Product';
        Log::info('Bắt đầu lấy thông tin chi tiết của '.$phanhe.'.');

        $client = new Client();
        try {
            $url = $host.'/api/sale/purchase/item';
            $response = $client->get($url, [
                'query' => [
                    'access_token' => $access_token,
                    'code' => $code,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của '.$phanhe.' thành công từ API của '.$apiname.'.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của '.$phanhe.': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của '.$phanhe.'', 'message' => $e->getMessage()], 500);
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
