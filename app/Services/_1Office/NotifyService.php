<?php

namespace App\Services\_1Office;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class NotifyService
{
    public function pushNotify1Office($title, $desc, $email_users)
    {
        // Lấy thông tin cấu hình từ config
        $host = config('services.oauthapiendpoint1office.1office_host_api_notify');

        // Chuẩn bị dữ liệu để gửi qua API
        $body = [
            "title" => $title,
            "desc" => $desc,
            "email_users" => $email_users,
        ];

        // Log thông tin trước khi gửi yêu cầu tới API

        $client = new Client();

        try {
            // Xây dựng URL với các tham số query
//            $url = $host . '?' . http_build_query($body);
            $url = "https://luciferblack.1office.vn/bpa-webhook/e6b3a7a5-884d-42d0-b75c-38489fde4575?title=Test Thông Báo&desc=Thông báo này dành cho người dùng cá nhân3&email_users=trianvuong@gmail.com";

            Log::info("url: ". $url);

            // Gửi yêu cầu POST tới API
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Đọc và log phản hồi từ API
            $responseBody = json_decode((string) $response->getBody(), true);

            // Trả về phản hồi thành công
            return response()->json($responseBody, 201);

        } catch (\Exception $e) {
            // Trả về phản hồi lỗi
            Log::error('Lỗi khi gửi yêu cầu đến API '. $e->getMessage());
            return response()->json(['error' => 'Lỗi khi cập nhật trạng thái xác thực địa chỉ khách hàng', 'message' => $e->getMessage()], 500);
        }
    }
}
