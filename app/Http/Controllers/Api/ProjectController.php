<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class ProjectController extends Controller
{
    //
    public function projectList()
    {
        $projects = Project::latest()->get();
        try {
            if(Auth::user()->hasPermissionTo('Project list')){
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

            }else{
                return response()->json([
                    'statusCode' => 401,
                    'success' => false,
                    'message' => 'Permission denied'
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

    public function assignProject(request $request)
    {
        return $request;

    }

    public function projectEdit(Request $request)
    {
        return $request;
    }
}
