<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Jakarta");
class TokoHubModel extends CI_Model
{
    public function getTokoAdmin()
    {
        $data = $this->db->query("SELECT id, name, address, phone, photo FROM Toko ORDER BY name")->result('array');
        return $data == null ? 'Empty' : $data;
    }

    public function editTokoAdmin($data)
    {
        return $this->db->update('Toko', $data, array('id' => $data['id']));
    }

    public function addToko($data)
    {
        return $this->db->insert('Toko', $data);
    }

    public function deleteToko($id)
    {
        return $this->db->delete('Toko', array('id' => $id));
    }
}
