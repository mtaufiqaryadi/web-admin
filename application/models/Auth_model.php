<?php
class Auth_model extends CI_Model
{
    public function tambahUser()
    {
        $data = [
            'name' => htmlspecialchars($this->input->post('name', true)),
            'email' => htmlspecialchars($this->input->post('email', true)),
            'image' => 'default.jpg',
            'password' => password_hash($this->input->post('password2'), PASSWORD_DEFAULT),
            'rule_id' => 2,
            'is_active' => 0,
            'date_created' => time()
        ];
        $this->db->insert('user', $data);
    }

    public function getDataByEmail()
    {
        $email = htmlspecialchars($this->input->post('email', true));
        $user = $this->db->get_where('user', ['email' => $email]);
        return $user->row_array();
    }


    public function sendEmail($token)
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'peluitakhir@gmail.com',
            'smtp_pass' => 'taufiq971002',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];
        $emailServer = $config['smtp_user'];
        $emailUser = $this->input->post('email');;
        $this->email->initialize($config);
        $this->email->from($emailServer, 'Peluit Akhir');
        $this->email->to($emailUser);
        $this->email->subject('Verify Account');
        $this->email->message('Klik link berikut untuk aktivasi akun: <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . $token . '">Aktivasi</a>');
        if ($this->email->send()) {
            return true;
        } else {
            show_error($this->email->print_debugger());
            die;
        }
    }

    public function sendEmailForgetPassword($token)
    {
        $email = $this->input->post('email');
        //cek apakah terdapat akun dengan email diatas
        $this->db->where('email', $email);
        $result = $this->db->get('user')->row_array();
        if ($result) {
            $config = [
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_user' => 'peluitakhir@gmail.com',
                'smtp_pass' => 'taufiq971002',
                'smtp_port' => 465,
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];
            $this->email->initialize($config);
            $this->email->from($config['smtp_user'], 'Peluit Akhir');
            $this->email->to($email);
            $this->email->subject('Forget Password');
            $this->email->message('<a href="' . base_url() . 'auth/changepassword?email=' . $this->input->post('email') . '&token=' . $token . '">Aktivasi</a>');
            if ($this->email->send()) {
                return true;
            } else {
                show_error($this->email->print_debugger());
                die;
            }
        } else {
            $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>email tidak ditemukan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth/forgetpassword');
        }
    }

    public function token()
    {
        $token = base64_encode(random_bytes(32));
        $email = $this->input->post('email');
        $user_token = [
            'email' => $email,
            'token' => $token,
            'date_created' => time()
        ];

        $this->db->insert('user_token', $user_token);

        $result = $this->db->get_where('user_token', ['email' => $user_token['email']])->row_array();
        return $result['token'];
    }

    public function verifyAccount()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');
        $user = $this->db->get_where('user_token', ['email' => $email])->row_array();
        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            if ($user_token) {
                $this->db->set('is_active', 1);
                $this->db->where('email', $email);
                $this->db->update('user');
                return true;
            } else {
                $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>token tidak ditemukan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('flash', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>email tidak ditemukan</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth');
        }
    }
}
