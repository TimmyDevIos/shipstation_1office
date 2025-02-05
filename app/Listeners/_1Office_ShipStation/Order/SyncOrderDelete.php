<?php

namespace App\Listeners\_1Office_ShipStation\Order;

use App\Events\_1Office_ShipStation\Order\OrderDeleted;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class SyncOrderDelete implements ShouldQueue
{
    public function handle(OrderDeleted $event)
    {
        $host= config('services.apihub.apihub_host');
        $token= config('services.apihub.apihub_token');
        $nameapi= config('services.oauthapiendpointshipstation.shipstation_apiname');
        Log::info('Bắt đầu xử lý SyncOrderDeleteTo' . $nameapi . ' Listener.');
        $phanhe='Order';

        $client = new Client(['verify' => false]);
        $order = $event->orderInfo;
        Log::info('Thông tin '.$phanhe.':', $order);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];
        try {
            $response = $client->request('delete', $host . '/api/shipstation/order/' . $order['idthirdparty'], [
                'headers' => $headers,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                // Xử lý phản hồi từ API nếu cần
                Log::info('Xoá '.$phanhe.' qua API ' . $nameapi . ' thành công. ID '.$phanhe.': ' . $order['idthirdparty']);
                // Truy vấn bản ghi audit mới nhất cho khách hàng đã bị xoá
                $latestAudit = Audit::where('auditable_type', 'App\Models\Order')
                    ->where('auditable_id', $order['id']) // Sử dụng auditable_id từ orderInfo
                    ->where('event', 'deleted')
                    ->latest() // Sử dụng latest() để lấy bản ghi mới nhất
                    ->first();

                // Lấy thông tin old_values từ bản ghi audit mới nhất
                $oldValues = $latestAudit ? $latestAudit->old_values : [];

                // // Ghi lại audit log với old_values được lấy từ bản ghi mới nhất
                // Audit::create([
                //     'user_id' => auth()->id(),
                //     'event' => 'deleted',
                //     'auditable_id' => $order['id'], // Sử dụng id từ orderInfo
                //     'auditable_type' => 'App\Models\Order',
                //     'old_values' => $oldValues,
                //     'new_values' => [], // Không có new_values khi xoá
                //     'url' => request()->url(),
                //     'ip_address' => request()->ip(),
                //     'user_agent' => request()->header('User-Agent'),
                //     'context' => 'api',
                //     'third_party_unit' => $nameapi,
                // ]);
            } else {
                Log::error('Không thể xoá '.$phanhe.' qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API' . $nameapi . ': ' . $e->getMessage());
        }

        Log::info('Kết thúc xử lý SyncOrderDeleteTo' . $nameapi . ' Listener.');

    }
}
