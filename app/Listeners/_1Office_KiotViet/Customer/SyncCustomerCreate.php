<?php

namespace App\Listeners\_1Office_KiotViet\Customer;

use App\Events\_1Office_KiotViet\Customer\CustomerCreated;
use App\Services\OAuthClientCredentialsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

// Sau khi thêm Listeners mới thì vào EventServiceProvider.php để cập nhật

class SyncCustomerCreate implements ShouldQueue
{
    protected $oauthService;

    public function __construct(OAuthClientCredentialsService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function handle(CustomerCreated $event)
    {
        $host= config('services.apihub.apihub_host');
        $token= config('services.apihub.apihub_token');
        $branchId =config('services.oauthapiendpointkiotviet.kiotviet_branchId');
        $nameapi= config('services.oauthapiendpointkiotviet.kiotviet_apiname');
        Log::info('Bắt đầu xử lý SyncCustomerCreateTo'.$nameapi.' Listener.');
        $client = new Client(['verify' => false]);
        $customer = $event->customer;
        // Log::info('Dữ liệu khách hàng:', $customer->toArray());

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];

        $body = [
            "name" => $customer->name,
            "gender" => $customer->gender,
            "birthDate" => $customer->birthday,
            "contactNumber" => $customer->phones,
            "address" => $customer->address,
            "branchId" => $branchId,
            "email" => $customer->emails,
            "type" => $customer->FullData['type'],
        ];

        try {
            $response = $client->request('POST', $host.'/api/KiotViet/customers', [
                'headers' => $headers,
                'json' => $body,
                'verify' => false,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $thirdpartyCustomer = json_decode($response->getBody()->getContents(), true);
                $thirdpartyCustomerId = $thirdpartyCustomer['details']['original']['data']['id'];
                $customer->update(['idthirdparty' => $thirdpartyCustomerId]);
                Log::info('Tạo khách hàng qua API '.$nameapi.' thành công. ID khách hàng: ' . $thirdpartyCustomerId);
                // Ghi lại audit log với context là "api" và third_party_unit là "mockup API"
                Audit::create([
                    'user_id' => auth()->id(),
                    'event' => 'created',
                    'auditable_id' => $customer->id,
                    'auditable_type' => get_class($customer),
                    'old_values' => [],
                    'new_values' => $customer->toArray(),
                    'url' => request()->url(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'context' => 'api',
                    'third_party_unit' => config('services.apihub.apihub_apiname'),
                ]);
            } else {
                Log::error('Không thể tạo khách hàng qua API '.$nameapi.': ' . $response->getStatusCode() . ' - ' . $response->getBody());
            }
        } catch (RequestException $e) {
            Log::error('Lỗi khi gửi yêu cầu đến API '.$nameapi.': ' . $e->getMessage());
        }

        Log::info('Kết thúc xử lý SyncCustomerCreateTo'.$nameapi.' Listener.');
    }
}