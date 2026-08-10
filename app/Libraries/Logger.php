<?php
#[AllowDynamicProperties]
class Logger {

    protected $CI;

    public function __construct() {
        $this->CI = get_instance();
        $this->CI->load->library('user_agent');
    }

    /**
     * Write an audit log entry to database or fallback file log
     */
    public function log($user_id, $module, $action, $old_value = NULL, $new_value = NULL) {
        $ip = $this->CI->input->ip_address();
        
        // Parse User Agent details
        $browser = $this->CI->agent->browser() . ' ' . $this->CI->agent->version();
        $os = $this->CI->agent->platform();

        // Prepare insert data
        $log_data = array(
            'user_id'          => $user_id,
            'module'           => $module,
            'action'           => $action,
            'old_value'        => $old_value !== NULL ? (is_array($old_value) || is_object($old_value) ? json_encode($old_value) : $old_value) : NULL,
            'new_value'        => $new_value !== NULL ? (is_array($new_value) || is_object($new_value) ? json_encode($new_value) : $new_value) : NULL,
            'ip_address'       => $ip,
            'browser'          => $browser,
            'operating_system' => $os,
            'created_at'       => date('Y-m-d H:i:s')
        );

        // Fail-safe check
        if ($this->CI->db->table_exists('activity_logs')) {
            $this->CI->db->insert('activity_logs', $log_data);
        } else {
            // Write to system logs
            log_message('info', 'Activity Log Fallback - ' . json_encode($log_data));
        }
    }
}
