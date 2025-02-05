<?php

namespace App\Services\_1Office;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use OwenIt\Auditing\Models\Audit;
use App\Repositories\_1Office_ShipStation\Order\OrderRepository;

class OrderService
{
    public function changeStatusOrder($id, $ordercode, $status, $statusAddress)
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
            $response = $client->request('PUT', $host . '/api/1office/order/status/' . $ordercode . '/' . $status .'/'. $statusAddress, [
                'headers' => $headers,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                Audit::create([
                    'user_id' => auth()->id(),
                    'event' => 'Cập nhật trạng thái đơn hàng lên 1Office',
                    'auditable_id' => $id, // Sử dụng id từ orderInfo
                    'auditable_type' => 'App\Models\Order',
                    'old_values' => [],
                    'new_values' => ['orderStatus' => $status], // Không có new_values khi xoá
                    'url' => request()->url(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'context' => 'api',
                    'third_party_unit' => 'ShipStation',
                    'actions' => 'Pull'
                ]);
                Log::info('Đã cập nhật trạng thái Order thành công. [' . $status . ']');
            } else {
                Log::error('Không thể cập nhật trạng thái đơn hàng qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API ' . $nameapi . ': ' . $e->getMessage());
        }
    }
    public function serviceUpdateStatusOrderInTransit($ordercode, $status, $trackingNumber, $rate)
    {
        $host = config('services.apihub.apihub_host');
        $token = config('services.apihub.apihub_token');
        $nameapi = config('services.oauthapiendpointshipstation.shipstation_apiname');

        $client = new Client(['verify' => false]);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        $data = [
            'ordercode' => $ordercode,
            'status' => $status,
            'trackingNumber' => $trackingNumber,
            'rate' => $rate,
        ];

        try {
            Log::info("bắt đầu chạy: ".$host . '/api/1office/order/status/statusintransit/' );
            $response = $client->request('PUT', $host . '/api/1office/order/status/updatestatusintransit', [
                'headers' => $headers,
                'verify' => false,
                'json' => $data
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $order = Order::where("code",$ordercode)->first();

                $order->update([
                    'orderStatus' => $status,
                    'rate' => $rate,
                ]);
                Audit::create([
                    'user_id' => auth()->id(),
                    'event' => 'Cập nhật trạng thái đơn hàng lên 1Office',
                    'auditable_id' => $order->id, // Sử dụng id từ orderInfo
                    'auditable_type' => 'App\Models\Order',
                    'old_values' => [
                        'orderStatus' => 'Label Created',
                        'rate' => ''
                    ],
                    'new_values' => [
                        'orderStatus' => $status,
                        'rate' => $rate
                    ], // Không có new_values khi xoá
                    'url' => request()->url(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'context' => 'api',
                    'third_party_unit' => 'ShipStation',
                    'actions' => 'Pull'
                ]);
                Log::info('Đã cập nhật trạng thái Order thành công. [' . $status . ']');
            } else {
                Log::error('Không thể cập nhật trạng thái đơn hàng qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API ' . $nameapi . ': ' . $e->getMessage());
        }
    }

    public function changeStatusAddressCustomer($code, $status, $orderid){
        $host= config('services.apihub.apihub_host');
        $token= config('services.apihub.apihub_token');
        $nameapi= config('services.oauthapiendpointshipstation.shipstation_apiname');

        $client = new Client(['verify' => false]);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];

        try {
            $response = $client->request('PUT', $host.'/api/1office/customer/statusaddress/'.$code.'/'.$status, [
                'headers' => $headers,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                // Ghi lại audit log với context là "api" và third_party_unit là "mockup API"

                Audit::create([
                    'user_id' => auth()->id(),
                    'event' => 'Cập nhật trạng thái xác thực địa chỉ',
                    'auditable_id' => $orderid, // Sử dụng id từ orderInfo
                    'auditable_type' => 'App\Models\Order',
                    'old_values' => [],
                    'new_values' => ['addressVerified' => $status], // Không có new_values khi xoá
                    'url' => request()->url(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'context' => 'api',
                    'third_party_unit' => 'ShipStation',
                    'actions' => 'Pull'
                ]);
                Log::info('Đã cập nhật trạng thái xác thực địa chỉ khách hàng thành công. ['.$status.']');
            } else {
                Log::error('Không thể cập nhật trạng thái xác thực địa chỉ khách hàng qua API '.$nameapi.': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API '.$nameapi.': ' . $e->getMessage());
        }
    }
}
