<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Folder;
use App\Task;

class TaskController extends Controller
{
    public function index(int $id) {
        // すべてのフォルダの取得
        $folders = Folder::all();

        // 選ばれたフォルダを取得
        $current_folder = Folder::find($id);

        // 選ばれたフォルダに紐づくタスクを取得
        // $tasks = Task::where('folder_id', $current_folder->id)->get();
        $tasks = $current_folder->tasks()->get();

        return view('tasks/index', [
            'folders' => $folders,
            'current_folder_id' => $current_folder->id,
            'tasks' => $tasks,
        ]);
    }
}
