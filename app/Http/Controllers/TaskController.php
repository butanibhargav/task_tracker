<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SubTask;
use App\Models\TrackTask;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $task=Task::orderBy('id','DESC')->get();
        return view('task')->with(["task"=>$task]);
    }

    /**
     * Show the form for creating a new resource for task.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('add_task');
    }

    /**
     * Create new task resources
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
       $validator=$request->validate([
        'name'=>'required',
        'task_name'=> 'required',
        'assigned_time' => 'required|date_format:Y-m-d H:i:s'
        ]);

       $task = new Task([
        'name' => $request->get('name'),
        'task_name'=> $request->get('task_name'),
        'assigned_time'=> $request->get('assigned_time')
        ]);

       $task->save();
       return redirect()->route('task.index')->with('success','Task Added');
   }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * edit task
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        return view('add_task')->with(['task'=>$task]);
    }

    /**
     * Update task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        // validation
        $validator=$request->validate([
            'name'=>'required',
            'task_name'=> 'required',
            'assigned_time' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $data=$request->all();
        $task->update($data);
        return redirect()->route('task.index')->with('success','Task Updated');
    }

    /**
     * delete task
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task=Task::findOrFail($id);
        // task have subtask return delete error
        if(!$task->subtasks->isEmpty())
        {
            return redirect()->route('task.index')->with('warning','Delete Error! Task have sub tasks');
        }
        // update delete flag
        else
        {
            $task->deleted = !$task->deleted;
            $task->save();
            return redirect()->route('task.index')->with('success','Task updated Successfully');
        }

    }


    /**
     * Toggle task complete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle_complete($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();
        return redirect('task');
    }

    /**
     * create subtask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create_subtask(Request $request, Task $id)
    {
        if($request->isMethod('post'))
        {
            // validation
            $validator=$request->validate([
                'name'=>'required',
            ]);
            // subtask 
            $sub_task = new SubTask([
                'name' => $request->get('name'),
                'task_id'=> $id->id
            ]);
            $sub_task->save();

            return redirect()->route('task.index')->with('success','Sub Task Added');
        }
        else
        {   
            // add form view
            return view('add_sub_task')->with(["task"=>$id]);
        }
    }

    /**
     * Clone Task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function clone(Task $task)
    {
        if($task)
        {
            // clone task
         $create_task = new Task([
            'name' => $task->name,
            'task_name'=> $task->task_name,
            'assigned_time'=> $task->assigned_time
        ]);
         $create_task->save();

           // clone subtasks
         foreach ($task->subtasks as $key => $value) {
             $sub_task = new SubTask([
                'name' => $value->name,
                'task_id' => $create_task->id
            ]);

             $sub_task->save();
         }
         return redirect()->route('task.index')->with('success','Task Clone Created');
     }
 }

    /**
     * Track Task Time.
     *
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function track(Request $request)
    {
        if($request->get('task_id') && $request->get('seconds'))
        {
            $task_id = $request->get('task_id');
            $seconds = $request->get('seconds');
            $task = Task::findOrFail($task_id);
            $track_task = TrackTask::where(["task_id"=>$task_id])->first();
            // update exising time
            if($track_task)
            {
                list($hours, $minutes, $sec) = explode(":", $track_task->total_time);
                $time_in_seconds=($hours*3600+$minutes*60+$sec)+$seconds;
                $track_task->total_time=gmdate("H:i:s", $time_in_seconds);
                $track_task->save();
            }
            // new entry
            else
            {
                $new_track_task = new TrackTask;
                $new_track_task->task_id = $task_id;
                $new_track_task->total_time = gmdate("H:i:s", $seconds);
                $new_track_task->save();
            }
            // json response
            return response()->json(['seconds' => $seconds, "task_id" => $task_id]);
        }
    }
}
