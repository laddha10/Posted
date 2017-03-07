<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Posts;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        //fetch 5 posts from database which are active and latest
        $posts = Posts::where('active',1)->orderBy('created_at','desc')->paginate(5);
        //page heading
        $title = 'Latest Posts';
        //return home.blade.php template from resources/views folder
        return view('home')->with(['posts'=>$posts,'title'=>$title]);
    }
    public function create(Request $request){

        If($request->user()->can_post())
        {
            return view('posts.create');
        }
        return redirect('/')->withErrors('You have not sufficient permission for writing posts');
    }
    public function store(PostFormRequest $request)
    {
        $post=new Posts();
        $post->title=$request->get('title');
        $post->body=$request->get('body');
        $post->slug = str_slug($post->title);
        $post->author_id=$request->user()->id;
        if($request->has('save'))
        {
            $post->active=0;
            $message='Post saved successfully';
        }
        else
        {
            $post->active=1;
            $message='Post Published successfully';
        }
        $post->save();
        return redirect('edit/'.$post->slug)->withMessage($message);
    }
    public function show($slug)
    {
        $post=Posts::where('slug',$slug)->first();
        if(!$post)
        {
            return redirect('/')-withErrors('Requested post not found');
        }
        $comments=$post->comments;
        return view('posts.show')->withPost($post)->withComments($comments);
    }
    public  function edit(Request $request,$slug)
    {
        $post=Posts::where('slug',$slug)->first();
        if($post && ($request->user()->id==$post->author_id || $request->user()->is_admin()))
        {
              return view('posts.edit')->with('post',$post);
        }
        return redirect('/')->with('You do not have sufficient permissions');
    }
    public function update(Request $request)
    {
        $post_id = $request->input('post_id');
        $post = Posts::find($post_id);
        if ($post && ($request->user()->id == $post->author_id || $request->user()->is_admin())) {
            $title = $request->input('title');
            $slug = str_slug($title);
            $duplicate = Posts::where('slug', $slug)->first();
            if ($duplicate) {
                if ($duplicate->id != $post_id) {
                    return redirect('edit/' . $slug)->withErrors('Title already exist') - withInput();
                } else {
                    $post->slug = $slug;
                }
            }
            $post->title = $title;
            $post->body = $request->input('body');
            if ($request->has('Save')) {
                $post->active = 0;
                $message = 'Post saved successfully';
                $landing = 'edit/' . $post->slug;
            } else {
                $post->active = 1;
                $message = 'Post updated successfully';
                $landing = 'edit/' . $post->slug;
            }
            $post->save();
            return redirect($landing)->withMessage($message);
        } else {
            return redirect('/')->withMessage('You do not have sufficient permission');
        }
    }
        public function destroy(Request $request, $id)
    {
        //
        $post = Posts::find($id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
        {
            $post->delete();
            $data['message'] = 'Post deleted Successfully';
        }
        else
        {
            $data['errors'] = 'Invalid Operation. You have not sufficient permissions';
        }
        return redirect('/')->with($data);
    }

    
}
