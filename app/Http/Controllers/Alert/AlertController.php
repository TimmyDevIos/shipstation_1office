<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $alerts = Alert::all();
        return view('admin.alert.index', compact('alerts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'message' => 'required|string',
            'solution' => 'required|string',
            'status' => 'required|string|max:255',
        ]);

        Alert::create($request->all());

        return redirect()->route('alerts.index')->with('success', 'Alert created successfully.');
    }

    public function show(Alert $alert)
    {
        return view('alerts.show', compact('alert'));
    }


    public function update($id, $status)
    {
        Log::info("Raw status from URL: [" . $status . "]");

        $status = urldecode($status); // Giải mã trạng thái từ URL
        $status = trim($status); // Loại bỏ khoảng trắng trước và sau
        Log::info("Decoded status: [" . $status . "]");

        $alert = Alert::find($id);

        if (!$alert) {
            Log::error("Alert with ID $id not found.");
            return redirect()->route('alerts.index')->with('error', 'Alert không tồn tại.');
        }

        $validStatuses = ['New', 'Đã xử lý', 'Mở lại', 'Bỏ qua']; // Sửa lỗi chính tả nếu cần

        if (in_array($status, $validStatuses)) {
            $alert->update([
                'status' => $status,
            ]);
            Log::info("Alert with ID $id updated to status: [$status].");
            return redirect()->route('alerts.index')->with('success', 'Đã cập nhật trạng thái thành ['.$status.'].');
        }

        Log::error("Invalid status: [$status].");
        return redirect()->route('alerts.index')->with('error', 'Trạng thái không hợp lệ.');
    }






    public function destroy(Alert $alert)
    {
        $alert->delete();

        return redirect()->route('alerts.index')->with('success', 'Alert deleted successfully.');
    }
}
