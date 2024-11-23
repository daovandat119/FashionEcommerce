<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
    use HasFactory;

    protected $table = 'order_reviews';

    protected $fillable = [
        'OrderID',
        'UserID',
        'RatingLevelID',
        'Review',
    ];

    public function createOrderReview($dataOrderReview)
    {
        return OrderReview::create([
            'OrderID' => $dataOrderReview['OrderID'],
            'UserID' => $dataOrderReview['UserID'],
            'RatingLevelID' => $dataOrderReview['RatingLevelID'],
            'Review' => $dataOrderReview['Review'],
        ]);
    }

}
