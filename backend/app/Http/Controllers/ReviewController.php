<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getReviewedData']]);
    }

    public function reviewed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10|max:200',
            'rating' => 'required|integer|min:1|max:5',
            'blog_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        $existingReview = Review::where('blog_id', $request->blog_id)->first();

        if ($existingReview) {
            $reviewData = json_decode($existingReview->review_data, true);

            if ($reviewData === null) {
                $reviewData = [];
            }

            // Check if the user's review already exists in the array
            $userReviewExists = false;
            foreach ($reviewData as $key => $review) {
                if ($review['user_id'] === $user->id) {
                    // Update the existing review
                    $reviewData[$key]['rating'] = $request->rating;
                    $reviewData[$key]['message'] = $request->message;
                    $userReviewExists = true;
                    break; // Exit the loop since the user's review has been updated
                }
            }

            // If the user's review doesn't exist, add it to the array
            if (!$userReviewExists) {
                $newData = [
                    "user_id" => $user->id,
                    "rating" => $request->rating,
                    "message" => $request->message
                ];

                // Add the new data to the beginning of the array
                array_unshift($reviewData, $newData);
            }

            $existingReview->update([
                'review_data' => json_encode($reviewData)
            ]);

            return response()->json(['message' => 'Review added/updated successfully', 'review' => $existingReview]);
        } else {
            $input = [
                'blog_id' => $request->blog_id,
                'review_data' => json_encode([
                    [
                        "user_id"  => $user->id,
                        "rating" => $request->rating,
                        "message" => $request->message
                    ]
                ])
            ];
            $review = Review::create($input);

            return response()->json(['message' => 'Review added successfully', 'review' => $review]);
        }
    }

    public function getReviewedData($blogId)
    {
        $review = Review::where('blog_id', $blogId)->first();

        $reviewsWithUsers = [];

        $reviewsData = json_decode($review->review_data, true);

        foreach ($reviewsData as $review) {
            $userId = $review['user_id'];

            // Retrieve the user for this review
            $user = User::find($userId);

            // Include the review and user in the response
            $reviewsWithUsers[] = [
                'review' => $review,
                'user' => $user,
            ];
        }

        return response()->json(['reviewsWithUsers' => $reviewsWithUsers]);
    }
}
