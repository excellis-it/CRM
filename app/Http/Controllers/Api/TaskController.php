<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\AssignTask;
use App\Mail\assignTaskMail;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

/**
 * @group Task APIs
 *
 * APIs for Task management
 */

class TaskController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Task list', ['only' => ['taskListByProject']]);
        $this->middleware('permission:Task create', ['only' => ['taskCreate']]);
        $this->middleware('permission:Task edit', ['only' => ['taskEdit','taskUpdate']]);
        $this->middleware('permission:Task delete', ['only' => ['taskDelete']]);
        $this->middleware('permission:Task assign', ['only' => ['assignTask']]);
    }
    
    /*
    * create task api
    * @bodyParam title string required The title of the task.
    * @bodyParam description string required The description of the task.
    * @bodyParam project_id int required The project id of the task.
    * @response 200 {
    *   "data": {
    *    "project_id": 1,
    *    "title": "make an authentication",
    *    "description": "lorem ipsum dolor sit amet",
    *    "updated_at": "2023-06-30T07:32:34.000000Z",
    *    "created_at": "2023-06-30T07:32:34.000000Z",
    *    "id": 2
    *    },
    *  "success": true,
    *  "message": "Task created successfully" 
    * }
    * @response 401 {
    *  "error": {
    *    "status_code": 401,
    *    "message": [
    *      "The title field is required.",
    *      "The description field is required.",
    *      "The project id field is required."
    *    ]
    *  },
    *  "status": false
    * }
    */

    public function taskCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);
         
        if ($validator->fails()) {
            $errors['status_code'] = 401;
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['error' => $errors, 'status' => false], 401);
        }
        try {

            $task = new Task;
            $task->project_id = $request->project_id;
            $task->title = $request->title; 
            $task->description = $request->description;
            $task->save();
            return response()->json([
                'data' => $task,
                'success' => true,
                'message' => 'Task created successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
    /*
    * view all task by project api
    * @bodyParam project_id int required The project id of the task.
    * @response 200 {
    *   "data": [
    *    {
    *      "id": 1,
    *      "project_id": 1,
    *      "title": "make an authentication",
    *      "description": "lorem ipsum dolor sit amet",
    *      "created_at": "2023-06-30T07:32:34.000000Z",
    *      "updated_at": "2023-06-30T07:32:34.000000Z"
    *    },
    *    ]
    *  "success": true,
    *  "message": "Task list available"
    * }
    * @response 401 {
    *    "status_code": 401,
    *    "message": "Task list not available",
    *    "success": false,
    * }
    */

    public function taskListByProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        try{
            $task_list = Task::where('project_id', $request->project_id)->get();
            if(count($task_list) > 0){
                return response()->json([
                    'data' => $task_list,
                    'success' => true,
                    'message' => 'Task list available'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => true,
                    'message' => 'Task list not available'
                ]);
            }      
        }catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }      
    }

    /*
    * view task details api
    * @bodyParam task_id int required The task id of the task.
    * @response 200 {
    *   "data": {
    *    "id": 1,
    *    "project_id": 1,
    *    "title": "make an authentication",
    *    "description": "lorem ipsum dolor sit amet",
    *    "created_at": "2023-06-30T07:32:34.000000Z",
    *    "updated_at": "2023-06-30T07:32:34.000000Z"
    *    },
    *  "success": true,
    *  "message": "Task available"
    * }
    * @response 401 {
    *    "status_code": 401,
    *    "message": "Task not available",
    *    "success": false,
    * }
    */

    public function taskEdit(Request $request)
    {
        $validatopr = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }

        try{
            $task = Task::find($request->task_id);
            if($task){
                return response()->json([
                    'data' => $task,
                    'success' => true,
                    'message' => 'Task available'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => true,
                    'message' => 'Task not available'
                ]);
            }
        }catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }    
    }

    /*
    * update task api
    * @bodyParam task_id int required The task id of the task.
    * @bodyParam title string required The title of the task.
    * @bodyParam description string required The description of the task.
    * @response 200 {
    *   "data": {
    *    "id": 1,
    *    "project_id": 1,
    *    "title": "make an authentication",
    *    "description": "lorem ipsum dolor sit amet",
    *    "created_at": "2023-06-30T07:32:34.000000Z",
    *    "updated_at": "2023-06-30T07:32:34.000000Z"
    *    },
    *  "success": true,
    *  "message": "Task updated successfully"
    * }
    * @response 401 {
    *    "status_code": 401,
    *    "message": "Task not updated",
    *    "success": false,
    * }
    */

    public function taskUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,'error' => $errors], 401);
        }

        try{
            $task = Task::find($request->task_id);
            if($task){
                $task->title = $request->title;
                $task->description = $request->description;
                $task->save();
                return response()->json([
                    'data' => $task,
                    'success' => true,
                    'message' => 'Task updated successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => true,
                    'message' => 'Task not updated'
                ]);
            }
        }catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }    
    }

    /*
    * assign task api
    * @bodyParam task_id int required The task id of the task.
    * @bodyParam user_id int required The user id of the user.
    * @bodyParam due_on date required The due date of the task.
    * @response 200 {
    *   "data": {
    *    "id": 1,
    *    "project_id": 1,
    *    "title": "make an authentication",
    *    "description": "lorem ipsum dolor sit amet",
    *    "created_at": "2023-06-30T07:32:34.000000Z",
    *    "updated_at": "2023-06-30T07:32:34.000000Z"
    *    },
    *  "success": true,
    *  "message": "Task assigned successfully"
    * }

    * @response 401 {
    *    "status_code": 401,
    *    "message": "Task not assigned",
    *    "success": false,
    * }
    */


    public function assignTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'due_on' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,'error' => $errors], 401);
        }

        try{
            $task = new AssignTask;
            if($task){
                $task->task_id = $request->task_id;
                $task->emp_id = $request->user_id;
                $task->due_on = $request->due_on;
                $task->notes = $request->notes;
                $task->save();
                $user = User::find($request->user_id);
                $task = Task::find($request->task_id)->with('project')->first();

                $maildata = [
                    'user' => $user,
                    'project' => $task->project,
                ];
                Mail::to($user->email)->send(new assignTaskMail($maildata));
    
                return response()->json([
                    'data' => $task,
                    'success' => true,
                    'message' => 'Task assigned successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => true,
                    'message' => 'Task not assigned'
                ]);
            }
        }catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    public function taskDelete(Request $request)
    {
        return $request;

    }

}
