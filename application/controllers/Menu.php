<?php
class Menu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('Menu_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Menu Management';
        $data['user'] = $this->Menu_model->getDataByEmail($email);
        $data['menu'] = $this->Menu_model->getMenu();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('templates/footer');
    }

    public function subMenu()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Sub Menu Management';
        $data['user'] = $this->Menu_model->getDataByEmail($email);
        $data['submenu'] = $this->Menu_model->getSubMenu();
        $data['menu'] = $this->Menu_model->getMenu();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('menu/submenu', $data);
        $this->load->view('templates/footer');
    }

    public function addMenu()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Menu Management';
        $data['user'] = $this->Menu_model->getDataByEmail($email);
        $data['menu'] = $this->Menu_model->getMenu();
        $this->form_validation->set_rules('menu', 'Menu', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Menu_model->addNewMenu();
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>menu berhasil ditambahkan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('menu');
        }
    }

    public function addSubMenu()
    {
        $email = $this->session->userdata('email');
        $data['title'] = 'Sub Menu Management';
        $data['user'] = $this->Menu_model->getDataByEmail($email);
        $data['submenu'] = $this->Menu_model->getSubMenu();
        $data['menu'] = $this->Menu_model->getMenu();

        $this->form_validation->set_rules('menu_id', 'Menu', 'required|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('url', 'URL', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Menu_model->addNewSubMenu();
            $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>sub menu berhasil ditambahkan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('menu/submenu');
        }
    }

    public function deleteMenu($id)
    {
        $this->Menu_model->deleteMenuById($id);
        $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>menu berhasil dihapus</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        redirect('menu');
    }

    public function deleteSubMenu($id)
    {
        $this->Menu_model->deleteSubMenuById($id);
        $this->session->set_flashdata('flash', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>sub menu berhasil dihapus</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        redirect('menu/submenu');
    }
}
