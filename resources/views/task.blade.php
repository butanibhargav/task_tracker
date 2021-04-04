<!DOCTYPE html>
<html>
<head>
  <title>Tasks</title>
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta name="csrf-token"  content="{{ csrf_token() }}">
</head>
<body>
  <div class="container">
    <div class="row mt-3">
     @if(session()->has('success'))
     <div class="alert alert-success">{{ session('success') }}</div>
     @endif
     @if(session()->has('warning'))
     <div class="alert alert-warning">{{ session('warning') }}</div>
     @endif
     <div class="pull-right">
      <a class="btn btn-primary" href="{{ route('task.create') }}">Add Task</a>
    </div>
  </div>
  <div class="row">
    <div class="text-center">
      <h2 id="task_name">

      </h2>
    </div>
    <div id="timer" style="text-align:center;font-size: 25px;font-weight: bold"></div>
    <input type="hidden" name="task_id" id="task_id">
    <div id="hidden_time" style="display: none;"></div>
  </div>
  <div class="row">
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col">#</th>
          <th>Name</th>
          <th>Task Name</th>
          <th>Assigned DateTime</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($task as $tsk)
        <form id="delete_form_{{ $tsk->id }}" action="{{ route('task.destroy',$tsk->id) }}" method="post">
          @method('DELETE')
          @csrf
        </form>
        <tr @if($tsk->completed==true and $tsk->deleted==false) class='alert alert-success'  @elseif($tsk->deleted==true and $tsk->completed==false) class='alert alert-danger' @elseif($tsk->completed==true and $tsk->deleted==true) class='alert alert-warning' @endif>
          <td>
            @if($tsk->completed==false and $tsk->deleted==false)
            <a title="start timer" task_id="{{$tsk->id}}" onclick="start_timer('timer{{$tsk->id}}','{{$tsk->task_name}}')" id="timer{{$tsk->id}}" class="task_timer" track="start"><i class="fa fa-play"></i></a>
            @endif
          </td>
          <td>{{ $tsk->name }}</td>
          <td><b>{{ $tsk->task_name }}</b>
            @if($tsk->subtasks)
            <table>
             @foreach($tsk->subtasks as $sub_task) 
             <tr>
              <td></td>
              <td>{{ $sub_task->name }}</td>
            </tr>
            @endforeach
          </table>
          @endif
        </td>
        <td>{{ date('F d, Y h:i:sa',strtotime($tsk->assigned_time)) }}</td>
        <th>
          <a class="btn btn-warning" href="{{ route('task.edit',$tsk->id) }}">Edit</a>
          <a class="btn btn-success" href="{{ route('create.subtask',$tsk->id) }}">Add Sub Task</a>

          <a class="btn btn-primary" onclick="return confirm('are you sure to clone?')" href="{{ route('task.clone',$tsk->id) }}">Clone </a>
          <a href="{{ Route('complete_task',$tsk->id) }}" class="btn btn-success">{{ ($tsk->completed) ? 'Completed' : 'Complete' }}</a>
          @if($tsk->deleted==false)
          <a class="btn btn-danger" onclick="return confirm('Are you sure to delete?')?document.getElementById('delete_form_{{ $tsk->id }}').submit():'';">
            {{ ($tsk->deleted) ? 'Deleted' : 'Delete' }}
          </a>
          @else
          <a class="btn btn-danger" onclick="return confirm('Are you sure to undo delete?')?document.getElementById('delete_form_{{ $tsk->id }}').submit():'';">
            Deleted
          </a>
          @endif     
        </th>
      </tr>
      @empty
      <tr><td  colspan="5">No Task Found</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
</div>

<!-- add task model -->
<div class="modal fade" id="addsubtaskmodel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Sub Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger print-error-msg" style="display:none">
          <ul></ul>
        </div>
        <form method="post" id="add_task_form">
          @csrf
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name">
          </div>
          
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="add_task_btn">Save Sub Task</button>
      </div>
    </div>
  </div>
</div>
</body>
<script
src="https://code.jquery.com/jquery-3.6.0.min.js"
integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
crossorigin="anonymous"></script>

<script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.7.0/timer.jquery.js"></script>
<script type="text/javascript">
  //start a timer
  function start_timer(id,name)
  {
    // remove time
    $("#timer,#hidden_time").timer('remove');
    var task_id=$("#"+id).attr('task_id');
    $("#task_id").val(task_id);

    $("#task_name").html(name);
    var cls=$("#"+id+" i").attr('class')
    if(cls=="fa fa-play")
    {
      $("#"+id+" i").removeClass(cls);
      $("#"+id+" i").addClass('fa fa-pause')
      $('.task_timer').each(function(i, obj) {
        if($(obj).attr('id')!=id)
        {
          $(obj).children().removeClass('fa-pause');
          $(obj).children().addClass('fa-play');
        }
      });

    }
    else
    {
     $("#"+id+" i").removeClass('fa fa-pause')
     $("#"+id+" i").addClass('fa fa-play')
     

   }
   var stop_flag=1
   $('.task_timer').each(function(i, obj) {
    if($(obj).children().attr('class')=='fa fa-pause')
    {
      stop_flag=0
    }
  });
   if(stop_flag==1)
   {
    $('#timer,#hidden_time').timer('pause'); 
    // $("#task_time").timer('pause');
  }
  else
  { 
    $("#hidden_time").timer();
    $("#timer").timer({
      duration:'15s',
      format: '%H:%M:%S',
      callback: function() {
        var task_id=$("#task_id").val();
        var time_seconds=$("#hidden_time").data('seconds');
        // get second from hidden field
        update_value=parseInt(time_seconds);
        $.ajax({
          type:"post",
          url:"{{ Route('track.task') }}",
          data:{task_id:task_id,seconds:update_value,_token:'{{ csrf_token() }}'},
          success:function(data)
          {
            // data = JSON.parse(data);
            if(task_id == data.task_id)
            {
              $("#hidden_time").timer('remove');
              var new_start = update_value - data.seconds;
              $("#hidden_time").timer({
                seconds:new_start,
                format: '%H:%M:%S',
              });
            }

          }
        });
        
      },
      repeat: true

    });
  }


}

</script>
</html>