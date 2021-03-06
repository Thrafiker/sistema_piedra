<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\PostForm;
use App\Http\Requests\CommentForm;
use Auth;

// para politicas
use Gate;


class PostsController extends Controller{

	public function getAll(){

		// Hacemos variable posts con los Post con user, de 2 en 2
		return view("posts.list")->with('posts', Post::with("user")->paginate(2)->setPath('all'));
	}

	/*
	* display form posts
	*/
	public function getCreate(){

		return view("posts.create");
	}


	/*
	* Crea post y una SESSION para mostrar mensaje de registrado en Vista posts/create.blade.php
	*/
	public function postCreate(PostForm $postForm){

		$post = new Post;
		$post->title = \Request::input('title');
		$post->content = \Request::input('content');
		$post->foto = \Request::input('foto');
		$post->user_id = Auth::user()->id;
		$post->save();

		\Session::flash('post_created', \Lang::get("messages.post_created"));
		return redirect()->back();
	}

	// ============================= vistas GET y POST de URL Edit =======================================
	/*
	* display form posts for edit, 
	* If it does not exist, return to the previous view.
	*/
	public function getEdit($id){

		$post = Post::find($id);

		if (Gate::denies('update', $post)) {
			\Session::flash('not_autorizate_updated', \Lang::get("messages.not_autorizate_updated"));
		}else{
			if($post){
				return view("posts.edit")->with('post', $post);
			}
		}
		
		return redirect()->back();
		
	}

	/** Editando un Post y regreso back()
	*	creo SESION post_updated, servira para sacar mensaje
	*	
		@if ( Session: :has('post_updated'))
			< div ... alert>{ !! Session::get('post_updated') !! }
	*
	* Inyectamos Requests/PostForm.php (RULES), y las usa implicitamente
	*/
	public function postEdit(PostForm $postForm, $id){ // importa ORDEN en Controllers

		$post = Post::find($id);
		$post->title = \Request::input('title');
		$post->content = \Request::input('content');
		$post->foto = \Request::input('foto');

		/** Obtenemos en linea 
		Form: :hidden('user_id', $ post->user_id) ! !}  */
		$post->user_id = \Request::input('user_id');
		$post->save();

		\Session::flash('post_updated', \Lang::get("messages.post_updated"));
		
		return redirect()->back();
	}


	// ====================================================================

	/** http://localhost:8080/posts/detail/1
	*
	* @return Vista posts/detail.php tendra 2 arrays post y comments (si tiene)
	*/	
	public function getDetail($id){

		$post = Post::find($id);
		if($post){
			$comments = Comment::with("user")->where("post_id", "=", $id)->get();

			return view("posts.detail")->with(array('post' => $post, "comments" => $comments));
		}
		return redirect()->back();
	}


	/** Enviar un comentario a un Post y regreso back()
	*	creo SESION comment_saved
	*/
	public function postComment(CommentForm $commentForm, $id){

		$comment = new Comment;

		$comment->subject = \Request::input("subject");
		$comment->comment = \Request::input("comment");
		
		$comment->post_id = $id;


		$comment->user_id = Auth::user()->id;
		$comment->save();
		
		\Session::flash('comment_saved', \Lang::get("messages.comment_saved"));
		
		return redirect()->back();
	}


	// ====================================================================
	/** Borrado del Post y sus comentarios por ser cascade en su Batabases/Migration/...Post.php */
	public function deleteDestroy($id){

		$post = Post::find($id);

		if (Gate::denies('eliminar', $post)) {
			\Session::flash('not_autorizate_delete', \Lang::get("messages.not_autorizate_delete"));
		}else{

			//$post = Post::find($id);
		
			if($post){
				$post->delete();
				\Session::flash('post_deleted', \Lang::get("messages.post_deleted"));
			}
		}
		return redirect()->back();

	}


}
