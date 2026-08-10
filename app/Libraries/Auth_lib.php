<?php
#[AllowDynamicProperties]
class Auth_lib {

    protected $CI;

    public function __construct() {
        $this->CI = get_instance();
        $this->CI->load->library('session');
        $this->CI->load->library('encryption');
    }

    /**
     * Check if a user is currently authenticated
     */
    public function is_logged_in() {
        return (bool) $this->CI->session->userdata('logged_in');
    }

    /**
     * Authenticate administrative credentials
     */
    public function login($username_or_email, $password, $remember = FALSE) {
        // Standard Database Authentication
        $this->CI->load->model('auth_model');
        $user = $this->CI->auth_model->verify_user($username_or_email, $password);

        if ($user === 'DEFAULT_CREDENTIALS') {
            // SECURITY: refuse to authenticate an account that still holds
            // the well-known seeded default password hash, regardless of
            // whether the credentials technically matched. This closes the
            // gap when the installer was skipped, misconfigured, or the
            // database was re-seeded after go-live.
            $this->CI->session->set_flashdata(
                'error',
                'Akun ini masih menggunakan kredensial bawaan (default) dan telah diblokir demi keamanan. ' .
                'Ganti password admin langsung di database (kolom `users`.`password`, hash bcrypt) atau jalankan ulang installer sebelum login.'
            );
            return FALSE;
        }

        if ($user) {
            // Set User Session
            $session_data = array(
                'user_id'   => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'logged_in' => TRUE
            );

            // Fetch user role
            $role_id = 0;
            $role_name = 'Editor';
            
            if ($this->CI->db->table_exists('user_roles')) {
                $this->CI->db->select('ur.role_id, r.name as role_name');
                $this->CI->db->from('user_roles ur');
                $this->CI->db->join('roles r', 'r.id = ur.role_id');
                $this->CI->db->where('ur.user_id', $user->id);
                $role_query = $this->CI->db->get()->row();
                
                if ($role_query) {
                    $role_id = $role_query->role_id;
                    $role_name = $role_query->role_name;
                }
            }

            $session_data['role_id']   = $role_id;
            $session_data['role_name'] = $role_name;

            $this->CI->session->set_userdata($session_data);

            // Handle Remember Me (Encrypted Cookie)
            if ($remember) {
                $this->CI->load->helper('cookie');
                $token = bin2hex(random_bytes(16));
                $cookie_data = array(
                    'name'   => 'educms_remember',
                    'value'  => $this->CI->encryption->encrypt(json_encode(array('uid' => $user->id, 'token' => $token))),
                    'expire' => 1209600 // 14 days
                );
                set_cookie($cookie_data);
            }

            // Write audit log
            $this->CI->load->library('logger');
            $this->CI->logger->log($user->id, 'Auth', 'login', NULL, json_encode(array('ip' => $this->CI->input->ip_address())));

            return TRUE;
        }
        return FALSE;
    }

    /**
     * Terminate active authentication session
     */
    public function logout() {
        $user_id = $this->CI->session->userdata('user_id');
        
        if ($user_id) {
            $this->CI->load->library('logger');
            $this->CI->logger->log($user_id, 'Auth', 'logout', NULL, NULL);
        }

        // Delete remember me cookie
        $this->CI->load->helper('cookie');
        delete_cookie('educms_remember');

        // Destroy sessions
        $this->CI->session->sess_destroy();
    }

    /**
     * Get details of any user by ID
     */
    public function get_user($id) {
        return $this->CI->db->get_where('users', array('id' => $id, 'deleted_at' => NULL))->row();
    }

    /**
     * Get details of currently logged-in user
     */
    public function get_current_user() {
        if (!$this->is_logged_in()) {
            return NULL;
        }
        return $this->get_user($this->CI->session->userdata('user_id'));
    }

    /**
     * Hash plain password
     */
    public function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
