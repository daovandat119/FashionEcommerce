<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Events\ReviewsPosted;

class ReviewsController extends Controller
{
    protected $reviewRepository;

    public function __construct()
    {
        $this->reviewRepository = new Reviews();
    }
//
    public function index($id)
    {
        $reviews = $this->reviewRepository->getReviewsWithChildren($id);

        return response()->json([
            'message' => 'Success',
            'data' => $reviews,
        ], 200);
    }

    public function checkReviewByUser(Request $request)
    {
        $userId = auth()->id();

        $checkReview = $this->reviewRepository->checkReview($userId, $request->ProductID);

        $checkOrder = (new Order())->checkOrder($userId, $request->ProductID);

        return response()->json([
            'message' => 'Success',
            'data' => [
                'checkReview' => $checkReview,
                'checkOrder' => $checkOrder,
            ],
        ], 200);
    }
//
    public function store(Request $request)
    {
        $userId = auth()->id();

        $roleName = auth()->user()->RoleName;

        if ($roleName == 'User') {
            $order = (new Order())->checkOrder($userId, $request->ProductID);

            $checkReview = $this->reviewRepository->checkReview($userId, $request->ProductID);

            if (!$order) {
                return response()->json([
                    'message' => 'You have not bought this product',
                ], 400);
            }
        }

        $data = [
            'UserID' => $userId,
            'ProductID' => $request->ProductID,
            'RatingLevelID' => $request->RatingLevelID ?? null,
            'ReviewContent' => $request->ReviewContent,
            'ParentReviewID' => $request->ParentReviewID ?? null,
        ];

        $review = $this->reviewRepository->createReview($data);

        event(new ReviewsPosted($review));

        return response()->json([
            'message' => 'Review created successfully',
        ], 201);
    }

}
