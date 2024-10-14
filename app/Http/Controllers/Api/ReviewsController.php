<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;
use App\Models\Order;

class ReviewsController extends Controller
{
    protected $reviewRepository;

    public function __construct()
    {
        $this->reviewRepository = new Reviews();
    }

    public function index($id)
    {
        $reviews = $this->reviewRepository->getReviews($id);

        return response()->json([
            'message' => 'Success',
            'data' => $reviews,
        ], 200);
    }
//
    public function store(Request $request)
    {
        $userId = auth()->id();

        $order = (new Order())->checkOrder($userId, $request->ProductID);

        if (!$order) {
            return response()->json([
               'message' => 'You have not bought this product',
            ], 400);
        }

        $data = [
            'UserID' => $userId,
            'ProductID' => $request->ProductID,
            'RatingLevelID' => $request->RatingLevelID,
            'ReviewContent' => $request->ReviewContent,
        ];

        $this->reviewRepository->createReview($data);

        return response()->json([
            'message' => 'Review created successfully',
        ], 201);
    }

}
