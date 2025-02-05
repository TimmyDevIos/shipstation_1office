<?php

namespace App\Http\Controllers\APIendpoints\_1Office_KiotViet;

use App\Http\Controllers\Controller;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Repositories\_1Office_KiotViet\Customer\CustomerRepository;
use Illuminate\Http\Request;

// Đảm bảo bạn đã import service

class _1OfficeToKiotVietController extends Controller
{
    protected $oauthService;
    protected $customerRepository;

    public function __construct(OAuthClientCredentialsService $oauthService, CustomerRepository $customerRepository)
    {
        $this->oauthService = $oauthService;
        $this->customerRepository = $customerRepository;
    }

    public function handleWebhook(Request $request)
    {
        $cf_orderStatus=config('services.oauthapiendpointkiotviet.1office_cf_order_status');
        $access_token=config('services.oauthapiendpoint1office.1office_access_token');
        $host=config('services.oauthapiendpoint1office.1office_host');
        $apiname=config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe='Order';
        $event = $request->input('event');
        $data = $request->input('data');
        $orderStatus = $request->input('data.0.'.$cf_orderStatus);

        Log::info('Sự kiện Webhook nhận được: ' .$event.'');

        if ($event === 'created') {
            Log::info('Đang xử lý sự kiện tạo mới '.$phanhe.'');
            return $this->store($data, $request);
        } elseif ($event === 'deleted') {
            $customerId = $request->input('data.0.code');
            Log::info('Đang xử lý sự kiện xóa cho mã khách hàng: ' . $customerId);
            $customer = $this->findCustomer($customerId);
            return $this->destroy($customer);
        } elseif ($event === 'updated') {
            Log::info('Đang xử lý sự kiện cập nhật');
            $customerId = $request->input('data.code');
            Log::info('Đang xử lý sự kiện cập nhật cho mã khách hàng: ' . $customerId);
            $customer = $this->findCustomer($customerId);
            return $this->update($data, $customer);
        } else {
            Log::warning('Nhận được sự kiện không hợp lệ: ' . $event);
            return response()->json(['message' => 'Sự kiện không hợp lệ'], 400);
        }
    }

    protected function findCustomer($customerId)
    {
        Log::info('Đang tìm kiếm khách hàng với mã: ' . $customerId);
        
        if (!$customerId) {
            Log::error('Mã khách hàng không được cung cấp');
            throw new \Exception('Mã khách hàng không được cung cấp');
        }

        $customer = $this->customerRepository->findByCode($customerId);
        
        if (!$customer) {
            Log::error('Không tìm thấy khách hàng');
            throw new \Exception('Không tìm thấy khách hàng');
        }

        Log::info('Khách hàng được tìm thấy: ' . $customer->id);
        return $customer;
    }

