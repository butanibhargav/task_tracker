<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;
    //table name
    protected $table = "sub_tasks";
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'task_id'
    ];

    public function Task()
    {
        return $this->belongsTo('App\Models\Task');
    }
}
