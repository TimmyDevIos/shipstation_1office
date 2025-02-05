<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    // Lấy danh sách logs với phân trang
    public function index(Request $request)
{
    $audits = Audit::orderBy('created_at', 'desc')
                   ->orderBy('id', 'desc') // Sắp xếp theo id nếu thời gian trùng nhau
                   ->paginate(5);

    return response()->json($audits);
}

    // Tìm kiếm logs theo một số tiêu chí
    public function search(Request $request)
    {
        $query = Audit::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->has('context')) {
            $query->where('context', $request->input('context'));
        }

        if ($request->has('third_party_unit')) {
            $query->where('third_party_unit', $request->input('third_party_unit'));
        }

        $audits = Audit::orderBy('created_at', 'desc')
                   ->orderBy('id', 'desc') // Sắp xếp theo id nếu thời gian trùng nhau
                   ->paginate(5);

        return response()->json($audits);
    }
}
