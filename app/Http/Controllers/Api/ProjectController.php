<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\AssignProject;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

/**
 * @group Project APIs
 *
 * APIs for Projects
 */
class ProjectController extends Controller
{
    //
    function __construct()
    {
        $this->middleware('permission:Project list', ['only' => ['projectList']]);
        $this->middleware('permission:Project edit', ['only' => ['projectEdit']]);
        $this->middleware('permission:Project update', ['only' => ['projectUpdate']]);
        $this->middleware('permission:Project create', ['only' => ['projectCreate']]);
        $this->middleware('permission:Project delete', ['only' => ['permissionDelete']]);
        $this->middleware('permission:Project assign', ['only' => ['assignProject']]);
    }

    /*
    *  Project list
    *  @response 200 {
    *    "data": [
    *        {
    *            "id": 2,
    *            "created_by": "2",
    *            "title": "Test Project",
    *            "description": "Lorem ipsum dolor sit amet",
    *            "project_type": "Web development",
    *            "start_date": "2023-06-28",
    *            "end_date": "2023-09-20",
    *            "created_at": "2023-06-28T10:57:01.000000Z",
    *            "updated_at": "2023-06-28T10:57:01.000000Z"
    *        },
    *        {
    *            "id": 1,
    *            "created_by": "2",
    *            "title": "Xyz project",
    *            "description": "lorem ipsum dolor sit amet",
    *            "project_type": "web development",
    *            "start_date": "2023-06-22",
    *            "end_date": "2023-08-12",
    *            "created_at": "2023-06-28T10:01:36.000000Z",
    *            "updated_at": "2023-06-29T10:27:17.000000Z"
    *        }
    *    ],
    *    "success": true,
    *    "message": "Project list found successfully"
    */

    public function projectList()
    {
        $projects = Project::latest()->get();
        try {
            $count = $projects->count();
            if ($count > 0) {
                return response()->json([
                    'data' => $projects,
                    'success' => true,
                    'message' => 'Project list found successfully'
                ]);
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'Projects list empty'
                ]);
            }     
        } catch (\Throwable $th) {
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  Project edit
    *  @bodyParam id int required The id of the project. Example: 1
    *  @response 200 {
    *    "data": {
    *        "id": 1,
    *        "created_by": "2",
    *        "title": "xyz project",
    *        "description": "lorem ipsum dolor sit amet",
    *        "project_type": "web development",
    *        "start_date": "2023-06-22",
    *        "end_date": "2023-08-12",
    *        "created_at": "2023-06-28T10:01:36.000000Z",
    *        "updated_at": "2023-06-29T10:27:17.000000Z"
    *    },
    *    "success": true,
    *    "message": "Project found successfully"
    */


    public function projectCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'project_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
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

            $project = new Project;
            $project->created_by = Auth::user()->id;
            $project->title = $request->title;
            $project->description = $request->description;
            $project->project_type = $request->project_type;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->status = 1;
            $project->save();
        
            return response()->json([
                'data' => $project,
                'success' => true,
                'message' => 'Project created successfully'
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  Project assign
    *  @bodyParam project_id int required The id of the project. Example: 1
    *  @bodyParam user_id int required The id of the user. Example: 1
    *  @response 200 {
    *    "data": {
    *        "id": 1,
    *        "project_id": "1",
    *        "user_id": "2",     
    *        "created_at": "2023-06-28T10:01:36.000000Z",
    *        "updated_at": "2023-06-29T10:27:17.000000Z"
    *    },
    *    "success": true,
    *    "message": "Project assign successfully"
    */



    public function assignProject(request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'user_id' => 'required',
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
            $check_assign_project = AssignProject::where('project_id', $request->project_id)->get();
            if(count($check_assign_project) > 0){
                foreach ($check_assign_project as $key => $value) {
                    $value->delete();
                }
            }
            $project_id = $request->project_id;
            foreach($request->user_id as $key => $value){
                $assignProject = new AssignProject;
                $assignProject->project_id = $project_id;
                $assignProject->employee_id = $value;         
                $assignProject->save();
            }
            
            return response()->json([
                'data' => $assignProject,
                'success' => true,
                'message' => 'Project assigned successfully'
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }    
    }

    /*
    *  Project update
    *  @bodyParam project_id int required The id of the project. Example: 1
    *  @bodyParam title string required The title of the project. Example: xyz project
    *  @bodyParam description string required The description of the project. Example: lorem ipsum dolor sit amet
    *  @bodyParam project_type string required The project type of the project. Example: web development
    *  @bodyParam start_date date required The start date of the project. Example: 2023-06-22
    *  @bodyParam end_date date required The end date of the project. Example: 2023-08-12
    *  @response 200 {
    *    "data": {
    *        "id": 1,
    *        "created_by": "1",
    *        "title": "xyz project",
    *        "description": "lorem ipsum dolor sit amet",
    *        "project_type": "web development",
    *        "start_date": "2023-06-22",
    *        "end_date": "2023-08-12",
    *        "created_at": "2023-06-28T10:01:36.000000Z",
    *        "updated_at": "2023-06-29T10:27:17.000000Z"
    *    },
    *    "success": true,
    *    "message": "Project updated successfully"
    *    }
    *    @response 401 {
    *    "error": {
    *        "status_code": 401,
    *        "message": [
    *            "The project id field is required.",
    *            "The title field is required.",
    *            "The description field is required.",
    *            "The project type field is required.",
    *            "The start date field is required.",
    *            "The end date field is required."
    *        ]
    *    },
    *    "status": false
    *    }
    */
    
    

    public function projectUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required',
            'description' => 'required',
            'project_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
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
            $project = Project::where('id', $request->project_id)->first();
            $project->title = $request->title;
            $project->description = $request->description;
            $project->project_type = $request->project_type;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->update();
        
            return response()->json([
                'data' => $project,
                'success' => true,
                'message' => 'Project updated successfully'
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }     
    }

    /*
    *  Project edit
    *  @bodyParam project_id int required The id of the project. Example: 1
    *  @response 200 {
    *    "data": {
    *        "project_details": {
    *            "id": 1,
    *            "created_by": "1",
    *            "title": "xyz project",
    *            "description": "lorem ipsum dolor sit amet",
    *            "project_type": "web development",
    *            "start_date": "2023-06-22",
    *            "end_date": "2023-08-12"
    *        },
    *        "assign_project": [
    *            {
    *                "id": 1,
    *                "employee_id": "1",
    *                "employee": {
    *                    "id": 1,
    *                    "name": "admin",
    *                    "email": "admin@yopmail.com",
    *                }
    *            }
    *        ]
    *    },
    *    "success": true,
    *    "message": "Project details found successfully"
    * }
    * @response 401 {
    *    "status": false,
    *    "statusCode": 401,
    *    "message": "The selected project id is invalid."
    * }
    */




    public function projectEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }

        try{
            $data['project_details'] = Project::select('id','created_by','title','description','project_type','start_date','end_date')->where('id', $request->project_id)->first();
            $data['assign_project'] = AssignProject::select('id','employee_id')->where('project_id', $request->project_id)->with('employee:id,name,email')->get();

            return response()->json([
                'data' => $data,
                'success' => true,
                'message' => 'Project details found successfully'
            ]);
        }catch(\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }    

    }
}
