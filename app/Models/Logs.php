<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Logs extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public static function add_log($table_name, $primary_id, $data, $action = 'add', $updated_by )
    {
        $update_data = array();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $update_data['system_info'] = $user_agent;
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

        $update_data['agent_ip'] = $ip;

        if (!empty($data)) {
            foreach ($data as $key => $record) {
                $update_data[$key] = $record;
            }
        }

        if(!empty($updated_by)){
            $updated_by = isset(Auth::user()->id) ? Auth::user()->id : NULL ;
        }else{
            $updated_by = NULL ;
        }

        $data = array(
            'table_name' => $table_name,
            'primary_id' => $primary_id,
            'data' => json_encode($update_data),
            'action' => $action,
            'created_by' => isset(Auth::user()->id) ? Auth::user()->id : 1 ,
            'updated_by' => $updated_by 
        );
        Logs::create($data);
    }

    public static function get_logs_details($table_name, $primary_id)
    {
        $logs_records = Logs::where(['table_name' => $table_name, 'primary_id' => $primary_id, 'action' => 'add'])->get();
        $logs = array();
        $data_create_log=array();
        if (count($logs_records) > 0) {
            $log = $logs_records[0];
            $create_log_data = json_decode($log->data);

            $created_by = User::where('id', $log->created_by)->get()->first();

            $data_create_log = array(
                'timestamp' => date('d-m-Y h:i A', strtotime($log->created_at)),
                'by' => isset($created_by->name) ? $created_by->name . ' - ' . $created_by->email : 'Applicant',
                'agent_ip' => isset($create_log_data->agent_ip) ? $create_log_data->agent_ip : ''
            );


        }
        $logs['created_log'] = $data_create_log;

        $logs_records = Logs::where(['table_name' => $table_name, 'primary_id' => $primary_id, 'action' => 'edit'])->orderBy('id','desc')->get();
        $data_modified_log=array();
        if (count($logs_records) > 0) {
            $log = $logs_records[0];
            $create_log_data = json_decode($log->data);
            $created_by = User::where('id', $log->created_by)->get()->first();

            $data_modified_log = array(
                'timestamp' => date('d-m-Y h:i A', strtotime($log->created_at)),
                'by' => isset($created_by->name) ? $created_by->name . ' - ' . $created_by->email : 'None',
                'agent_ip' => isset($create_log_data->agent_ip) ? $create_log_data->agent_ip : ''
            );

        }
        $logs['modified_log'] = $data_modified_log;


        return $logs;
    }

}
