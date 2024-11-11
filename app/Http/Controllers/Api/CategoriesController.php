<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriesRequest;

class CategoriesController extends Controller
{
    protected $repoCategories;

    public function __construct()
    {
        $this->repoCategories = new Categories();
    }

    public function index(Request $request)
    {
        $role = auth()->check() ? auth()->user()->role->RoleName : 'User';

        $total = $this->repoCategories->countCategories();
        $page = $request->input('Page', 1);
        $limit = $request->input('Limit', 10);

        $categories = $this->repoCategories->listCategories(
            ...($role == 'Admin' ? [
                $request->input('Search'),
                ($page - 1) * $limit,
                $limit,
                null
            ] : [])
        );

        $totalPage = ceil($total / $limit);

        return response()->json([
            'message' => 'Success',
            'data' => $categories,
            ...($role == 'Admin' ? [
                'total' => $total,
                'totalPage' => $totalPage,
                'page' => $page,
                'limit' => $limit
            ] : [])
        ], 200);
    }
    //
    public function store(CategoriesRequest $request)
    {
        $dataInsert = [
            'CategoryName' => $request->input('CategoryName'),
            'Status' => 'ACTIVE'
        ];

        $this->repoCategories->addCategory($dataInsert);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $dataInsert
        ], 201);
    }

    public function edit($id)
    {
        $category = $this->repoCategories->getDetail($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $category
        ], 200);
    }

    public function update(CategoriesRequest $request, $id)
    {
        $category = $this->repoCategories->getDetail($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $newCategoryName = $request->input('CategoryName');

        $dataUpdate = [
            'CategoryName' => $newCategoryName,
        ];

        $this->repoCategories->updateCategory($id, $dataUpdate);

        $updatedCategory = $this->repoCategories->getDetail($id);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $updatedCategory
        ], 200);
    }

    public function delete($id)
    {
        // $ids = $request->input('ids');
        // $idArray = explode(',', $ids);
        // $results = [];

        // foreach ($idArray as $id) {
        //     $id = trim($id);
        //     $category = $this->repoCategories->getDetail($id);

        //     if (!$category) {
        //         $results[] = ['id' => $id, 'message' => 'Category not found'];
        //         continue;
        //     }



        //     $results[] = ['id' => $id, 'message' => 'Deleted successfully'];
        // }

        $this->repoCategories->deleteCategoryAndRelatedData($id);

        return response()->json([
            'message' => 'Operation completed',
            'id' => $id
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $category = $this->repoCategories->getDetail($id);
//

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $this->repoCategories->updateCategoryAndRelatedStatus($id, $request->input('Status'));
//
        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully',
        ], 200);
    }
}
