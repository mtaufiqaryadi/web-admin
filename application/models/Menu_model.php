<?php

class Menu_model extends CI_Model
{
    public function getDataByEmail($email)
    {
        $user = $this->db->get_where('user', ['email' => $email]);
        return $user->row_array();
    }

    public function getMenu()
    {
        $menu = $this->db->get('user_menu');
        return $menu->result_array();
    }

    public function getSubMenu()
    {
        $query = "   SELECT `user_sub_menu`.*,`user_menu`.`menu`
                        FROM `user_sub_menu` JOIN `user_menu`
                            ON `user_sub_menu`.`menu_id` = `user_menu`.`id`       
        ";
        $submenu = $this->db->query($query);
        return $submenu->result_array();
    }

    public function addNewMenu()
    {
        $data = [
            'menu' => $this->input->post('menu', true)
        ];
        $this->db->insert('user_menu', $data);
    }

    public function addNewSubMenu()
    {
        $data = [
            'menu_id' => $this->input->post('menu_id'),
            'title' => $this->input->post('title'),
            'url' => $this->input->post('url'),
            'icon' => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
        ];

        $this->db->insert('user_sub_menu', $data);
    }

    public function deleteMenuById($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user_menu');
    }

    public function deleteSubMenuById($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user_sub_menu');
    }
}
