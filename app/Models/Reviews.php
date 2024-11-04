<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Reviews extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $primaryKey = 'ReviewID';

    public $timestamps = true;

    protected $fillable = [
        'UserId',
        'ProductId',
        'RatingLevelId',
        'ReviewContent',
    ];

    public function getReviews($productId)
    {
        return DB::table($this->table)
            ->join('users', 'reviews.UserID', '=', 'users.UserID')
            ->join('rating_levels', 'reviews.RatingLevelID', '=', 'rating_levels.RatingLevelID')
            ->where('reviews.ProductID', $productId)
            ->select('users.UserName', 'rating_levels.LevelName', 'reviews.ReviewContent')
            ->get();
    }

    public function createReview($data)
    {
        return DB::table($this->table)->insert([
            'UserID' => $data['UserID'],
            'ProductID' => $data['ProductID'],
            'RatingLevelID' => $data['RatingLevelID'],
            'ReviewContent' => $data['ReviewContent'],
        ]);
    }
}
