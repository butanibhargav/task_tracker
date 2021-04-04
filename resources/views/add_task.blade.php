<!DOCTYPE html>
<html>
<head>
  <title>Tasks</title>
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div class="row">
            
            @if(isset($task))
            <h2 class="text-center">Update Task</h2>
            <form method="post" id="" action="{{ route('task.update',$task->id) }}">
                @method('PATCH')
                @else
                <h2 class="text-center">Add Task</h2>
                <form method="post" id="add_task_form" action="{{ route('task.store') }}">
                   @endif
                   @csrf
                   <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $task->name ?? old('name') }}">
                </div>
                @error('name')
                <div class="alert alert-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
                <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">Task Name:</label>
                    <input type="text" class="form-control" id="task_name" name="task_name" value="{{ $task->task_name ?? old('task_name') }}">
                </div>
                @error('task_name')
                <div class="alert alert-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
                <div class="mb-3" style="position: relative;">
                    <label for="recipient-name" class="col-form-label">Assigned Date Time:</label>
                    <input type="text" class="form-control" id="assigned_date" name="assigned_time" value="{{ $task->assigned_time ?? old('assigned_time') }}">
                </div>
                @error('assigned_time')
                <div class="alert alert-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>

        </div>
    </div>
    <script
    src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#assigned_date').datetimepicker({
            format:'YYYY-MM-DD HH:mm:ss'    
        });
    }); 
</script>
</body>
