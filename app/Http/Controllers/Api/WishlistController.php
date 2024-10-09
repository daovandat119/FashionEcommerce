<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;


class WishlistController extends Controller
{
    protected $wishlist;

    public function __construct()
    {
        $this->wishlist = new Wishlist();
    }

    public function create(Request $request)
    {
        $userID = 4;

        $data = [
            'UserID' => $userID,
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
        $userID = 4;

        $wishlist = $this->wishlist->getWishlistByUserID($userID);

        return response()->json(['message' => 'Success', 'data' => $wishlist], 200);
    }

    public function destroy($id)
    {
        $userID = 4;

        $data = [
            'UserID' => $userID,
            'WishlistID' => $id,
        ];

        $wishlist = $this->wishlist->getWishlistByID($data);

        if($wishlist){
            $this->wishlist->deleteWishlist($data);
        }

        return response()->json(['message' => 'Success', 'data' => $wishlist], 200);
    }
}
