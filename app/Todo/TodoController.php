<?php

namespace App\Http\Controllers\App\Todo;

use App\Helper\ConstHelper;
use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\TodoModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);
        $search = $request->query('search', '');
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 5);

        if ($perPage <= 0) {
            $perPage = 5;
        }

        $perPageOptions = ConstHelper::OPTION_ROWS_PER_PAGE;

        return Inertia::render('app/todo/todo-page', [
            'todoList' => fn () =>
                TodoModel::query()
                    ->where('user_id', $auth->id)
                    ->when($search, function ($query) use ($search) {
                        $lower = strtolower($search);

                        $query->where(function ($q) use ($lower) {
                            $q->whereRaw('LOWER(title) LIKE ?', ["%{$lower}%"])
                              ->orWhereRaw('LOWER(description) LIKE ?', ["%{$lower}%"]);
                        });
                    })
                    ->orderByDesc('created_at')
                    ->paginate($perPage),

            'pageName'       => Inertia::always('Todo List'),
            'auth'           => Inertia::always($auth),
            'isEditor'       => Inertia::always($isEditor),
            'search'         => Inertia::always($search),
            'page'           => Inertia::always($page),
            'perPage'        => Inertia::always($perPage),
            'perPageOptions' => Inertia::always($perPageOptions),
        ]);
    }

    public function postChange(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);
        if (!$isEditor) {
            return back()->with('error', 'Tidak punya hak akses.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'isDone'      => 'required|boolean',
        ]);

        // Update
        if ($request->filled('todoId')) {
            $todo = TodoModel::where('id', $request->todoId)
                ->where('user_id', $auth->id)
                ->first();

            if (!$todo) {
                return back()->with('error', 'Todo tidak ditemukan.');
            }

            $todo->title = $request->title;
            $todo->description = $request->description;
            $todo->is_done = $request->isDone;
            $todo->save();

            return back()->with('success', 'Todo berhasil diperbarui.');
        }

        // Insert baru
        TodoModel::create([
            'id'          => ToolsHelper::generateId(),
            'user_id'     => $auth->id,
            'title'       => $request->title,
            'description' => $request->description,
            'is_done'     => $request->isDone,
        ]);

        return back()->with('success', 'Todo berhasil ditambahkan.');
    }

    public function postDelete(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);
        if (!$isEditor) {
            return back()->with('error', 'Tidak punya hak akses.');
        }

        $request->validate([
            'todoIds' => 'required|array',
        ]);

        TodoModel::whereIn('id', $request->todoIds)
            ->where('user_id', $auth->id)
            ->delete();

        return back()->with('success', 'Todo berhasil dihapus.');
    }

    private function checkIsEditor($auth): bool
    {
        return ToolsHelper::checkRoles('Todo', $auth->akses);
    }
}
