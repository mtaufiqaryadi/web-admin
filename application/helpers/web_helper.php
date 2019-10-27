<?php

function is_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('email')) {
        redirect('auth');
    } else {
        $role_id = $ci->session->userdata('rule_id');
        $menu = $ci->uri->segment(1);
        $query = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
        $menu_id = $query['id'];
        $userAccess = $ci->db->get_where('user_access_menu', ['rule_id' => $role_id, 'menu_id' => $menu_id]);

        if ($userAccess->num_rows() < 1) {
            redirect('auth/blocked');
        }
    }
}

function check_access($rule_id, $menu_id)
{
    $ci = get_instance();
    $result = $ci->db->get_where('user_access_menu', ['rule_id' => $rule_id, 'menu_id' => $menu_id]);
    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }
}
