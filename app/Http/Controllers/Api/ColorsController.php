<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Colors;
use App\Http\Requests\ColorRequest;

class ColorsController extends Controller
{
    protected $repoColors;

    public function __construct()
    {
        $this->repoColors = new Colors();
    }

    public function index()
    {
        $role = auth()->check() ? auth()->user()->role->RoleName : 'User';

        $colors = $this->repoColors->listColors($role);
        return response()->json(['message' => 'Success', 'data' => $colors], 200);
    }

    public function store(ColorRequest $request)
    {
        $data = [
            'ColorName' => $request->input('ColorName'),
        ];
        $color = $this->repoColors->addColor($data);

        return response()->json(['message' => 'Màu sắc đã được thêm thành công!', 'data' => $color], 201);
    }
//

    public function edit($id)
    {
        $color = $this->repoColors->getDetail($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        return response()->json(['message' => 'Success', 'data' => $color], 200);
    }

    public function update(ColorRequest $request, $id)
    {
        $color = $this->repoColors->getDetail($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $data = [
            'ColorName' => $request->input('ColorName'),
        ];

        $this->repoColors->updateColor($id, $data);

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $data], 200);
    }

    public function delete($id)
    {
        $color = $this->repoColors->getDetail($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $this->repoColors->deleteColor($id);

        return response()->json(['message' => 'Thành công!'], 200);
    }
}
