<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiaSuRequest;
use App\Http\Resources\GiaSuResource;
use App\Models\GiaSu;
use Illuminate\Http\Request;

class GiaSuController extends Controller
{
    public function index()
    {
        $tutors = GiaSu::with('taiKhoan')->get();
        return GiaSuResource::collection($tutors);
    }

    public function show($id)
    {
        $tutor = GiaSu::with('taiKhoan')->findOrFail($id);
        return new GiaSuResource($tutor);
    }

    public function store(GiaSuRequest $request)
    {
        $tutor = GiaSu::create($request->validated());
        return response()->json([
            'message' => 'Tạo gia sư thành công!',
            'data' => new GiaSuResource($tutor)
        ], 201);
    }

    public function update(GiaSuRequest $request, $id)
    {
        $tutor = GiaSu::findOrFail($id);
        $tutor->update($request->validated());
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => new GiaSuResource($tutor)
        ]);
    }

    public function destroy($id)
    {
        $tutor = GiaSu::findOrFail($id);
        $tutor->delete();

        return response()->json(['message' => 'Đã xóa gia sư.']);
    }
}
