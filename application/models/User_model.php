<?php
class User_model extends CI_Model
{
    public function getDataByEmail($email)
    {
        $user = $this->db->get_where('user', ['email' => $email]);
        return $user->row_array();
    }

    public function editData()
    {
        $data = [
            'email' => $this->input->post('email'),
            'name' => $this->input->post('name')
        ];
        $user = $this->getDataByEmail($data['email']);
        $old_img = $user['image'];

        //cek gambar
        $upload_image = $_FILES['image'];
        if ($upload_image) {
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']     = '1000';
            $config['upload_path'] = './assets/img/profile/';

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('image')) {
                if ($old_img != 'default.jpg') {
                    unlink(FCPATH . 'assets/img/profile/' . $old_img);
                }
                $new_img = $this->upload->data('file_name');
                $this->db->set('image', $new_img);
            } else {
                $this->upload->display_errors();
            }
        }

        $this->db->set('name', $data['name']);
        $this->db->where('email', $data['email']);
        $this->db->update('user');
    }
}
