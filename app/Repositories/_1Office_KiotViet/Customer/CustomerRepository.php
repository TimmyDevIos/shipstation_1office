<?php

namespace App\Repositories\_1Office_KiotViet\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use App\Events\_1Office_KiotViet\Customer\CustomerCreated;
use App\Events\_1Office_KiotViet\Customer\CustomerUpdated;
use App\Events\_1Office_KiotViet\Customer\CustomerDeleted;

class CustomerRepository
{
    public function findByCode($code)
    {
        Log::info('Đang tìm kiếm khách hàng bằng mã: ' . $code);
        return Customer::where('code', $code)->first();
    }

    public function create(array $data)
    {
        try {
            $customer = Customer::create($data);

            // Kích hoạt sự kiện CustomerCreated
            event(new CustomerCreated($customer));

            // Xuất log thông báo về việc tạo khách hàng thành công
            Log::info('Khách hàng được tạo thành công: ' . $customer->code);

            return $customer;
        } catch (\Exception $e) {
            // Xuất log thông báo về lỗi tạo khách hàng
            Log::error('Lỗi khi tạo khách hàng: ' . $e->getMessage());

            // Xử lý nếu có lỗi xảy ra trong quá trình tạo
            throw new \Exception("Lỗi khi tạo khách hàng: " . $e->getMessage());
        }
    }

    public function update(Customer $customer, array $data)
    {
        try {
            $customer->update($data);

            // Kích hoạt sự kiện CustomerUpdated
            event(new CustomerUpdated($customer));

            // Xuất log thông báo về việc cập nhật khách hàng thành công
            Log::info('Khách hàng được cập nhật thành công: ' . $customer->code);

            return $customer;
        } catch (\Exception $e) {
            // Xuất log thông báo về lỗi cập nhật khách hàng
            Log::error('Lỗi khi cập nhật khách hàng: ' . $e->getMessage());

            // Xử lý nếu có lỗi xảy ra trong quá trình cập nhật
            throw new \Exception("Lỗi khi cập nhật khách hàng: " . $e->getMessage());
        }
    }

    public function delete(Customer $customer)
    {
        try {
            if ($customer->exists) {
                // Lưu thông tin khách hàng vào mảng trước khi xoá
                $customerInfo = $customer->toArray();
                $customerCode = $customer->code;

                // Xoá khách hàng
                $customer->delete();

                // Log thông báo xoá khách hàng thành công sử dụng thông tin đã lưu trước đó
                Log::info('Khách hàng được xoá thành công: ' . $customerCode);

                // Kích hoạt sự kiện CustomerDeleted sử dụng mảng thông tin
                event(new CustomerDeleted($customerInfo));

                return true;
            } else {
                Log::error('Khách hàng không tồn tại: ' . $customer->code);
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
        return Customer::all();
    }
}