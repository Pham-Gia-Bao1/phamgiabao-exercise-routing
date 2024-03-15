<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

global $users;

// 5 group route
Route::prefix('/user')->group(function () use ($users) {

    // Get all users
    Route::get('/', function () use ($users) {
        return response()->json($users);
    });

    // Get user by index
    Route::get('/{userIndex}', function ($userIndex) use ($users) {
        if (!is_numeric($userIndex) || $userIndex < 0 || $userIndex >= count($users)) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return $users[$userIndex];
    })->where('userIndex', '[0-9]+')->name('get_user_by_index');

    // Get user by name
    Route::get('/{userName}', function ($userName) use ($users) {
        $foundUser = collect($users)->firstWhere('name', $userName);

        if ($foundUser) {
            return $foundUser;
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    })->where('userName', '[a-zA-Z]+')->name('get_user_by_name');


    // route api posts
    Route::get('/{userIndex}/post/{postIndex}', function ($userIndex, $postIndex) use ($users) {
        // Check if userIndex and postIndex are numeric
        if (!is_numeric($userIndex) || !is_numeric($postIndex)) {
            return response()->json(['error' => 'Invalid user or post index'], 400);
        }

        // Check if userIndex is within bounds
        if ($userIndex < 0 || $userIndex >= count($users)) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Get user
        $user = $users[$userIndex];

        // Check if postIndex is within bounds
        if ($postIndex < 0 || $postIndex >= count($user['posts'])) {
            return response()->json(['error' => "Can not find the post with id  $postIndex for user $userIndex "], 404);
        }

        // Get post
        $post = $user['posts'][$postIndex];

        return $post;
    })->where(['userIndex' => '[0-9]+', 'postIndex' => '[0-9]+'])->name('get_user_post');

    // Fallback route
    Route::fallback(function () {
        return response()->json(['error' => 'You cannot get a user like this!'], 404);
    })->name('fall_back');
});
