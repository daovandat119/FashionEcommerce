<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sizes;
use App\Http\Requests\SizeRequest;

class SizeController extends Controller
{
    protected $repoSizes;

    public function __construct()
    {
        $this->repoSizes = new Sizes();
    }

    public function index()
    {
        $sizes = $this->repoSizes->listSizes();
        return response()->json(['message' => 'Success', 'data' => $sizes], 200);
    }

    public function store(SizeRequest $request)
    {
        $dataInsert = [
            'SizeName' => $request->input('SizeName'),
        ];

        $this->repoSizes->addSize($dataInsert);

        return response()->json(['message' => 'Success', 'data' => $dataInsert], 201);
    }

    public function edit($id)
    {
        $size = $this->repoSizes->getDetail($id);

        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        return response()->json($size);
    }

    public function update(SizeRequest $request, $id)
    {

        $size = $this->repoSizes->getDetail($id);

        $dataUpdate = [
            'SizeName' => $request->input('SizeName'),
        ];

        $this->repoSizes->updateSize($id, $dataUpdate);

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $dataUpdate], 200);
    }

    public function delete($id)
    {
        $size = $this->repoSizes->getDetail($id);

        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        $this->repoSizes->deleteSize($id);

        return response()->json(['message' => 'Xóa thành công!'], 200);
    }

}
