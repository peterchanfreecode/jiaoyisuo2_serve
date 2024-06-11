<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\DailyReport;
class DailyReportController extends Controller
{
    public function index()
    {
        return view('admin.daily_report.index');
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $result = new DailyReport();
        $result = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }
}
