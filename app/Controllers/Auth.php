<?php

namespace App\Controllers;

use \MY_Controller;
class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('auth_lib', NULL, 'auth');
    }

    /**
     * Handle Login Form Render and Submission
     */
    public function login() {
        // Redirect to dashboard if already authenticated
        if ($this->auth->is_logged_in()) {
            redirect('admin');
        }

        // Set Validation Rules
        $this->form_validation->set_rules('identity', 'Username atau Email', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $identity = $this->input->post('identity');
            $password = $this->input->post('password');
            $remember = (bool) $this->input->post('remember');

            if ($this->auth->login($identity, $password, $remember)) {
                // Retrieve pending redirection url
                $redirect = $this->session->userdata('redirect_to');
                $this->session->unset_userdata('redirect_to');
                
                redirect($redirect ? $redirect : 'admin');
            } else {
                $this->session->set_flashdata('error', 'Username/Email atau Password salah.');
                redirect('admin/login');
            }
        } else {
            // Render View
            $this->load->view('auth/login');
        }
    }

    /**
     * Terminate user session
     */
    public function logout() {
        $this->auth->logout();
        redirect('admin/login');
    }

    /**
     * Handle Password Recovery Requests
     */
    public function forgot() {
        // Set validation rules
        $this->form_validation->set_rules('email', 'Alamat Email', 'required|valid_email|trim');

        if ($this->form_validation->run() === TRUE) {
            $email = $this->input->post('email');
            
            $exists = FALSE;
            if ($this->db->table_exists('users')) {
                $query = $this->db->get_where('users', array('email' => $email, 'deleted_at' => NULL));
                $exists = ($query->num_rows() > 0);
            } else {
                // Mock fallback email
                $exists = ($email === 'admin@educms.local');
            }

            if ($exists) {
                // In production, send a reset email token here
                $this->session->set_flashdata('success', 'Instruksi pemulihan sandi telah dikirim ke email Anda.');
            } else {
                $this->session->set_flashdata('error', 'Email tidak ditemukan dalam sistem kami.');
            }
            redirect('admin/forgot');
        } else {
            $this->load->view('auth/forgot');
        }
    }
}
