<?php

use App\Http\Controllers\APIendpoints\_1Office_KiotViet\_1OfficeToKiotVietController;
use App\Http\Controllers\APIendpoints\_1Office_ShipStation\_1OfficeToShipStationController;
use App\Http\Controllers\APIendpoints\_1Office\_1OfficeController;
use App\Http\Controllers\APIendpoints\ShipStation\ShipStationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIendpoints\Webhooks\WebhookController;

Route::post('/getwebhook', [WebhookController::class, 'getWebhook']);

Route::prefix('webhook')->group(function () {
        Route::prefix('1office')->group(function () {
            // Webhook Customer từ 1Office đến ShipStation
            Route::prefix('shipstation')->group(function () {
                Route::post('/SHIP_NOTIFY', [_1OfficeToShipStationController::class, 'SHIP_NOTIFY_Webhook'])->name("shipstation.SHIP_NOTIFY");
                Route::post('/order', [_1OfficeToShipStationController::class, 'handleWebhook']);
            });
        });
    });

// ================================================
// Routes có yêu cầu xác thực (Authentication required)
// ================================================
Route::middleware('auth:sanctum')->group(function () {
//
//    Route::prefix('webhook')->group(function () {
//        Route::prefix('1office')->group(function () {
//            // Webhook Customer từ 1Office đến ShipStation
//            Route::prefix('shipstation')->group(function () {
////                Route::post('/SHIP_NOTIFY', [_1OfficeToShipStationController::class, 'SHIP_NOTIFY_Webhook'])->name("shipstation.SHIP_NOTIFY");
////                Route::post('/order', [_1OfficeToShipStationController::class, 'handleWebhook'])->name("handleWebhook");
//            });
//        });
//    });
    // --------------------------------------------
    // Routes liên quan đến Webhook
    // --------------------------------------------


    // --------------------------------------------
    // Routes nội bộ (Internal API) cho KiotViet
    // --------------------------------------------
    Route::prefix('kiotviet')->group(function () {

        Route::post('/customers', [_1OfficeToKiotVietController::class, 'createCustomerKiotviet']);
        // Tạo mới khách hàng trên KiotViet

        Route::put('/customer/{id}', [_1OfficeToKiotVietController::class, 'updateCustomerKiotviet']);
        // Cập nhật thông tin khách hàng trên KiotViet theo ID

        Route::delete('/customer/{id}', [_1OfficeToKiotVietController::class, 'deleteCustomerKiotviet']);
        // Xoá khách hàng trên KiotViet theo ID

        Route::get('/categories', [_1OfficeToKiotVietController::class, 'getCategoryList']);
        // Lấy danh sách danh mục sản phẩm trên KiotViet

        Route::get('/customers', [_1OfficeToKiotVietController::class, 'getCustomersList']);
        // Lấy danh sách khách hàng trên KiotViet

        Route::get('/customers/{id}', [_1OfficeToKiotVietController::class, 'getCustomerId']);
        // Lấy thông tin chi tiết khách hàng theo ID trên KiotViet

        Route::get('/customers/code/{code}', [_1OfficeToKiotVietController::class, 'getCustomerCode']);
        // Lấy thông tin chi tiết khách hàng theo mã khách hàng trên KiotViet
    });

    // --------------------------------------------
    // Routes nội bộ (Internal API) cho 1Office
    // --------------------------------------------
    Route::prefix('1office')->group(function () {

        // --------------------------------------------
        // Routes KHÁCH HÀNG 1Office
        // --------------------------------------------
        Route::post('/customer', [_1OfficeController::class, 'createCustomer1Office']);
        // Tạo mới khách hàng trên 1Office

        Route::put('/customer/{id}', [_1OfficeController::class, 'updateCustomerKiotviet']);
        // Cập nhật thông tin khách hàng trên 1Office theo ID

        Route::delete('/customer/{id}', [_1OfficeController::class, 'deleteCustomerKiotviet']);
        // Xoá khách hàng trên 1Office theo ID

        Route::get('/customers', [_1OfficeController::class, 'getCustomersList']);
        // Lấy danh sách khách hàng trên 1Office

        Route::get('/customer/{code}', [_1OfficeController::class, 'getCustomerCode']);
        // Lấy thông tin chi tiết khách hàng theo code trên 1Office

        Route::put('/customer/statusaddress/{code}/{status?}', [_1OfficeController::class, 'updateStatusAddressVerificationCustomer']);
        // Cập nhật thông tin khách hàng trên 1Office theo ID



        // --------------------------------------------
        // Routes ĐƠN HÀNG 1Office
        // --------------------------------------------

        Route::post('/order', [_1OfficeController::class, 'createCustomer1Office']);
        // Tạo mới ĐƠN HÀNG trên 1Office

        Route::put('/order/{code}', [_1OfficeController::class, 'updateCustomer1Office']);
        // Cập nhật thông tin ĐƠN HÀNG trên 1Office theo ID

        // Thêm tham số vào URL (ví dụ thêm {status})
        Route::put('/order/status/{code}/{status}/{statusAddress}', [_1OfficeController::class, 'updateStatusOrder']);
        Route::put('/order/status/updatestatusintransit', [_1OfficeController::class, 'updateStatusOrderInTransit'])->name("1office.order.updatestatusintransit");

        // Cập nhật thông tin ĐƠN HÀNG trên 1Office theo ID

        Route::delete('/order/{code}', [_1OfficeController::class, 'deleteCustomer1Office']);
        // Xoá ĐƠN HÀNG trên 1Office theo ID

        Route::get('/orders', [_1OfficeController::class, 'getOrdersList']);
        // Lấy danh sách ĐƠN HÀNG trên 1Office

        Route::get('/order/{code}', [_1OfficeController::class, 'getOrderCode']);
        // Lấy thông tin chi tiết ĐƠN HÀNG theo code trên 1Office

        // --------------------------------------------
        // Routes SẢN PHẨM 1Office
        // --------------------------------------------

        Route::post('/product', [_1OfficeController::class, 'createProduct1Office']);
        // Tạo mới SẢN PHẨM trên 1Office

        Route::put('/product/{code}', [_1OfficeController::class, 'updateProduct1Office']);
        // Cập nhật thông tin SẢN PHẨM trên 1Office theo ID

        Route::delete('/product/{code}', [_1OfficeController::class, 'deleteProduct1Office']);
        // Xoá SẢN PHẨM trên 1Office theo ID

        Route::get('/products', [_1OfficeController::class, 'getProductsList']);
        // Lấy danh sách SẢN PHẨM trên 1Office

        Route::get('/product/{code}', [_1OfficeController::class, 'getProductCode']);
        // Lấy thông tin chi tiết SẢN PHẨM theo code trên 1Office

    });

    // --------------------------------------------
    // Routes nội bộ (Internal API) cho ShipStation
    // --------------------------------------------
    Route::prefix('shipstation')->group(function () {

        // --------------------------------------------
        // Routes KHÁCH HÀNG ShipStation
        // --------------------------------------------
        Route::post('/customer', [ShipStationController::class, 'createCustomerKiotviet']);
        // Tạo mới khách hàng trên ShipStation

        Route::put('/customer/{id}', [ShipStationController::class, 'updateCustomerKiotviet']);
        // Cập nhật thông tin khách hàng trên ShipStation theo ID

        Route::delete('/customer/{id}', [ShipStationController::class, 'deleteCustomerKiotviet']);
        // Xoá khách hàng trên ShipStation theo ID

        Route::get('/customers', [ShipStationController::class, 'getCustomersList']);
        // Lấy danh sách khách hàng trên ShipStation

        Route::get('/customer/{code}', [ShipStationController::class, 'getCustomerCode']);
        // Lấy thông tin chi tiết khách hàng theo code trên ShipStation


        // --------------------------------------------
        // Routes ĐƠN HÀNG ShipStation
        // --------------------------------------------

        Route::post('/order', [ShipStationController::class, 'createOrderShipStation']);
        // Tạo mới ĐƠN HÀNG trên ShipStation

        Route::put('/order/{code}', [ShipStationController::class, 'updateCustomerShipStation']);
        // Cập nhật thông tin ĐƠN HÀNG trên ShipStation theo ID

        Route::delete('/order/{code}', [ShipStationController::class, 'deleteOrderShipStation']);
        // Xoá ĐƠN HÀNG trên ShipStation theo ID

        Route::post('/order/deletelist', [ShipStationController::class, 'deletelist']);
        // Tạo mới ĐƠN HÀNG trên ShipStation

        Route::get('/orders', [ShipStationController::class, 'getOrdersList']);
        // Lấy danh sách ĐƠN HÀNG trên ShipStation

        Route::get('/order/{code}', [ShipStationController::class, 'getOrderCode']);
        // Lấy thông tin chi tiết ĐƠN HÀNG theo code trên ShipStation


        // --------------------------------------------
        // Routes SẢN PHẨM ShipStation
        // --------------------------------------------

        Route::post('/product', [ShipStationController::class, 'createProductShipStation']);
        // Tạo mới SẢN PHẨM trên ShipStation

        Route::put('/product/{code}', [ShipStationController::class, 'updateProductShipStation']);
        // Cập nhật thông tin SẢN PHẨM trên ShipStation theo ID

        Route::delete('/product/{code}', [ShipStationController::class, 'deleteProductShipStation']);
        // Xoá SẢN PHẨM trên ShipStation theo ID

        Route::get('/products', [ShipStationController::class, 'getProductsList']);
        // Lấy danh sách SẢN PHẨM trên ShipStation

        Route::get('/product/{code}', [ShipStationController::class, 'getProductCode']);
        // Lấy thông tin chi tiết SẢN PHẨM theo code trên ShipStation

    });

    // --------------------------------------------
    // Routes liên quan đến Audit Logs
    // --------------------------------------------
    Route::prefix('audit')->group(function () {

        Route::get('/logs', [AuditLogController::class, 'index']);
        // Lấy danh sách logs

        Route::get('/logs/search', [AuditLogController::class, 'search']);
        // Tìm kiếm logs
    });

    // --------------------------------------------
    // Các routes khác
    // --------------------------------------------
    Route::get('user', [AuthController::class, 'user']);
    // Lấy thông tin người dùng hiện tại

});
