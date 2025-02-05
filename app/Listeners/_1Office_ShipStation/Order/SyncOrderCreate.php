<?php

namespace App\Listeners\_1Office_ShipStation\Order;

use App\Events\_1Office_ShipStation\Order\OrderCreated;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\Alert;
use App\Services\_1Office\OrderService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class SyncOrderCreate implements ShouldQueue
{
    protected $oauthService;
    protected $orderService;

    public function __construct(OAuthClientCredentialsService $oauthService, OrderService $orderService)
    {
        $this->oauthService = $oauthService;
        $this->orderService = $orderService;
    }

    public function handle(OrderCreated $event)
    {
//        $host = config('services.apihub.apihub_host');
        $host = "https://core.apihub.cloud";
//        $token = config('services.apihub.apihub_token');
        $token = "UBdyoMAKHWUrSkvVAqu7U9QcF4CArTsjOUY0IH06915f86d4";
        $nameapi = config('services.oauthapiendpointshipstation.shipstation_apiname');
        $phanhe = 'Order';
        $client = new Client(['verify' => false]);
        $order = $event->order;
        $detail = $this->updateUnitPricesToZero($order->detail);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        $body = [
            "orderNumber" => $order->code,
            "orderDate" => $order->date_sign,
            "orderStatus" => "awaiting_shipment",
            "customerUsername" => $order->customerEmail,
            "customerEmail" => $order->customerEmail,
            "customerNotes" => $order->customerNotes,
            "internalNotes" => $order->internalNotes,
            "billTo" => $order->Address_Bill,
            "shipTo" => $order->Address_Ship,
            "items" => $detail, // Sử dụng $detail là một mảng PHP, không cần json_encode
            "weight" => $order->weight,
            "advancedOptions" => $order->advanced_options_ShipSation,
        ];

        try {
            $response = $client->request('POST', $host . '/api/shipstation/order', [
                'headers' => $headers,
                'json' => $body,
                'verify' => false,
            ]);
            Log::info("Sử dụng host: ". $host);
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $thirdpartyOrder = json_decode($response->getBody()->getContents(), true);
                $thirdpartyOrderId = $thirdpartyOrder['data']['original']['orderId'];
                $thirdpartyOrderKey = $thirdpartyOrder['data']['original']['orderKey'];
                $thirdpartyStatusAddress = $thirdpartyOrder['data']['original']['shipTo']['addressVerified'];
                $order->update([
                    'idthirdparty' => $thirdpartyOrderId,
                    'orderKeyShipSation' => $thirdpartyOrderKey,
                    'orderStatus' => 'Label Created',
                    'Address_Ship' => $thirdpartyOrder['data']['original']['shipTo'],
                ]);
                $this->orderService->changeStatusAddressCustomer($order->customer_code, $thirdpartyStatusAddress, $order['id']);

                //Update Status and Full Address lên 1Office
                $this->orderService->changeStatusOrder($order['id'], $order['code'], "Label Created", $thirdpartyStatusAddress);

                Log::info('Tạo ' . $phanhe . ' qua API ' . $nameapi . ' thành công. ID ' . $phanhe . ': ' . $thirdpartyOrderId);
            } else {
                Log::error('Không thể tạo ' . $phanhe . ' qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Alert::created([
                'type' => 'Gặp lỗi khi tạo đơn hàng trên Shipstation ',
                'message' =>$e->getMessage(),
                'solution' => 'Chuyển trạng thái đơn hàng về "succsec Payment" cập nhật lại thông tin và gửi lại',
                'status' => "New"
            ]);
            $order = Order::where('code', $order->code)->first();
            if ($order) {
                $order->delete();
            }
            Log::error('Lỗi khi gửi yêu cầu đến API ' . $nameapi . ': ' . $e->getMessage());
        }

        Log::info('Kết thúc xử lý SyncOrderCreateTo' . $nameapi . ' Listener.');
    }

    private function updateUnitPricesToZero($detailJson)
    {
        // Nếu detailJson là một mảng, chuyển đổi nó thành JSON để xử lý
        if (is_array($detailJson)) {
            $detailJson = json_encode($detailJson);
        }

        // Giải mã JSON chi tiết đơn hàng thành mảng PHP
        $detailArray = json_decode($detailJson, true);

        // Lặp qua tất cả các sản phẩm và cập nhật giá trị unitPrice thành 0
        foreach ($detailArray as &$product) {
            $product['unitPrice'] = 0;
        }

        // Trả về mảng PHP đã được cập nhật giá trị unitPrice
        return $detailArray;
    }
}
