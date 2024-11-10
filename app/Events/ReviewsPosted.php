<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewsPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    public function broadcastWith()
    {
        return [
            'ReviewContent' => $this->review->ReviewContent,
            'UserID' => $this->review->UserID,
            'ParentReviewID' => $this->review->ParentReviewID,
            'ProductID' => $this->review->ProductID,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('reviews'),
        ];
    }
}
