<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function storeReply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $reply = $comment->replies()->create([
            'user_id' => auth()->id(),
            'post_id' => $comment->post_id, // Lưu post_id từ comment gốc
            'content' => $request->content,
            'parent_id' => $comment->id, // Lưu parent_id từ comment gốc
        ]);

        $reply->formatted_time = $this->formatTime($reply->created_at);

        return response()->json(['success' => true, 'reply' => $reply->load('user')]);
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $comment->formatted_time = $this->formatTime($comment->created_at);

        return response()->json(['success' => true, 'comment' => $comment->load('user')]);
    }

    public function getLikes(Post $post)
    {
        $likes = $post->likes()->with('user')->get();

        foreach ($likes as $like) {
            $like->formatted_time = $this->formatTime($like->created_at);
        }

        return response()->json(['likes' => $likes]);
    }

    public function getComments(Post $post)
    {
        $comments = $post->comments()->with('user', 'replies.user')->get();

        foreach ($comments as $comment) {
            $comment->formatted_time = $this->formatTime($comment->created_at);
            foreach ($comment->replies as $reply) {
                $reply->formatted_time = $this->formatTime($reply->created_at);
            }
        }

        return response()->json(['comments' => $comments]);
    }

    public function like(Post $post)
    {
        $user = auth()->user();

        // Kiểm tra xem người dùng đã like bài viết chưa
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // Nếu đã like, thì bỏ like
            $like->delete();
            $status = 'unliked';
        } else {
            // Nếu chưa like, thì thêm like
            $post->likes()->create(['user_id' => $user->id]);
            $status = 'liked';
        }

        // Trả về số lượng like hiện tại
        return response()->json(['success' => true, 'likes_count' => $post->likes()->count(), 'status' => $status]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Lấy danh sách bạn bè của người dùng hiện tại
        $friendIds = $user->isFriends()->pluck('id')->toArray();

        // Lấy danh sách bạn của bạn bè của người dùng hiện tại
        $friendOfFriendIds = User::whereIn('id', $friendIds)
            ->with('friends')
            ->get()
            ->pluck('friends')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // Lấy các bài viết theo các điều kiện
        $posts = Post::where(function ($query) use ($user, $friendIds, $friendOfFriendIds) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($query) use ($friendIds) {
                    $query->whereIn('user_id', $friendIds)
                        ->whereIn('privacy', ['public', 'friend']);
                })
                ->orWhere(function ($query) use ($friendOfFriendIds) {
                    $query->whereIn('user_id', $friendOfFriendIds)
                        ->where('privacy', 'public');
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        })->with(['user', 'likes', 'comments' => function ($query) {
            $query->whereNull('parent_id')->latest();
        }, 'comments.replies' => function ($query) {
            $query->latest();
        }])->latest()->get();

        // Định dạng thời gian cho từng bài viết
        foreach ($posts as $post) {
            $post->formatted_time = $this->formatTime($post->created_at);
            $post->is_liked = $post->likes->contains('user_id', $user->id);
            $post->comment_count = $post->comments()->count();

            foreach ($post->comments as $comment) {
                $comment->formatted_time = $this->formatTime($comment->created_at);
                foreach ($comment->replies as $reply) {
                    $reply->formatted_time = $this->formatTime($reply->created_at);
                }
            }
        }

        return view('pages.posts.index', compact('posts'));
    }

    private function formatTime($time)
    {
        $now = Carbon::now();
        $diffInSeconds = $now->diffInSeconds($time);
        $diffInMinutes = $now->diffInMinutes($time);
        $diffInHours = $now->diffInHours($time);
        $diffInDays = $now->diffInDays($time);
        $diffInWeeks = $now->diffInWeeks($time);
        $diffInMonths = $now->diffInMonths($time);
        $diffInYears = $now->diffInYears($time);

        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' giây trước';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' phút trước';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' giờ trước';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' ngày trước';
        } elseif ($diffInWeeks < 4) {
            return $diffInWeeks . ' tuần trước';
        } elseif ($diffInMonths < 12) {
            return $diffInMonths . ' tháng trước';
        } else {
            return $time->format('d/m/Y');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'content' => 'required',
            'privacy' => 'required|in:public,friend,private',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            'images' => 'array'
        ], [
            'content.required' => 'Nội dung không được để trống',
            'images.*.image' => 'File không phải là ảnh',
            'images.*.mimes' => 'File không đúng định dạng ảnh'
        ]);

        try {
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $name = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('/uploads/posts'), $name);
                    $images[] = '/uploads/posts/' . $name;
                }
            }

            Post::create([
                'user_id' => auth()->id(),
                'content' => $request->content,
                'images' => $images,
                'privacy' => $request->privacy,
            ]);

            return back()->with('success', 'Đăng bài viết thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Đăng bài viết thất bại' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Kiểm tra xem người dùng hiện tại có quyền xóa bài viết hay không
        if ($post->user_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xóa bài viết này.'], 403);
        }
    
        try {
            $post->delete();
            return response()->json(['status' => 'success', 'message' => 'Bài viết đã được xóa.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Có lỗi xảy ra khi xóa bài viết.'], 500);
        }
    }
}
