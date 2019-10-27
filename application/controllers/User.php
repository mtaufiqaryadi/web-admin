<?php
class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'My Profile';
        $data['user'] = $this->User_model->getDataByEmail($email);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'edit profile';
        $data['user'] = $this->User_model->getDataByEmail($email);

        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/editprofile', $data);
            $this->load->view('templates/footer');
        } else {
            $this->User_model->editData();
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>data berhasil diedit</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('user');
        }
    }

    public function changePassword()
    {
        $email = $this->session->userdata('email');
        $data['user'] = $this->User_model->getDataByEmail($email);
        $data['title'] = 'Change Password';

        $this->form_validation->set_rules('currentPassword', 'Current Passowrd', 'required|trim');
        $this->form_validation->set_rules('newPassword1', 'New Password', 'required|trim|min_length[3]|matches[newPassword2]');
        $this->form_validation->set_rules('newPassword2', 'Repeat Password', 'required|trim|min_length[3]|matches[newPassword1]');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/changepassword', $data);
            $this->load->view('templates/footer');
        } else {
            $currentPassword = $this->input->post('currentPassword');
            $newPassword = $this->input->post('newPassword1');
            if (!password_verify($currentPassword, $data['user']['password'])) {
                $this->session->set_flashdata('flash', '<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Current Password Wrong</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('user/changepassword');
            } elseif ($currentPassword == $newPassword) {
                $this->session->set_flashdata('flash', '<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>New Password must different from current password</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('user/changepassword');
            } else {
                $this->db->set('password', password_hash($newPassword, PASSWORD_DEFAULT));
                $this->db->where('email', $email);
                $this->db->update('user');
                $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Password successfuly changed</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('user/changepassword');
            }
        }
    }
}
