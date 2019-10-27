<?php
class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->model('Auth_model');
        $this->load->model('User_model');
    }

    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        $data['title'] = 'login';
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            $user = $this->Auth_model->getDataByEmail();
            //jika email ada
            if ($user) {
                //jika user aktif
                if ($user['is_active'] == 1) {
                    if (password_verify($this->input->post('password'), $user['password'])) {
                        $data = [
                            'email' => $user['email'],
                            'rule_id' => $user['rule_id']
                        ];
                        //jika user atau admin
                        if ($user['rule_id'] == 1) {
                            $this->session->set_userdata($data);
                            redirect('admin');
                        } else {
                            $this->session->set_userdata($data);
                            redirect('user');
                        }
                    } else {
                        $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>password salah </strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        redirect('auth');
                    }
                } else {
                    $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>email belum aktif </strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>email tidak ditemukan </strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('auth');
            }
        }
    }

    public function signup()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        $data['title'] = 'signup';
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/signup');
            $this->load->view('templates/auth_footer');
        } else {
            $this->Auth_model->tambahUser();
            $token = $this->Auth_model->token();
            $this->Auth_model->sendEmail($token);
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>user berhasil didaftarkan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth');
        }
    }

    public function verify()
    {
        $aktivasiUser = $this->Auth_model->verifyAccount();

        if ($aktivasiUser) {
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>user berhasil di aktivasi</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('rule_id');

        $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>user berhasil logout</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        redirect('auth');
    }

    public function blocked()
    {
        $data['title'] = 'blocked';
        $email = $this->session->userdata('email');
        $data['user'] = $this->User_model->getDataByEmail($email);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('auth/blocked');
        $this->load->view('templates/footer');
    }

    public function forgetPassword()
    {
        $data['title'] = 'Forget Password';

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgetpassword');
            $this->load->view('templates/auth_footer');
        } else {
            $token = $this->Auth_model->token();
            $this->Auth_model->sendEmailForgetPassword($token);
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>cek email</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth');
        }
    }

    public function changePassword()
    {
        $data['title'] = 'Change Password';
        $email = $this->input->get('email');
        $aktivasiUser = $this->Auth_model->verifyAccount();
        if ($aktivasiUser) {
            $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]');
            $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');
            if ($this->form_validation->run() == false) {
                $this->load->view('templates/auth_header', $data);
                $this->load->view('auth/changepassword');
                $this->load->view('templates/auth_footer');
            } else {
                $newPassword = $this->input->post('password1');
                $this->db->set('password', password_hash($newPassword, PASSWORD_DEFAULT));
                $this->db->where('email', $email);
                $this->db->update('user');
                $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Password successfuly changed</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('auth');
            }
        }
    }
}
