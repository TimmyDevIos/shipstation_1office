<?php

namespace App\Listeners\_1Office_ShipStation\Order;

use App\Events\_1Office_ShipStation\Order\OrderUpdated;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class SyncOrderUpdate implements ShouldQueue
{
    public function handle(OrderUpdated $event)
    {
        $host= config('services.apihub.apihub_host');
        $token= config('services.apihub.apihub_token');
        $nameapi= config('services.oauthapiendpointshipstation.shipstation_apiname');
        Log::info('Bắt đầu xử lý SyncOrderUpdatedTo'.$nameapi.' Listener.');
        $phanhe='Order';
        $client = new Client(['verify' => false]);
        $order = $event->order;
        $detail = $this->updateUnitPricesToZero($order->detail);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];

        $body = [
            // "orderId" => $order->idthirdparty,
            "orderNumber" => $order->code,
            "orderKey" => $order->orderKeyShipSation,
            "orderDate"=> $order->date_sign,
            "orderStatus" => ($order->orderStatus === 'Success Payment' || $order->orderStatus === 'Label Created' ) ? "awaiting_shipment" : $order->orderStatus,
            "customerUsername" => $order->customerEmail,
            "customerEmail" => $order->customerEmail,
            "customerNotes" => $order->customerNotes,
            "internalNotes" => $order->internalNotes,
            "billTo" => $order->Address_Bill,
            "shipTo" => $order->Address_Ship,
            "items" => $detail,
            "weight" => $order->weight,
            "advancedOptions"=>$order->advanced_options_ShipSation,
        ];

        // Log::info("body: ", $body);

        try {
            $response = $client->request('POST', $host.'/api/shipstation/order', [
                'headers' => $headers,
                'json' => $body,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                Log::info('Cập nhật thành công '.$phanhe.' qua API '.$nameapi);
            } else {
                Log::error('Không thể tạo '.$phanhe.' qua API '.$nameapi.': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API '.$nameapi.': ' . $e->getMessage());
        }

        Log::info('Kết thúc xử lý SyncOrderCreateTo'.$nameapi.' Listener.');
    }

    private function updateUnitPricesToZero($detailJson) {
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

        // Chuyển đổi lại thành JSON và trả về
        return $detailArray;
    }
}
