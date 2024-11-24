<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Http\Requests\WishlistRequest;
//

class WishlistController extends Controller
{
    protected $wishlist;

    public function __construct()
    {
        $this->wishlist = new Wishlist();
    }

    public function create(WishlistRequest $request)
    {
        $userId = auth()->id();

        $data = [
            'UserID' => $userId,
            'ProductID' => $request->ProductID,
        ];

        $wishlist = $this->wishlist->getWishlist($data);

        if(!$wishlist){
            $this->wishlist->addToWishlist($data);
        }

        return response()->json(['message' => 'Success', 'data' => $data], 200);
    }

    public function index()
    {
        $userId = auth()->id();
        
        $wishlist = $this->wishlist->getWishlistByUserID($userId);

        return response()->json(['message' => 'Success', 'data' => $wishlist], 200);
    }

    public function destroy($id)
    {
        $userId = auth()->id();

        $data = [
            'UserID' => $userId,
            'WishlistID' => $id,
        ];

        $wishlist = $this->wishlist->getWishlistByID($data);

        if($wishlist){
            $this->wishlist->deleteWishlist($data);
        }

        return response()->json(['message' => 'Success', 'data' => $wishlist], 200);
    }
}
