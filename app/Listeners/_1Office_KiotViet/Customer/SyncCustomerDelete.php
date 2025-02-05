<?php

namespace App\Listeners\_1Office_KiotViet\Customer;

use App\Events\_1Office_KiotViet\Customer\CustomerDeleted;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class SyncCustomerDelete implements ShouldQueue
{
    public function handle(CustomerDeleted $event)
    {
        $host = config('services.apihub.apihub_host');
        $token = config('services.apihub.apihub_token');
        $branchId = config('services.oauthapiendpointkiotviet.kiotviet_branchId');
        $nameapi = config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt đầu xử lý SyncCustomerUpdateTo' . $nameapi . ' Listener.');

        $client = new Client(['verify' => false]);
        $customer = $event->customerInfo;
        Log::info('Thông tin khách hàng:', $customer);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        try {
            $response = $client->request('delete', $host . '/api/KiotViet/customer/' . $customer['idthirdparty'], [
                'headers' => $headers,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                // Xử lý phản hồi từ API nếu cần
                Log::info('Xoá khách hàng qua API ' . $nameapi . ' thành công. ID khách hàng: ' . $customer['idthirdparty']);
                // Truy vấn bản ghi audit mới nhất cho khách hàng đã bị xoá
                $latestAudit = Audit::where('auditable_type', 'App\Models\Customer')
                    ->where('auditable_id', $customer['id']) // Sử dụng auditable_id từ customerInfo
                    ->where('event', 'deleted')
                    ->latest() // Sử dụng latest() để lấy bản ghi mới nhất
                    ->first();

                // Lấy thông tin old_values từ bản ghi audit mới nhất
                $oldValues = $latestAudit ? $latestAudit->old_values : [];

                // Ghi lại audit log với old_values được lấy từ bản ghi mới nhất
                Audit::create([
                    'user_id' => auth()->id(),
                    'event' => 'deleted',
                    'auditable_id' => $customer['id'], // Sử dụng id từ customerInfo
                    'auditable_type' => 'App\Models\Customer',
                    'old_values' => $oldValues,
                    'new_values' => [], // Không có new_values khi xoá
                    'url' => request()->url(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'context' => 'api',
                    'third_party_unit' => config('services.apihub.apihub_apiname'),
                ]);
            } else {
                Log::error('Không thể xoá khách hàng qua API ' . $nameapi . ': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API' . $nameapi . ': ' . $e->getMessage());
        }

        Log::info('Kết thúc xử lý SyncCustomerDeleteTo' . $nameapi . ' Listener.');

    }
}
