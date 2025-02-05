<?php

namespace App\Http\Controllers\APIendpoints\_1Office_ShipStation;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Alert;
use App\Repositories\_1Office_ShipStation\Order\OrderRepository;
use App\Services\OAuthClientCredentialsService;
use App\Services\Shipstation\WebhookService;
use App\Services\_1Office\OrderService;
use App\Services\_1Office\NotifyService;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

// Đảm bảo bạn đã import service

class _1OfficeToShipStationController extends Controller
{
    protected $oauthService;
    protected $orderRepository;
    protected $webhookService;
    protected $orderService;
    protected $notifyService;


    public function __construct(
        OAuthClientCredentialsService $oauthService,
        OrderRepository $orderRepository,
        WebhookService $webhookService,
        NotifyService $notifyService,
        OrderService $orderService)
    {
        $this->oauthService = $oauthService;
        $this->orderRepository = $orderRepository;
        $this->webhookService = $webhookService;
        $this->orderService = $orderService;
        $this->notifyService = $notifyService;
    }

    public function ORDER_NOTIFY_Webhook(Request $request){
        $nameapi = config('services.oauthapiendpointshipstation.shipstation_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Hoặc log request theo định dạng JSON

        if(!array_key_exists("resource_url", $data)) {
            return response()->json([
                'message' => 'Không tìm thấy resource_url trong request',
                'errors' => 'true'
            ]);

        }

        // Gọi hàm createOrder với dữ liệu nhận được
        $response = $this->webhookService->get_resource_url($data["resource_url"]);

        // Kiểm tra kết quả của hàm createOrder
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            // Lấy nội dung JSON như một đối tượng
            $orderData = $response->getData();

            // Nếu bạn cần một mảng thay vì một đối tượng
            $orderArray = json_decode(json_encode($orderData), true);
            Log::info('Order Data (Array):', $orderArray);
            $orderNumber = $orderArray['orders'][0]['orderNumber'];

            $this->orderService->changeStatusOrder($orderNumber, "Label Created");
            return response()->json([
                'message' => 'Cập nhật trạng thái đơn hàng thành "Label Created" trên 1Office thành công.',
                'errors' => 'true'
            ],200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API ' . $nameapi . '.',
                'details' => $response,
            ], $response->status());
        }
    }

    public function pushNotify()
    {
        Log::info("Vào hàm pushNotify");
        $this->notifyService->pushNotify1Office("Thông báo test", "Nội dung thông báo", "trianvuong@gmail.com");
        return response()->json([
            'message' => 'Đã gửi thông báo',
            'errors' => 'true'
        ]);
    }

    public function SHIP_NOTIFY_Webhook(Request $request){
        Log::error("Đã gọi hàm SHIP_NOTIFY_Webhook");

        $nameapi = config('services.oauthapiendpointshipstation.shipstation_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Hoặc log request theo định dạng JSON

        if(!array_key_exists("resource_url", $data)) {
            return response()->json([
                'message' => 'Không tìm thấy resource_url trong request',
                'errors' => 'true'
            ]);
            Log::error("Không tìm thấy resource_url trong request");
        }
        Log::info("Có resource_url: ".$data["resource_url"]);

//         Gọi hàm createOrder với dữ liệu nhận được
        $response = $this->webhookService->get_resource_url($data["resource_url"]);

        // Kiểm tra kết quả của hàm createOrder
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            // Lấy nội dung JSON như một đối tượng
            $orderData = $response->getData();
            // Kiểm tra kiểu dữ liệu
            if (is_object($orderData)) {
                // Chuyển đổi đối tượng stdClass thành mảng nếu cần
                $responseArray = json_decode(json_encode($orderData), true);
                $orderNumbers = []; // Tạo biến để lưu tất cả các orderNumber
                // Xử lý dữ liệu
                foreach ($responseArray['shipments'] as $item) {
                    $orderNumbers[] = $item['orderNumber']; // Lưu orderNumber vào mảng
                    $orderNumber = $item['orderNumber'];
                    $trackingNumber = $item['trackingNumber'];
                    $rate = $item['shipmentCost'];
                    $this->orderService->serviceUpdateStatusOrderInTransit($orderNumber, "In Transit", $trackingNumber, $rate);
                }
                // Tạo thông báo message chứa danh sách orderNumbers
                $message = 'Cập nhật trạng thái đơn hàng thành "In Transit" trên 1Office thành công. Danh sách orderNumbers: ' . implode(', ', $orderNumbers);

                return response()->json([
                    'message' => $message,
                    'errors' => 'true'
                ], 200);
            } else {
                Log::error('Dữ liệu không phải là đối tượng stdClass.');
            }
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API ' . $nameapi . '.',
                'details' => $response,
            ], $response->status());
        }
    }

    public function handleWebhook(Request $request)
    {
        $cf_orderStatus = config('services.oauthapiendpoint1office.1office_cf_order_status');
        $access_token = config('services.oauthapiendpoint1office.1office_access_token');
        $host = config('services.oauthapiendpoint1office.1office_host');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Order';
        $event = $request->input('event'); //lấy ra sự kiện của webhook
        if($event === 'deleted'){
            $orderCode = $request->input('data.0.code'); //lấy code nhận được từ webhook
        }
        else{
            $orderCode = $request->input('data.code'); //lấy code nhận được từ webhook
        }
        $order = $this->orderExists($orderCode, $phanhe); //kiểm tra order từ webhook có trong database chưa
        $orderbyAPI = json_decode($this->getOrderByCode($orderCode, $phanhe)->getContent(), true); //gọi API lấy đầy đủ thông tin chi tiết Đơn hàng bằng Code
        Log::info('orderbyAPI: ',$orderbyAPI);
        if($event !== 'deleted'){
            $orderStatus = $orderbyAPI['data']['cf9']; //lấy ra trạng tháng đơn hàng từ API
        }else{
            $orderStatus = ""; //lấy ra trạng tháng đơn hàng từ API
        }

        Log::info('Sự kiện Webhook nhận được: ' . $event . ' và trạng thái '.$phanhe.': ' . $orderStatus . '');

        if (!$order && $event === 'updated' && $orderStatus === 'Success Payment') {
            Log::info('Đang xử lý sự kiện tạo mới '.$phanhe.'');
            return $this->store($orderbyAPI, $request, $phanhe);
        } elseif ($order && $event === 'updated') {
            // Log::info('Đang xử lý sự kiện cập nhật cho mã '.$phanhe.': ' . $orderCode);
            // $order = $this->findOrder($orderCode, $phanhe);
            // return $this->update($orderbyAPI, $order, $phanhe);
            return response()->json(['message' => 'Không được cập nhật khi ở trạng thái Lable Create'], 400);
        } elseif ($order && $event === 'deleted') {
            Log::info('Đang xử lý sự kiện xóa cho mã '.$phanhe.': ' . $orderCode);
            $order = $this->findOrder($orderCode, $phanhe);
            return $this->destroy($order);
        }
        else {
            Log::warning('Nhận được sự kiện không hợp lệ: ' . $event);
            return response()->json(['message' => 'Sự kiện không hợp lệ'], 400);
        }
    }

    protected function findOrder($orderCode, $phanhe)
    {
        Log::info('Đang tìm kiếm '.$phanhe.' với mã: ' . $orderCode);

        if (!$orderCode) {
            Log::error('Mã '.$phanhe.' không được cung cấp');
            throw new \Exception('Mã '.$phanhe.' không được cung cấp');
        }

        $order = $this->orderRepository->findByCode($orderCode);

        if (!$order) {
            Log::error('Không tìm thấy '.$phanhe.'');
            throw new \Exception('Không tìm thấy '.$phanhe.'');
        }

        Log::info(''.$phanhe.' được tìm thấy: ' . $order->id);
        return $order;
    }

    protected function orderExists($code, $phanhe)
    {
        $order = $this->orderRepository->findByCode($code);

        if ($order) {
            Log::info('Tìm thấy '.$phanhe.'');
            return true;
        } else {
            Log::info('Không Tìm thấy '.$phanhe.'');
            return false;
        }
    }

    protected function extractWeight(string $input): int
    {
        $pattern = "/weight \(OZ\) : (\d+)/";
        preg_match($pattern, $input, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    protected function getOrderByCode($code, $phanhe)
    {
        $host = config('services.apihub.apihub_host');
        $token = config('services.apihub.apihub_token');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Order';

        $client = new Client(['verify' => false]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
        try {
            $url = $host . '/api/1office/order/' . $code;
            $response = $client->get($url, [
                'headers' => $headers,
                'verify' => false,
            ]);
            $body = json_decode((string) $response->getBody(), true);
            return response()->json($body);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của ' . $phanhe . '', 'message' => $e->getMessage()], 500);
        }
    }

    protected function getCustomerByCode($code)
    {
        $host = config('services.apihub.apihub_host');
        $token = config('services.apihub.apihub_token');
        $apiname = config('services.oauthapiendpoint1office.1office_apiname');
        $phanhe = 'Customer';

        $client = new Client(['verify' => false]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
        try {
            $url = $host . '/api/1office/customer/' . $code;
            $response = $client->get($url, [
                'headers' => $headers,
                'verify' => false,
            ]);
            $body = json_decode((string) $response->getBody(), true);
            return response()->json($body);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi lấy thông tin chi tiết của ' . $phanhe . '', 'message' => $e->getMessage()], 500);
        }
    }

    public function getTotalWeight(array $products): int
    {
        $totalWeight = 0;

        foreach ($products as $product) {
            $weight = $this->extractWeight($product['product_properties']);
            $amount = $product['amount'];
            $totalWeight += $weight * $amount;
        }
        return $totalWeight;
    }

    protected function convertProducts(array $products)
    {
        $converted = [];

        foreach ($products as $product) {
            $sku = $product['product_code'];
            $name = $product['product_name'];
            $weightUnits = $this->extractWeight($product['product_properties']);
            $quantity = $product['amount'];
            $unitPrice = $product['price'];

            $converted[] = [
                'sku' => $sku,
                'name' => $name,
                'weight' => [
                    'value' => $weightUnits,
                    'units' => 'ounces',
                    'WeightUnits' => 1,
                ],
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'productId' => '',
            ];
        }

        return json_encode($converted);
    }

    protected function convertAdvancedOptions()
    {

        $storeId = config('services.oauthapiendpointshipstation.shipstation_store_id');

        // Generate the address
        $advancedOptions = [
            'storeId' => $storeId,
        ];

        return $advancedOptions;
    }

    public function convertAddress($dataCustomer)
    {
        // Trích xuất các trường cần thiết
        $name = $dataCustomer['name'];
        $company = $dataCustomer['type'] == "Tổ chức" ? $dataCustomer['name'] : null;
        $street1 = $dataCustomer['cf2'];
        $street2 = $dataCustomer['cf3'];
        $street3 = null;
        $city = $dataCustomer['cf4'];
        $state = $dataCustomer['cf5'];
        $postalCode = $dataCustomer['cf6'];
        $country = $dataCustomer['cf1'];
        $phone = $dataCustomer['phones'];
        $addressVerified = null;
        $residential = null;

        // Tạo địa chỉ với thứ tự đã chỉ định
        $address = [
            'city' => $city,
            'name' => $name,
            'phone' => $phone,
            'state' => $state,
            'company' => $company,
            'country' => $country,
            'street1' => $street1,
            'street2' => $street2,
            'street3' => $street3,
            'postalCode' => $postalCode,
            'residential' => $residential,
            'addressVerified' => $addressVerified,

        ];

        // Mã hóa mảng thành JSON với thứ tự đã chỉ định
        return $address;
    }

    private function checkErrorsProperties(array $products)
    {
        $pattern = "/weight \(OZ\) : (\d+)/";
        $errorProducts = [];

        foreach ($products as $product) {
            if (!isset($product['product_properties']) || !preg_match($pattern, $product['product_properties'])) {
                $errorProducts[] = $product['product_code'];
            }
        }

        if (!empty($errorProducts)) {
            return [
                'status' => 'error',
                'error_products' => $errorProducts
            ];
        }

        return [
            'status' => 'success',
            'error_products' => []
        ];
    }

    private function checkErrorsAdressCustomer($dataCustomer)
    {
        $country = config('services.oauthapiendpoint1office.1office_cf_country');
        $address_line_1 = config('services.oauthapiendpoint1office.1office_cf_address_line_1');
        $city = config('services.oauthapiendpoint1office.1office_cf_city');
        $state = config('services.oauthapiendpoint1office.1office_cf_state');
        $zip_Code = config('services.oauthapiendpoint1office.1office_cf_zip_Code');
        if($dataCustomer[$country] == '' ||$dataCustomer[$address_line_1] == '' ||$dataCustomer[$city] == '' ||$dataCustomer[$state] == '' ||$dataCustomer[$zip_Code] == '' ){
            return [
                'status' => 'error',
                'error_products' => []
            ];
        }
        return [
            'status' => 'success',
            'error_products' => []
        ];
    }


    public function store($orderbyAPI, Request $request, $phanhe)
    {
        $dataAPI = $orderbyAPI['data']; // Lấy data order từ API
        $customerResponse = $this->getCustomerByCode($dataAPI['customer_code'], $phanhe);
        $dataCustomer = json_decode($customerResponse->getContent(), true)['data']; // Lấy data khách hàng từ API

        if (empty($dataCustomer)) {
            // Xử lý khi không tìm thấy khách hàng
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy thông tin khách hàng'
            ], 404);
        }

        $detail = $dataAPI['detail']; // Danh sách sản phẩm trong order từ API
        $code = $dataAPI['code'];

        $result = $this->checkErrorsAdressCustomer($dataCustomer);
        if ($result['status'] === 'error') {
            // Lưu trạng thái lỗi vào alert
            Alert::create([
                'type' => 'Địa chỉ khách hàng còn thiếu',
                'message' => 'Thông tin địa chỉ của khách hàng thuộc đơn hàng '.$code.' bị thiếu thông tin.',
                'solution' => 'Kiểm tra lại khách hàng '.$dataCustomer['name'] .' - '.$dataCustomer['code'] .' và cập nhật lại thông tin',
                'status' => 'New'
            ]);

            // Dừng công việc và trả về thông báo lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Khách hàng '.$dataCustomer['name'] .' - '.$dataCustomer['code'] .' bị thiếu thông tin địa chỉ',
                'error_products' => $result['error_products']
            ]);
        }


        // Kiểm tra lỗi
        $result = $this->checkErrorsProperties($detail);

        if ($result['status'] === 'error') {
            // Lưu trạng thái lỗi vào alert
            Alert::create([
                'type' => 'Sản phẩm chưa có thuộc tính',
                'message' => 'Trong đơn hàng '.$code.' có  các sản phẩm chưa có thuộc tính ' . implode(', ', $result['error_products']),
                'solution' => 'Kiểm tra lại thuộc tính sản phẩm và cập nhật lại thông tin',
                'status' => 'New'
            ]);

            // Dừng công việc và trả về thông báo lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phầm chưa có thuộc tính',
                'error_products' => $result['error_products']
            ]);
        }

        // Tính tổng trọng lượng
        $totalWeight = $this->getTotalWeight($detail);

        // Convert 'd/m/Y' format to 'Y-m-d' for MySQL
        $date_sign = \DateTime::createFromFormat('d/m/Y', $dataAPI['date_sign']);
        if ($date_sign === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Định dạng ngày không hợp lệ'
            ], 400);
        }
        $convert_date_sign = $date_sign->format('Y-m-d');

        Log::info("Ghi chú: " . $dataAPI["cf20"] . $dataAPI["cf21"]);

        try {
            $orderData = [
                'code' => $dataAPI['code'],
                'date_sign' => $convert_date_sign,
                'customer_code' => $dataAPI['customer_code'],
                'customerUsername' => $dataAPI['customer_name'],
                'customerEmail' => $dataAPI['customer_emails'],
                'Address_Bill' => $this->convertAddress($dataCustomer),
                'Address_Ship' => $this->convertAddress($dataCustomer),
                'currency_unit' => $dataAPI['currency_unit'],
                'fee' => $dataAPI['fee'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['fee'], 2, '.', ''),
                'sale' => $dataAPI['sale'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['sale'], 2, '.', ''),
                'vat' => $dataAPI['vat'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['vat'], 2, '.', ''),
                'total_price' => number_format(0, 2, '.', ''), // Nếu bạn muốn total_price luôn là 0.00
                'orderStatus' => $dataAPI['cf9'],
                'desc' => $dataAPI['desc'],
                'detail' => $this->convertProducts($detail),
                'weight' => ["units" => "ounces", "value" => $totalWeight],
                'customerNotes' => $dataAPI["cf20"],
                'internalNotes' => $dataAPI["cf21"],
                'advanced_options_ShipSation' => $this->convertAdvancedOptions(),
                'id1OFFICE' => $dataAPI['ID'],
                'FullData' => $dataAPI,
            ];

            $order = $this->orderRepository->create($orderData);

            Log::info($phanhe . ' Tạo Mới thành công');
            return response()->json(['message' => $phanhe . ' Tạo mới thành công', 'order' => $order], 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lưu trữ ' . $phanhe . ': ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi xử lý yêu cầu lưu trữ ' . $phanhe, 'error' => $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $orders = $this->orderRepository->all();
        return response()->json($orders);
    }


    public function update($orderbyAPI, Order $order, $phanhe)
    {
        $dataAPI = $orderbyAPI['data']; //lấy data order từ API
        $dataCustomer = json_decode($this->getCustomerByCode($dataAPI['customer_code'],$phanhe)->getContent(), true)['data']; //lấy data khách hàng từ API
        $detail = $dataAPI['detail']; //danh sách sản phẩm trong order từ API
        $totalWeight = $this->getTotalWeight($detail); // tính totalWeight từ danh sách sản phẩm
        // Convert 'd/m/Y' format to 'Y-m-d' for MySQL
        $date_sign = \DateTime::createFromFormat('d/m/Y', $dataAPI['date_sign']);
        $convert_date_sign = $date_sign->format('Y-m-d');
        // Log::info("details: ", $detail);

        try {
            $orderData = [
                'code' => $dataAPI['code'],
                'date_sign' => $convert_date_sign,
                'customer_code' => $dataAPI['customer_code'],
                'customerUsername' => $dataAPI['customer_name'],
                'customerEmail' => $dataAPI['customer_emails'],
                'Address_Bill' => $this->convertAddress($dataCustomer),
                'Address_Ship' => $this->convertAddress($dataCustomer),
                'currency_unit' => $dataAPI['currency_unit'],
                'fee' => $dataAPI['fee'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['fee'], 2, '.', ''),
                'sale' => $dataAPI['sale'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['sale'], 2, '.', ''),
                'vat' => $dataAPI['vat'] === "" ? number_format(0, 2, '.', '') : number_format($dataAPI['vat'], 2, '.', ''),
                'total_price' => number_format(0, 2, '.', ''), // Nếu bạn muốn total_price luôn là 0.00
                'customerNotes' => $dataAPI["cf20"],
                'internalNotes' => $dataAPI["cf21"],
                'orderStatus' => $dataAPI['cf9'],
                'desc' => $dataAPI['desc'],
                'detail' => $this->convertProducts($detail),
                'weight' => ["units" => "ounces", "value" => $totalWeight],
                'advanced_options_ShipSation' => $this->convertAdvancedOptions(),
                'id1OFFICE' => $dataAPI['ID'],
                'FullData' => $dataAPI,
            ];
            $this->orderRepository->update($order, $orderData);
            return response()->json(['message' => ''.$phanhe.' được cập nhật thành công', 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi xử lý yêu cầu cập nhật '.$phanhe.'', 'error' => $e->getMessage()], 500);
        }
    }



    public function destroy(Order $order)
    {
        Log::info('Bắt đầu xóa khách hàng', ['order_id' => $order->id]);

        try {
            $result = $this->orderRepository->delete($order);

            if ($result) {
                Log::info('Khách hàng được xóa thành công', ['order_id' => $order->id]);
                return response()->json(['message' => 'Khách hàng được xóa thành công', 'order' => $order]);
            } else {
                Log::error('Không thể xóa khách hàng - phương thức xóa trả về false', ['order_id' => $order->id]);
                return response()->json(['message' => 'Không thể xóa khách hàng'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa khách hàng: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa khách hàng'], 500);
        }
    }

    private function createOrder($data)
    {
        $host = config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt đầu thêm thông tin khách hàng mới qua API ' . $nameapi . '.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();

        try {
            $url = $host . '/orders';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Thêm thông tin khách hàng mới thành công vào API của ' . $nameapi . '.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm thông tin khách hàng mới qua API ' . $nameapi . ': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi thêm thông tin khách hàng mới qua API ' . $nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    private function updateOrder($id, $data)
    {
        $host = config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt update thêm thông tin khách hàng qua API ' . $nameapi . '.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();
        Log::info($data);

        try {
            $url = $host . '/orders' . '/' . $id;

            $response = $client->put($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Update thông tin khách hàng mới thành công vào API của ' . $nameapi . '.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi update thông tin khách hàng mới vào API ' . $nameapi . ': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi update thông tin khách hàng mới vào API ' . $nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    private function deleteOrder($id)
    {
        $host = config('services.oauthapiendpointkiotviet.kiotviet_host');
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt delete thông tin khách hàng qua API ' . $nameapi . '.');

        // Lấy access token
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Không thể lấy access token.');
            return response()->json(['error' => 'Không thể lấy access token'], 500); // Mã trạng thái 500 cho biết lỗi máy chủ
        }

        $client = new Client();

        try {
            $url = $host . '/orders' . '/' . $id;

            $response = $client->delete($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => config('services.oauthapiendpointkiotviet.kiotviet_retailer'),
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Delete thông tin khách hàng thành công qua API của ' . $nameapi . '.');

            // Trả về phản hồi thành công với mã trạng thái 201 (Created)
            return response()->json($body, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi delete thông tin khách hàng qua API ' . $nameapi . ': ' . $e->getMessage());

            // Trả về phản hồi lỗi với mã trạng thái 500 (Internal Server Error)
            return response()->json(['error' => 'Lỗi khi delete thông tin khách hàng mới vào API ' . $nameapi, 'message' => $e->getMessage()], 500);
        }
    }

    public function createOrderKiotviet(Request $request)
    {
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Gọi hàm createOrder với dữ liệu nhận được
        $response = $this->createOrder($data);

        // Kiểm tra kết quả của hàm createOrder
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Tạo khách hàng mới thành công.',
                'data' => $response,
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API ' . $nameapi . '.',
                'details' => $response,
            ], $response->status());
        }
    }

    public function updateOrderKiotviet($id, Request $request)
    {
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        $data = $request->all(); // Lấy dữ liệu từ request

        // Gọi hàm createOrder với dữ liệu nhận được
        $response = $this->updateOrder($id, $data);

        // Kiểm tra kết quả của hàm createOrder
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Update khách hàng mới thành công qua API ' . $nameapi . '.',
                'data' => $response,
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình tạo khách hàng mới qua API ' . $nameapi . '.',
                'details' => $response,
            ], $response->status());
        }
    }
    public function deleteOrderKiotviet($id)
    {
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        // Gọi hàm createOrder với dữ liệu nhận được
        $response = $this->deleteOrder($id);

        // Kiểm tra kết quả của hàm createOrder
        if ($response->status() == 200) {
            // Nếu tạo khách hàng thành công
            return response()->json([
                'message' => 'Delete khách hàng mới thành công qua API ' . $nameapi . '.',
                'data' => $response,
            ], 200);
        } else {
            // Nếu tạo khách hàng không thành công
            return response()->json([
                'error' => 'Có lỗi xảy ra trong quá trình xoá khách hàng qua API ' . $nameapi . '.',
                'details' => $response,
            ], $response->status());
        }
    }

    private function getAccessToken_basic()
    {
        $apiname = config('services.oauthapiendpointshipstation.shipstation_apiname', '');
        $cacheKey = 'oauth_access_token_' . $apiname;
        $accessToken = Cache::get($cacheKey);
        if (Cache::has($cacheKey)) {
            Log::info('Lấy access token từ cache.');
            return $accessToken;
        } else {
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
        $cacheKey = 'oauth_access_token_' . $apiname;
        $accessToken = Cache::get($cacheKey);
        if (Cache::has($cacheKey)) {
            Log::info('Lấy access token từ cache.');
            return $accessToken;
        } else {
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

}
