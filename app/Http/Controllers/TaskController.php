<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Folder;
use App\Task;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Folder $folder) {
        // 認可処理
        // if (Auth::user()->id != $folder->user_id) {
        //     abort(403);
        // }
        // すべてのフォルダの取得
        // $folders = Folder::all();
        $folders = Auth::user()->folders()->get();

        // 選ばれたフォルダを取得
        // $current_folder = Folder::find($id);

        // if (is_null($current_folder)) {
        //     abort(404);         // エラー系のレスポンスを返却
        // }

        // 選ばれたフォルダに紐づくタスクを取得
        // $tasks = Task::where('folder_id', $current_folder->id)->get();
        // $tasks = $current_folder->tasks()->get();
        $tasks = $folder->tasks()->get();

        return view('tasks/index', [
            'folders' => $folders,
            // 'current_folder_id' => $current_folder->id,
            'current_folder_id' => $folder->id,
            'tasks' => $tasks,
        ]);
    }

    /**
     * タスク作成フォーム
     * GET /folders/{id}/tasks/create
     * @param Folder $folder
     * @return \Illuminate\View\View
     */
    public function showCreateForm(Folder $folder) {
        return view('tasks/create', [
            'folder_id' => $folder->id,
        ]);
    }

    /**
     * タスク作成
     * @param Folder $folder
     * @param CreateTask $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Folder $folder, CreateTask $request) {
        // $current_folder = Folder::find($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        // $current_folder->tasks()->save($task);
        $folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            // 'id' => $current_folder->id,
            'id' => $folder->id,
        ]);
    }

    /**
     * タスク編集フォーム
     * @param Folder $folder
     * @param Task $taask
     */
    public function showEditForm(Folder $folder, Task $task) {
        // 関連のないタスクの処理
        $this->checkRelation($folder, $task);
        
        // $task = Task::find($task_id);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }

    /**
     * タスク編集
     * @param Folder $folder
     * @param Task $task
     * @param EditTask $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Folder $folder, Task $task, EditTask $request) {
        // 関連のないタスクの処理
        $this->checkRelation($folder, $task);
        
        // $task = Task::find($task_id);

        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();

        return redirect()->route('tasks.index', [
            'id' => $task->folder_id,
        ]);
    }

    public function checkRelation(Folder $folder, Task $task) {
        if ($folder->id !== $task->folder_id) {
            abort(404);
        }
    }
}
