<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderReview;
class OrderReviewController extends Controller
{
    public function createOrderReview(Request $request)
    {
        $userId = auth()->id();

        $dataOrderReview = [
            'OrderID' => $request->OrderID,
            'UserID' => $userId,
            'RatingLevelID' => $request->RatingLevelID,
            'Review' => $request->Review,
        ];

        (new OrderReview())->createOrderReview($dataOrderReview);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo đánh giá đơn hàng thành công',
        ], 201);

    }
}
