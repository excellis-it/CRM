<?php

namespace Database\Seeders;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $departments = ['Calling','Support','Development','Design','Quality','Human resource','Marketing','Sales','Admin'];
        foreach ($departments as $department) {
            $add_dept = new Department();
            $add_dept->name = $department;
            $add_dept->save();
        }
    }
}
