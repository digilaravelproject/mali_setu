<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentManagementController extends Controller
{
    /**
     * Display a listing of blog comments.
     */
    public function index(Request $request)
    {
        $query = BlogComment::with(['user', 'blog']);

        // Search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('blog', function ($bq) use ($search) {
                      $bq->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        $comments = $query->latest()->paginate(20);

        $stats = [
            'total' => BlogComment::count(),
            'top_level' => BlogComment::whereNull('parent_id')->count(),
            'replies' => BlogComment::whereNotNull('parent_id')->count(),
        ];

        return view('admin.blog-comments.index', compact('comments', 'stats'));
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->delete();

            return redirect()->route('admin.blog-comments.index')->with('success', 'Comment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete comment: ' . $e->getMessage());
        }
    }
}
