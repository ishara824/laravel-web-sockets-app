<?php

namespace App\Http\Controllers;

use App\Events\PostNotification;
use App\Models\Post;
use Illuminate\Http\Request;
use Pusher\PushNotifications\PushNotifications;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'author' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ]);

        // Create the post
        $post = Post::create([
            'author' => $request->input('author'),
            'title' => $request->input('title'),
        ]);

        // Dispatch the event with the post data
        // broadcast(new PostNotification([
        //     'author' => $post->author,
        //     'title' => $post->title,
        // ]));

        $beamsClient = new PushNotifications([
            'instanceId' => env('VITE_PUSHER_BEAMS_INSTANCE_ID'),
            'secretKey' => env('VITE_PUSHER_BEAMS_SECRET_KEY')
        ]);

        $publishResponse = $beamsClient->publishToInterests(
            ["hello"],
            [
                "web" => [
                    "notification" => [
                        "title" => $post->author,
                        "body" => $post->title
                    ]
                ]
            ]
        );

        // Redirect with success message
        return redirect()->back()->with('success', 'Post created successfully!');
    }
}
