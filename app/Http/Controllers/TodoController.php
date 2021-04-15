<?php

namespace App\Http\Controllers;

use App\Http\Requests\newTodo;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $allTodos = Todo::all();

        return view('todos.list', ['todos' => $allTodos]);
    }

    /**
     * Create new view template
     * GET method
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('todos.new');
    }

    /**
     * Create
     * POST method
     * @param newTodo $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createNew(newTodo $request)
    {
        $input = $request->input();
        // use db entity
        Todo::create([
            'description' => $input['description'],
            'checked' => $input['checked'] ? true: false
        ]);

        Session::flash('success', 'Successful Todo added');
        return redirect('/');
    }

    /**
     * single edit form
     * GET
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function editUpdate(Request $request, string $id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            Session::flash('error', 'Unknown ID');
            return redirect('/');
        }
        return view('todos.edit', ['todo' => $todo]);
    }

    /**
     * single edit submission
     * POST
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::findOrfail($id);
        $todo->description = $request->input('description');
        $todo->checked = $request->input('checked') ? true : false;
        if(!$todo->save()) {
            Session::flash('error', 'Failed to update');
        } else {
            Session::flash('success', 'Successful Todo saved');
        }
        return redirect('/edit/' . $id);
    }

    /**
     * single edit submission
     * POST
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function updateChecked(Request $request, string $id, string $checked)
    {
        $todo = Todo::findOrfail($id);
        $todo->checked = $checked === '1' ? true : false;
        $message['status'] = $checked;
        if(!$todo->save()) {
            $message['message'] = 'Failed to update';
        } else {
            $message['message'] = 'Saved';
        }
        return response($message)->header('Content-type', 'application/json');
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request, string $id)
    {
        $todo = Todo::findOrfail($id);
        if(!$todo->delete()) {
            Session::flash('error', 'Failed to delete');
        } else {
            Session::flash('success', 'Successful deleted');
        }
        return redirect('/' );
    }
}
