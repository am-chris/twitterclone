<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\Post\Comment;
use App\User;
use App\Notifications\YouWereMentioned;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $last_post = Post::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();

        // If last post and this post are within
        if (!is_null($last_post)) {
            $now = Carbon::now();
            $length = $last_post->created_at->diffInSeconds($now);

            if ($length <= config('posts.min_seconds_between_posts')) {
                return redirect()->back();
            }

            // If last post and new post are identical
            if ($request->content == $last_post->content) {
                return redirect()->back();
            }
        }

        $post = new Post;
        if (isset($request->post_id)) {
            $post->post_id = $request->post_id;
        }
        $post->user_id = Auth::id();
        $post->content = $request->content;
        $post->save();

        // Check the body of the post for mentioned users
        preg_match_all('/\@([^\s\.]+)/', $post->content, $matches);

        $usernames = $matches[1];

        foreach ($usernames as $username) {
            $user = User::whereUsername($username)->first();

            if ($user) {
                $user->notify(new YouWereMentioned($post));
            }
        }

        if ($request->ajax()) {
            return response(['status' => 'The post was created.']);
        } else {
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::check()) {
            if ($post->user->private == 1 && Auth::user()->followingUser($post->user->id) == 0) {
                abort(404);
            }
        } else {
            if ($post->user->private == 1) {
                abort(404);
            }
        }

        $comments = Post::where('post_id', $post->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('posts.show', compact('post', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $post_id)
    {
        $post = Post::where('id', $post_id)
            ->first();

        if (Auth::user()->admin !== 1 && Auth::id() !== $post->user_id) {
            if ($request->ajax()) {
                return response(['status' => 'You lack the permissions to complete this action.']);
            } else {
                Session::flash('error', 'You lack the permissions to complete this action.');
                return redirect()->back();
            }
        }

        $post->delete();

        if ($request->ajax()) {
            return response(['status' => 'The post was deleted.']);
        } else {
            Session::flash('success', 'The post was deleted.');
            return redirect()->back();
        }
    }
}
