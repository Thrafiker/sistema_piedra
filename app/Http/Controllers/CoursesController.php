<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\User;
use App\Http\Requests\CursoForm;
use DB;
use Auth;

class CoursesController extends Controller
{

    /*
    * retrieve and display all cursos with users
    */
    public function getAll()
    {
        return view("cursos.list")->with('cursos', Curso::with("users")->paginate(2)->setPath('all'));
    }

    /*
    * display form cursos
    */
    public function getCreate()
    {
        return view("cursos.create");
    }

    /*
    * crea un curso nuevo
    */
    public function postCreate(CursoForm $cursoForm)
    {
        $curso = new Curso;
        $curso->curso = \Request::input('curso');
        $curso->save();
        \Session::flash('course_created', \Lang::get("messages.course_created"));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $curso = Curso::find($id);
        if($curso)
        {
            return view("cursos.edit")->with('curso', $curso);
        }
        return redirect()->back();
    }

    public function postEdit(CursoForm $cursoForm, $id)
    {
        $curso = Curso::find($id);
        $curso->curso = \Request::input('curso');
        $curso->save();
        \Session::flash('course_updated', \Lang::get("messages.course_updated"));
        return redirect()->back();
    }

    public function deleteDestroy($id)
    {
        $curso = Curso::find($id);
        if($curso)
        {
            $curso->delete();
            \Session::flash('course_deleted', \Lang::get("messages.course_deleted"));
        }
        return redirect()->back();
    }

    public function getNotsubscribed()
    {
        $cursos = DB::select('SELECT c.* FROM cursos c');

        $mis_cursos = DB::select('
        SELECT c.*
        FROM cursos c
        INNER JOIN curso_user cu
        ON c.id = cu.curso_id
        WHERE cu.user_id = ?
        ', [Auth::user()->id]);

        $diff = array_udiff($cursos, $mis_cursos,
              function ($obj_a, $obj_b)
              {
                return $obj_a->id - $obj_b->id;
              }
        );

        return view("cursos.list_unsubscribed")->with('cursos', $diff);
    }

    public function getSubscribe($id)
    {
        $curso = Curso::find($id);
        if($curso)
        {
            $user = User::find(Auth::user()->id);
            $user->cursos()->save($curso);
            \Session::flash('course_related', \Lang::get("messages.course_related"));
            return redirect()->back();
        }
    }

}
