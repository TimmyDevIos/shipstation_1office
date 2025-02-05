<?php

namespace App\Repositories\_1Office_ShipStation\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Events\_1Office_ShipStation\Order\OrderCreated;
use App\Events\_1Office_ShipStation\Order\OrderUpdated;
use App\Events\_1Office_ShipStation\Order\OrderDeleted;

class OrderRepository
{
    public function findByCode($code)
    {
        Log::info('Đang tìm kiếm khách hàng bằng mã: ' . $code);
        return Order::where('code', $code)->first();
    }

    public function create(array $data)
    {
        try {
            $order = Order::create($data);

            // Kích hoạt sự kiện OrderCreated
            event(new OrderCreated($order));

            // Xuất log thông báo về việc tạo khách hàng thành công
            Log::info('Khách hàng được tạo thành công: ' . $order->code);

            return $order;
        } catch (\Exception $e) {
            // Xuất log thông báo về lỗi tạo khách hàng
            Log::error('Lỗi khi tạo khách hàng: ' . $e->getMessage());

            // Xử lý nếu có lỗi xảy ra trong quá trình tạo
            throw new \Exception("Lỗi khi tạo khách hàng: " . $e->getMessage());
        }
    }

    public function update(Order $order, array $data)
    {
        Log::info('Data : ' , [$data]);
        try {
            $order->update($data);

            // Kích hoạt sự kiện OrderUpdated
            event(new OrderUpdated($order));

            // Xuất log thông báo về việc cập nhật khách hàng thành công
            Log::info('Khách hàng được cập nhật thành công: ' . $order->code);

            return $order;
        } catch (\Exception $e) {
            // Xuất log thông báo về lỗi cập nhật khách hàng
            Log::error('Lỗi khi cập nhật khách hàng: ' . $e->getMessage());

            // Xử lý nếu có lỗi xảy ra trong quá trình cập nhật
            throw new \Exception("Lỗi khi cập nhật khách hàng: " . $e->getMessage());
        }
    }

    public function delete(Order $order)
    {
        try {
            if ($order->exists) {
                // Lưu thông tin khách hàng vào mảng trước khi xoá
                $orderInfo = $order->toArray();
                $orderCode = $order->code;

                // Xoá khách hàng
                $order->delete();

                // Log thông báo xoá khách hàng thành công sử dụng thông tin đã lưu trước đó
                Log::info('Khách hàng được xoá thành công: ' . $orderCode);

                // Kích hoạt sự kiện OrderDeleted sử dụng mảng thông tin
                event(new OrderDeleted($orderInfo));

                return true;
            } else {
                Log::error('Khách hàng không tồn tại: ' . $order->code);
                return false;
            }
        } catch (\Exception $e) {
            // Log thông báo lỗi và trả về false
            Log::error('Lỗi khi xoá khách hàng: ' . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        return Order::all();
    }
}