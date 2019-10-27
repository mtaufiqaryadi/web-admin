<?php

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('Admin_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Dashbord';
        $data['user'] = $this->Admin_model->getDataByEmail($email);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Role';
        $data['user'] = $this->Admin_model->getDataByEmail($email);
        $data['role'] = $this->Admin_model->getRole();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role', $data);
        $this->load->view('templates/footer');
    }

    public function accessrole($id)
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Access Role';
        $data['user'] = $this->Admin_model->getDataByEmail($email);
        $data['menu'] = $this->Admin_model->getMenu();
        $data['rule'] = $this->Admin_model->getRoleById($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/access-role', $data);
        $this->load->view('templates/footer');
    }

    public function addRole()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Role';
        $data['user'] = $this->Admin_model->getDataByEmail($email);
        $data['role'] = $this->Admin_model->getRole();

        $this->form_validation->set_rules('role', 'Role', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Admin_model->addNewRole();
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>role berhasil ditambahkan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('admin/role');
        }
    }

    public function changeAccess()
    {
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');
        $data = [
            'rule_id' => $role_id,
            'menu_id' => $menu_id
        ];
        $result = $this->db->get_where('user_access_menu', $data);
        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
        } else {
            $this->db->delete('user_access_menu', $data);
        }

        $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>access berhasil diubah</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
    }
}
