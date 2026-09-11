<?php

namespace App\Http\Controllers;

use App\Models\MatchingMaster;
use Illuminate\Http\Request;

class MatchingMasterController extends Controller
{
    public function index()
    {
        return view('admin.masters.index', ['masters' => MatchingMaster::orderBy('type')->orderBy('sort_order')->get()->groupBy('type')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['type' => ['required', 'in:sport,field,job_type,target_age,request_style'], 'value' => ['required', 'max:255'], 'sort_order' => ['nullable', 'integer', 'min:0']]);
        MatchingMaster::create($data);
        return back()->with('status', 'マスタ項目を追加しました。');
    }

    public function update(Request $request, MatchingMaster $master)
    {
        $data = $request->validate(['value' => ['required', 'max:255'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean']]);
        $data['is_active'] = $request->boolean('is_active');
        $master->update($data);
        return back()->with('status', 'マスタ項目を更新しました。');
    }
}
