<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCommentController extends Controller
{
    /**
     * Store a newly created comment or reply in storage.
     */
    public function store(Request $request, $blogId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        $blog = Blog::findOrFail($blogId);
        $user = Auth::user();

        // If parent_id is set, it means we are replying to a comment.
        // Rule: Only the blog owner can reply to comments on their blog.
        if ($request->filled('parent_id')) {
            $parent = BlogComment::findOrFail($request->parent_id);

            // Verify parent comment belongs to the same blog
            if ($parent->blog_id !== $blog->id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid parent comment.'], 400);
                }
                return back()->with('error', 'Invalid parent comment.');
            }

            // Verify authorized: current user must be the blog owner
            if ($blog->user_id !== $user->id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Only the blog owner can reply to comments.'], 403);
                }
                return back()->with('error', 'Unauthorized action. Only the blog owner can reply.');
            }
        }

        $comment = BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        // Load user relationship
        $comment->load('user');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $request->filled('parent_id') ? 'Reply posted successfully!' : 'Comment posted successfully!',
                'data' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'parent_id' => $comment->parent_id,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'name' => $comment->user->name,
                        'initial' => strtoupper(substr($comment->user->name, 0, 1)),
                    ],
                ]
            ]);
        }

        return back()->with('success', 'Comment posted successfully!');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($id)
    {
        $comment = BlogComment::with('blog')->findOrFail($id);
        $user = Auth::user();

        // Authorization: Only the comment author or the blog owner can delete comments
        if ($comment->user_id !== $user->id && $comment->blog->user_id !== $user->id) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return back()->with('error', 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        }

        return back()->with('success', 'Comment deleted successfully.');
    }
}
