<?php

namespace App\Http\Controllers\APIendpoints\_1Office;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Đảm bảo bạn đã import service

class _1OfficeController extends Controller
{
    protected $oauthService;

    public function __construct(OAuthClientCredentialsService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    // --------------------------------------------
    // API KHÁCH HÀNG 1OFFICE
    // --------------------------------------------

    public function getCustomersList()
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Customer';

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint ' . $phanhe . ' của 1office.');
            $response = $client->get($host . '/api/customer/customer/gets', [
                'query' => [
                    'access_token' => $access_token,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách ' . $phanhe . ' từ API của ' . $apiname . ' thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách ' . $phanhe, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomerCode($code = null)
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Customer';
        Log::info('Bắt đầu lấy thông tin chi tiết của ' . $phanhe . '.');

        $client = new Client();
        try {
            $url = $host . '/api/customer/customer/item';
            $response = $client->get($url, [
                'query' => [
                    'access_token' => $access_token,
                    'code' => $code,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của ' . $phanhe . ' thành công từ API của ' . $apiname . '.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của ' . $phanhe . '', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatusAddressVerificationCustomer($code, $status)
    {
        // Lấy thông tin cấu hình từ config
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Customer';

        // Log bắt đầu quá trình xử lý
        Log::info('Bắt đầu cập nhật Status Address Verification Khách Hàng: '.$code);

        // Chuẩn bị dữ liệu để gửi qua API
        $body = [
            "access_token" => $access_token,
            "code" => $code,
            "cf7" => $status, // Sử dụng trạng thái được truyền vào
        ];

        // Log thông tin trước khi gửi yêu cầu tới API

        $client = new Client();

        try {
            // Xây dựng URL với các tham số query
            $url = $host . '/api/customer/customer/update?' . http_build_query($body);

            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Đọc và log phản hồi từ API
            $responseBody = json_decode((string) $response->getBody(), true);
            Log::info('Cập nhật thành công trạng thái xác thực địa chỉ khách hàng.');


            // Trả về phản hồi thành công
            return response()->json($responseBody, 201);

        } catch (\Exception $e) {
            // Log lỗi nếu có vấn đề xảy ra trong quá trình gửi yêu cầu
            Log::error('Lỗi khi cập nhật trạng thái xác thực địa chỉ khách hàng', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Trả về phản hồi lỗi
            return response()->json(['error' => 'Lỗi khi cập nhật trạng thái xác thực địa chỉ khách hàng', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus($code, $status)
    {
        // Lấy thông tin cấu hình từ config
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Customer';

        // Log bắt đầu quá trình xử lý
        Log::info('Bắt đầu cập nhật Status Address Verification Khách Hàng: '.$code);

        // Chuẩn bị dữ liệu để gửi qua API
        $body = [
            "access_token" => $access_token,
            "code" => $code,
            "cf7" => $status, // Sử dụng trạng thái được truyền vào
        ];

        // Log thông tin trước khi gửi yêu cầu tới API

        $client = new Client();

        try {
            // Xây dựng URL với các tham số query
            $url = $host . '/api/customer/customer/update?' . http_build_query($body);

            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Đọc và log phản hồi từ API
            $responseBody = json_decode((string) $response->getBody(), true);
            Log::info('Cập nhật thành công trạng thái xác thực địa chỉ khách hàng.');


            // Trả về phản hồi thành công
            return response()->json($responseBody, 201);

        } catch (\Exception $e) {
            // Log lỗi nếu có vấn đề xảy ra trong quá trình gửi yêu cầu
            Log::error('Lỗi khi cập nhật trạng thái xác thực địa chỉ khách hàng', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Trả về phản hồi lỗi
            return response()->json(['error' => 'Lỗi khi cập nhật trạng thái xác thực địa chỉ khách hàng', 'message' => $e->getMessage()], 500);
        }
    }

    // --------------------------------------------
    // API ĐƠN HÀNG 1OFFICE
    // --------------------------------------------

    public function getOrdersList()
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Order';

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint ' . $phanhe . ' của 1office.');
            $response = $client->get($host . '/api/sale/purchase/gets', [
                'query' => [
                    'access_token' => $access_token,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách ' . $phanhe . ' từ API của ' . $apiname . ' thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách ' . $phanhe, 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrderCode($code = null)
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Order';
        Log::info('Bắt đầu lấy thông tin chi tiết của ' . $phanhe . '.');

        $client = new Client();
        try {
            $url = $host . '/api/sale/purchase/item';
            $response = $client->get($url, [
                'query' => [
                    'access_token' => $access_token,
                    'code' => $code,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của ' . $phanhe . ' thành công từ API của ' . $apiname . '.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của ' . $phanhe . '', 'message' => $e->getMessage()], 500);
        }
    }

    protected function convertProducts(array $products)
    {
        $converted = [];

        foreach ($products as $product) {
            $product_id = $product["product_code"];
            $vat = $product["vat"];
            $desc = $product["desc"];
            $price = $product["price"];
            $amount = $product["amount"];
            $vat_id = $product["vat_id"];
            $unit_id = $product["unit_id"];
            $sale_off = $product["sale_off"];
            $unit_title = $product["unit_title"];
            $money_sale_off = $product["money_sale_off"];
            $technical_notes = $product["technical_notes"];

            $converted[] = [
                'product_id' => $product_id,
                'vat' => $vat,
                'desc' => $desc,
                'price' => $price,
                'amount' => $amount,
                'vat_id' => $vat_id,
                'unit_id' => $unit_id,
                'sale_off' => $sale_off,
                'unit_title' => $unit_title,
                'money_sale_off' => $money_sale_off,
                'technical_notes' => $technical_notes,
            ];
        }

        return $converted;
    }

    public function updateStatusOrder($code, $status, $statusAddress)
    {
        // Lấy thông tin cấu hình từ config
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $order_status = config('services.oauthapiendpoint1office.1office_cf_order_status');
        $address_full = config('services.oauthapiendpoint1office.1office_cf_order_address_full');
        $order_rate = config('services.oauthapiendpoint1office.1office_cf_order_rate');
        $internal_notes = config('services.oauthapiendpoint1office.1office_cf_order_internal_notes');
        $customer_notes = config('services.oauthapiendpoint1office.1office_cf_order_customer_notes');
        $phanhe = 'Order';

        // Log bắt đầu quá trình xử lý
        Log::info('Bắt đầu cập nhật trạng thái đơn hàng.', [
            'code' => $code,
            'status' => $status,
        ]);

        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = Order::where('code', $code)->first();

        // Kiểm tra xem đơn hàng có tồn tại không
        if (!$order) {
            Log::error('Không tìm thấy đơn hàng với mã code.', [
                'code' => $code,
            ]);

            return response()->json(['error' => 'Không tìm thấy đơn hàng với mã code: ' . $code], 404);
        }

        // Lấy dữ liệu chi tiết của đơn hàng
        $data = $order->FullData;
        $address_Ship = $order->Address_Ship;
        $addressFull = $address_Ship["street1"] . ', ' . $address_Ship["city"] . ', ' .$address_Ship["state"] . ', ' .$address_Ship["country"] . ', '  .$address_Ship["postalCode"]. ' ('.$statusAddress.').';
        // Chuẩn bị dữ liệu để gửi qua API
        $body = [
            "access_token" => $access_token,
            "code" => $code,
            $order_status => $status, // Sử dụng trạng thái được truyền vào
            $address_full => $addressFull,

            "detail" => json_encode($this->convertProducts($data["detail"])), // Chuyển đổi chi tiết sản phẩm sang JSON
        ];

        // Log thông tin trước khi gửi yêu cầu tới API

        $client = new Client();

        try {
            // Xây dựng URL với các tham số query
            $url = $host . '/api/sale/purchase/update?' . http_build_query($body);
            Log::info("url: ". $url);
            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Đọc và log phản hồi từ API
            $responseBody = json_decode((string) $response->getBody(), true);
            Log::info('Cập nhật thành công trạng thái đơn hàng qua API.');


            // Trả về phản hồi thành công
            return response()->json($responseBody, 201);

        } catch (\Exception $e) {
            // Log lỗi nếu có vấn đề xảy ra trong quá trình gửi yêu cầu
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng qua API.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Trả về phản hồi lỗi
            return response()->json(['error' => 'Lỗi khi cập nhật trạng thái đơn hàng qua API', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatusOrderInTransit(Request $request)
    {
        // Lấy thông tin cấu hình từ config
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $order_status = config('services.oauthapiendpoint1office.1office_cf_order_status');
        $address_full = config('services.oauthapiendpoint1office.1office_cf_order_address_full');
        $order_rate = config('services.oauthapiendpoint1office.1office_cf_order_rate');
        $tracking_number = config('services.oauthapiendpoint1office.1office_cf_order_tracking_number');
        $internal_notes = config('services.oauthapiendpoint1office.1office_cf_order_internal_notes');
        $customer_notes = config('services.oauthapiendpoint1office.1office_cf_order_customer_notes');
        $phanhe = 'Order';

        $data = $request->json()->all();

        $code = $data["ordercode"];
        $status = $data["status"];
        $trackingNumber = $data["trackingNumber"];
        $rate = $data["rate"];

        Log::info($status . " Trạng thái");


        // Ghi log dữ liệu JSON
        Log::info('Received JSON data:', $data);

        // Log bắt đầu quá trình xử lý
        Log::info('Bắt đầu cập nhật trạng thái đơn hàng.', [
            'code' => $code,
            'status' => $status,
        ]);

        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = Order::where('code', $code)->first();

        // Kiểm tra xem đơn hàng có tồn tại không
        if (!$order) {
            Log::error('Không tìm thấy đơn hàng với mã code.', [
                'code' => $code,
            ]);

            return response()->json(['error' => 'Không tìm thấy đơn hàng với mã code: ' . $code], 404);
        }
        // Lấy dữ liệu chi tiết của đơn hàng
        $data = $order->FullData;
        // Chuẩn bị dữ liệu để gửi qua API
        Log::info("RATE: ". $rate);
        $body = [
            "access_token" => $access_token,
            "code" => $code,
            $order_status => $status, // Sử dụng trạng thái được truyền vào$address_full => $addressFull,
            $order_rate => $rate, // Sử dụng trạng thái được truyền vào$address_full => $addressFull,
            $tracking_number => $trackingNumber, // Sử dụng trạng thái được truyền vào$address_full => $addressFull,
            "detail" => json_encode($this->convertProducts($data["detail"])), // Chuyển đổi chi tiết sản phẩm sang JSON
        ];

        // Log thông tin trước khi gửi yêu cầu tới API

        $client = new Client();

        try {
            // Xây dựng URL với các tham số query
            $url = $host . '/api/sale/purchase/update?' . http_build_query($body);
            Log::info("url: ". $url);
            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Đọc và log phản hồi từ API
            $responseBody = json_decode((string) $response->getBody(), true);
            Log::info('Cập nhật thành công trạng thái đơn hàng qua API.');


            // Trả về phản hồi thành công
            return response()->json($responseBody, 201);

        } catch (\Exception $e) {
            // Log lỗi nếu có vấn đề xảy ra trong quá trình gửi yêu cầu
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng qua API.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Trả về phản hồi lỗi
            return response()->json(['error' => 'Lỗi khi cập nhật trạng thái đơn hàng qua API', 'message' => $e->getMessage()], 500);
        }
    }

    // --------------------------------------------
    // API SẢN PHẨM 1OFFICE
    // --------------------------------------------

    public function getProductsList()
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Product';

        $client = new Client();

        try {
            Log::info('Gửi yêu cầu đến endpoint ' . $phanhe . ' của 1office.');
            $response = $client->get($host . '/api/sale/purchase/gets', [
                'query' => [
                    'access_token' => $access_token,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy danh sách ' . $phanhe . ' từ API của ' . $apiname . ' thành công.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy danh sách ' . $phanhe, 'message' => $e->getMessage()], 500);
        }
    }

    public function createProduct1Office(Request $request){
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        // $data = $request->all(); // Lấy dữ liệu từ request

        $data = [
            'code' => 'Test APi 001',
            'title' => 'Test API 001',
            'unit_id' => 'Cái'
        ];

        // Gọi hàm createCustomer với dữ liệu nhận được
        $response = $this->createProduct($data);

        // Kiểm tra kết quả của hàm createCustomer
        if ($response->status() == 201) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Tạo sản phẩm mới thành công.',
                'data' => $response
            ], 201);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo sản phẩm mới qua API '.$apiname.'.',
                'details' => $response
            ], $response->status());
        }
    }

    public function getProductCode($code = null)
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Product';
        Log::info('Bắt đầu lấy thông tin chi tiết của ' . $phanhe . '.');

        $client = new Client();
        try {
            $url = $host . '/api/sale/purchase/item';
            $response = $client->get($url, [
                'query' => [
                    'access_token' => $access_token,
                    'code' => $code,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Lấy thông tin chi tiết của ' . $phanhe . ' thành công từ API của ' . $apiname . '.');

            return response()->json($body);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chi tiết của ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của ' . $phanhe . '', 'message' => $e->getMessage()], 500);
        }
    }

    private function createProduct($data)
    {
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Product';
        Log::info('Bắt đầu thêm thông tin '.$phanhe.' mới qua API ' . $apiname . '.');


        $client = new Client();
        // Chuẩn bị dữ liệu để gửi qua API
        $body = [
            "access_token" => $access_token,
            "code" => $data["code"],
            "product_type" => 'Chỉ bán',
            "title" => $data["title"],
            "unit_id" => $data["unit_id"],
            // "properties" => '',
        ];

        try {
            $url = $host . '/api/warehouse/product/insert?' . http_build_query($body);

            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Thêm '.$phanhe.' mới thành công vào API của ' . $apiname . '.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm '.$phanhe.' mới qua API ' . $apiname . ': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi thêm '.$phanhe.' qua API ' . $apiname, 'message' => $e->getMessage()], 500);
        }
    }

}
