<?php
class Admin_model extends CI_Model
{
    public function getDataByEmail($email)
    {
        $user = $this->db->get_where('user', ['email' => $email]);
        return $user->row_array();
    }

    public function getRole()
    {
        $role = $this->db->get('user_rule');
        return $role->result_array();
    }

    public function addNewRole()
    {
        $data = [
            'rule' => $this->input->post('role', true)
        ];
        $this->db->insert('user_rule', $data);
    }

    public function getMenu()
    {
        $this->db->where('id !=', 1);
        $menu = $this->db->get('user_menu');
        return $menu->result_array();
    }

    public function getRoleById($id)
    {
        $role = $this->db->get_where('user_rule', ['id' => $id]);
        return $role->row_array();
    }
}
