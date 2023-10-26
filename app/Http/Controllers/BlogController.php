<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\BlogRequest;
use App\Models\LikeDislike;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{

    public function getblogpost(Request $request)
    {
        if(Auth::user()->role == 'admin'){
            $status = $request->input('status');
            $author = $request->input('author');
            $date = $request->input('date');
    
            $query = BlogPost::query();
    
            if ($status) {
                $query->where('status', $status);
            }
    
            if ($author) {
                $query->whereHas('author', function ($q) use ($author) {
                    $q->where('name', 'like', '%' . $author . '%');
                });
            }
    
            if ($date) {
                $query->whereDate('created_at', $date);
            }
    
            $blogPosts = $query->get();
    
            return response()->json([
                'success' => true,
                'message' => 'Blog post list',
                'data' => $blogPosts
            ]);
        }else{
            $status = $request->input('status');
            $author = $request->input('author');
            $date = $request->input('date');
    
            $query = BlogPost::query();
    
            if ($status) {
                $query->where('status', $status);
            }
    
            if ($author) {
                $query->whereHas('author', function ($q) use ($author) {
                    $q->where('name', 'like', '%' . $author . '%');
                });
            }
    
            if ($date) {
                $query->whereDate('created_at', $date);
            }
    
            $blogPosts = $query->where('author_id', Auth::user()->id)->get();
    
            return response()->json([
                'success' => true,
                'message' => 'Blog post list',
                'data' => $blogPosts
            ]);
        }
       
    }

    public function get($id)
    {
        $blogPost = BlogPost::find($id);

        if($blogPost->author_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this blog post',
            ], Response::HTTP_FORBIDDEN);
        }


        if(!$blogPost){
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog post found successfully',
            'data' => $blogPost,
        ], Response::HTTP_OK);
    }
    
     public function store(BlogRequest $request)
     {
        if($request->input('status') == 'draft'){
            $blogPost = BlogPost::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'status' => $request->input('status'),
                'author_id' => Auth::user()->id,
            ]);
        }else{
            $blogPost = BlogPost::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'published_date' =>date('Y-m-d H:i:s'),
                'status' => $request->input('status'),
                'author_id' => Auth::user()->id,
            ]);
        }
 
         return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully',
            'data' => $blogPost,
        ], Response::HTTP_CREATED);
     }
 
     public function update(BlogRequest $request, $id)
     {
      
         $blogPost = BlogPost::find($id);

         if($blogPost->author_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this blog post',
            ], Response::HTTP_FORBIDDEN);
        }


         if(!$blogPost){
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], Response::HTTP_NOT_FOUND);
         }
    
         if($request->input('status') == 'draft'){
            $blogPost->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'status' => $request->input('status'),
                'author_id' => Auth::user()->id,
            ]);
        }else{
            $blogPost->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'published_date' =>date('Y-m-d H:i:s'),
                'status' => $request->input('status'),
                'author_id' => Auth::user()->id,
            ]);
        }
 

       
 
         return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully',
            'data' => $blogPost,
        ], Response::HTTP_OK);
     }
 
     public function destory($id)
     {
        $blogPost = BlogPost::find($id);

        if($blogPost->author_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this blog post',
            ], Response::HTTP_FORBIDDEN);
        }

        if(!$blogPost){
           return response()->json([
               'success' => false,
               'message' => 'Blog post not found',
           ], Response::HTTP_NOT_FOUND);
        }
    
         $blogPost->delete();
 
         return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully'
        ]);
     }


        public function like($postId)
        {
            $blogPost = BlogPost::find($postId);
    
            if(!$blogPost){
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], Response::HTTP_NOT_FOUND);
            }

            $ceklike = LikeDislike::where('user_id', Auth::user()->id)
                                  ->where('blog_post_id', $postId)
                                  ->first();
            if($ceklike){
                $ceklike->delete();
            }

            LikeDislike::create([
                'user_id' => Auth::user()->id,
                'blog_post_id' => $postId,
                'type' => 'like'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blog post liked successfully'
            ], Response::HTTP_CREATED);
        }

        public function dislike($postId)
        {
            $blogPost = BlogPost::find($postId);
    
            if(!$blogPost){
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], Response::HTTP_NOT_FOUND);
            }

            $ceklike = LikeDislike::where('user_id', Auth::user()->id)
                                  ->where('blog_post_id', $postId)
                                  ->first();
            if($ceklike){
                $ceklike->delete();
            }

            LikeDislike::create([
                'user_id' => Auth::user()->id,
                'blog_post_id' => $postId,
                'type' => 'dislike'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blog post disliked successfully'
            ], Response::HTTP_CREATED);
        }

        


 
    
}
