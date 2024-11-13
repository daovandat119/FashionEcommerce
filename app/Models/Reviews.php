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
        'UserID',
        'ProductID',
        'RatingLevelID',
        'ParentReviewID',
        'ReviewContent',
        'created_at',
        'updated_at',
    ];

    public function createReview($data)
    {
        $review = Reviews::create([
            'UserID' => $data['UserID'],
            'ProductID' => $data['ProductID'],
            'RatingLevelID' => $data['RatingLevelID'],
            'ParentReviewID' => $data['ParentReviewID'],
            'ReviewContent' => $data['ReviewContent'],
        ]);

        if ($review) {
            return Reviews::find($review->ReviewID);
        }

        return false;
    }

    public function checkReview($userId, $productId)
    {
        return Reviews::where('UserID', $userId)
            ->where('ProductID', $productId)
            ->exists();
    }

    public function getReviewsWithChildren($productId)
    {
        $reviews = Reviews::with('children')
            ->where('ProductID', $productId)
            ->whereNull('ParentReviewID')
            ->get();

        return $reviews;
    }

    public function children()
    {
        return $this->hasMany(Reviews::class, 'ParentReviewID', 'ReviewID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