    public function store($data, Request $request)
    {
        Log::info($request);
        try {
            $customerData = [
                'type' => $data['type'],
                'code' => $data['code'],
                'name' => $data['name'],
                'gender' => $data['gender'],
                'birthday' => $data['birthday'],
                'phones' => $data['phones'],
                'emails' => $data['emails'],
                'address' => $data['address'],
                'id1OFFICE' => $request->input('postId'),
                'FullData' => $data,
            ];
            $customer = $this->customerRepository->create($customerData);

            Log::info('Dữ liệu khách hàng nhận được thành công', ['customer' => $customer]);
            return response()->json(['message' => 'Dữ liệu khách hàng nhận được thành công', 'customer' => $customer], 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lưu trữ khách hàng: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi xử lý yêu cầu lưu trữ', 'error' => $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $customers = $this->customerRepository->all();
        return response()->json($customers);
    }
    public function update($data, Customer $customer)
    {
        try {
            $this->customerRepository->update($customer, [
                
                'type' => $data['type'],
                'name' => $data['name'],
                'gender' => $data['gender'],
                'birthday' => $data['birthday'],
                'phones' => $data['phones'],
                'emails' => $data['emails'],
                'address' => $data['address'],
                'FullData' => $data,
            ]);

            return response()->json(['message' => 'Khách hàng được cập nhật thành công', 'customer' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi xử lý yêu cầu cập nhật', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        Log::info('Bắt đầu xóa khách hàng', ['customer_id' => $customer->id]);

        try {
            $result = $this->customerRepository->delete($customer);

            if ($result) {
                Log::info('Khách hàng được xóa thành công', ['customer_id' => $customer->id]);
                return response()->json(['message' => 'Khách hàng được xóa thành công', 'customer' => $customer]);
            } else {
                Log::error('Không thể xóa khách hàng - phương thức xóa trả về false', ['customer_id' => $customer->id]);
                return response()->json(['message' => 'Không thể xóa khách hàng'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa khách hàng: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa khách hàng'], 500);
        }
    }


    private function createCustomer($data)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt đầu thêm thông tin khách hàng mới qua API '.$nameapi.'.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();

        try {
            $url = $host.'/customers';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Thêm thông tin khách hàng mới thành công vào API của '.$nameapi.'.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm thông tin khách hàng mới qua API '.$nameapi.': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi thêm thông tin khách hàng mới qua API '.$nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    private function updateCustomer($id, $data)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt update thêm thông tin khách hàng qua API '.$nameapi.'.');

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
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Update thông tin khách hàng mới thành công vào API của '.$nameapi.'.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi update thông tin khách hàng mới vào API '.$nameapi.': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi update thông tin khách hàng mới vào API '.$nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    private function deleteCustomer($id)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt delete thông tin khách hàng qua API '.$nameapi.'.');

        // Lấy access token
        $accessToken = $this->getAccessToken();


        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();

        try {
            $url = $host.'/customers'.'/'.$id;

            $response = $client->delete($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Delete thông tin khách hàng thành công qua API của '.$nameapi.'.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi delete thông tin khách hàng qua API '.$nameapi.': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi delete thông tin khách hàng mới vào API '.$nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    public function createCustomerKiotviet(Request $request){
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Gọi hàm createCustomer với dữ liệu nhận được
        $response = $this->createCustomer($data);

        // Kiểm tra kết quả của hàm createCustomer
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Tạo khách hàng mới thành công.',
                'data' => $response
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API '.$nameapi.'.',
                'details' => $response
            ], $response->status());
        }
    }

    public function updateCustomerKiotviet($id, Request $request){
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Gọi hàm createCustomer với dữ liệu nhận được
        $response = $this->updateCustomer($id, $data);

        // Kiểm tra kết quả của hàm createCustomer
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Update khách hàng mới thành công qua API '.$nameapi.'.',
                'data' => $response
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API '.$nameapi.'.',
                'details' => $response
            ], $response->status());
        }
    }
    public function deleteCustomerKiotviet($id){
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        // Gọi hàm createCustomer với dữ liệu nhận được
        $response = $this->deleteCustomer($id);

        // Kiểm tra kết quả của hàm createCustomer
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Delete khách hàng mới thành công qua API '.$nameapi.'.',
                'data' => $response
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình xoá khách hàng qua API '.$nameapi.'.',
                'details' => $response
            ], $response->status());
        }
    }

    private function getAccessToken_basic()
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


    private function getAccessToken()
    {
        $apiname = config('services.oauthapiendpointkiotviet.kiotviet_apiname', '');
        $cacheKey = 'oauth_access_token_'.$apiname;
        $accessToken = Cache::get($cacheKey);
        if(Cache::has($cacheKey)){
            Log::info('Lấy access token từ cache.');
            return $accessToken;
        }
        else {
            // Định nghĩa các tham số cần thiết
            $tokenUrl = config('services.oauthapiendpointkiotviet.kiotviet_token_url');
            $clientId = config('services.oauthapiendpointkiotviet.kiotviet_client_id');
            $clientSecret = config('services.oauthapiendpointkiotviet.kiotviet_client_secret');
            $scopes = config('services.oauthapiendpointkiotviet.kiotviet_scopes', '');
            

            // Log các tham số
            Log::info('Thử lấy access token mới');

            // Sử dụng service để lấy accessToken mới
            $accessTokenData = $this->oauthService->getAccessToken_OAUTH2_ClientCredentials($tokenUrl, $clientId, $clientSecret, $scopes, $apiname);
            if (isset($accessTokenData['error'])) {
                Log::error('Lỗi khi lấy access token: ' . $accessTokenData['error']);
                return false;
            }
            $accessToken = $accessTokenData['access_token'];

            // Phần này bị dư phần lưu vì trong hàm tổng quát đã lưu rồi
            
            // $expiresInMinutes = $accessTokenData['expires_in']; // Chuyển đổi giây sang phút

            // // Lưu accessToken mới vào cache
            // Cache::put('kiotviet_access_token', $accessToken, $expiresInMinutes);
            // -----------------------
            return $accessToken;
        }
    }


    // ---------------------- //


    public function getCategoryList()
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        Log::info('Bắt đầu lấy danh sách danh mục.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint danh mục của KiotViet.');
            $response = $client->get($host.'/categories', [
                'headers' => [
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách danh mục từ API của KiotViet thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách danh mục: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách danh mục', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomersList()
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        Log::info('Bắt đầu lấy danh sách danh mục.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint danh mục của KiotViet.');
            $response = $client->get($host.'/customers', [
                'headers' => [
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách danh mục từ API của KiotViet thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách danh mục: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách danh mục', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomerId($id = null)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        Log::info('Bắt đầu lấy thông tin chi tiết của khách hàng.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();
        try {
            $url = $host.'/customers/' . $id;
            $response = $client->get($url, [
                'headers' => [
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
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

    public function getCustomerCode($code)
    {
        $host=config('services.oauthapiendpointkiotviet.kiotviet_host');
        Log::info('Bắt đầu lấy thông tin chi tiết của khách hàng.');
        Log::info('Basic: '.$this->getAccessToken_basic());

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500);
        }

        $client = new Client();

        try {
            $url = $host.'/customers/code/' . $code;
            $response = $client->get($url, [
                'headers' => [
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
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

}
