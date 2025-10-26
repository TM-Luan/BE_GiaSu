<?php

namespace App\Http\Controllers;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;
use App\Http\Resources\NguoiHocResources;
use App\Http\Requests\NguoiHocRequest;

class NguoiHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $nh = NguoiHoc::with('taiKhoan')->get();
        return NguoiHocResources::collection($nh);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nh = NguoiHoc::create($request->validated());
        return response()->json([
            'message' => 'Tạo người học thành công!',
            'data' => new NguoiHocResources($nh)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nh = NguoiHoc::with('taiKhoan')->findOrFail($id);
        return new NguoiHocResources($nh);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $nh = NguoiHoc::findOrFail($id);
        $nh->update($request->validated());
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => new NguoiHocResources($nh)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
                $nh = NguoiHoc::findOrFail($id);
        $nh->delete();

        return response()->json(['message' => 'Đã xóa người học.']);
    }
}
