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

    // PRODUCT
    public function getProduct($id_toko)
    {
        $data = $this->db->order_by('name', 'ASC')->get_where('product', array('id_toko' => $id_toko))->result('array');
        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function addProduct($data)
    {
        return $this->db->insert('product', $data);
    }

    public function editProduct($data)
    {
        return $this->db->update('product', $data, array('id' => $data['id']));
    }

    public function deleteProduct($id)
    {
        return $this->db->delete('product', array('id' => $id));
    }

    //EMPLOYEE
    public function getEmployee($id_toko)
    {
        $data = $this->db->order_by('name', 'ASC')->get_where('employee', array('id_toko' => $id_toko))->result('array');
        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function addEmployee($data)
    {
        return $this->db->insert('employee', $data);
    }

    public function editEmployee($data)
    {
        return $this->db->update('employee', $data, array('id' => $data['id']));
    }

    public function deleteEmployee($id)
    {
        return $this->db->delete('employee', array('id' => $id));
    }

    //SUPPLIER
    public function getSupplier($id_toko)
    {
        $data = $this->db->order_by('name', 'ASC')->get_where('supplier', array('id_toko' => $id_toko))->result('array');
        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function addSupplier($data)
    {
        return $this->db->insert('supplier', $data);
    }

    public function editSupplier($data)
    {
        return $this->db->update('supplier', $data, array('id' => $data['id']));
    }

    public function deleteSupplier($id)
    {
        return $this->db->delete('supplier', array('id' => $id));
    }

    //ATTENDANCE
    public function getAttendanceToko($id_toko, $startDate, $endDate)
    {
        $data = $this->db->query("SELECT id AS id_employee_parent, name, photo, (SELECT COUNT(attendance.id_employee) FROM attendance JOIN presence ON attendance.id_presence = presence.id WHERE attendance.id_employee = id_employee_parent AND attendance.status = 1 AND presence.date BETWEEN '$startDate' AND '$endDate') AS present, (SELECT COUNT(attendance.id_employee) FROM attendance JOIN presence ON attendance.id_presence = presence.id WHERE attendance.id_employee = id_employee_parent AND attendance.status = 0 AND presence.date BETWEEN '$startDate' AND '$endDate') AS absent FROM employee WHERE id_toko = $id_toko ORDER BY name")->result('array');
        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function getAttendanceEmployee($id_employee, $startDate, $endDate)
    {
        $data = $this->db->query("SELECT attendance.id, presence.date, attendance.status FROM attendance JOIN employee ON attendance.id_employee = employee.id JOIN presence ON attendance.id_presence = presence.id WHERE attendance.id_employee = $id_employee AND presence.date BETWEEN '$startDate' AND '$endDate' ORDER BY presence.date DESC")->result('array');
        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function getAttendanceHistory($id_toko, $date)
    {
        $data = $this->db->query("SELECT employee.id, employee.name, employee.photo, attendance.status FROM employee JOIN attendance ON employee.id = attendance.id_employee JOIN presence ON presence.id = attendance.id_presence  WHERE presence.date = '$date' AND presence.id_toko = $id_toko ORDER BY employee.name")->result('array');

        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function getPresenceStatus($id_toko, $date)
    {
        $presence = $this->db->query("SELECT id FROM presence WHERE date = '$date' AND id_toko = $id_toko")->result('array');
        $result = array();
        if ($presence == null) {
            $data = $this->db->query("SELECT id, name, photo FROM employee WHERE id_toko = $id_toko ORDER BY name")->result('array');
            $result = array('status' => 0, 'data' => $data);
        } else {
            $id_presence = $presence[0]['id'];
            $data = $this->db->query("SELECT attendance.id, employee.name, employee.photo, attendance.status FROM attendance JOIN employee ON attendance.id_employee = employee.id JOIN presence ON attendance.id_presence = presence.id WHERE presence.id = $id_presence ORDER BY employee.name")->result('array');
            $result = array('status' => 1, 'data' => $data);
        }
        return $result;
    }

    public function submitPresence($id_toko, $date, $data)
    {
        $this->db->insert('presence', array('id_toko' => $id_toko, 'date' => $date));
        $id = $this->db->insert_id();

        $temp = array();
        foreach ($data as $attendance) {
            $attendance['id_presence'] = $id;
            array_push($temp, $attendance);
        }

        return $this->db->insert_batch('attendance', $temp);
    }

    public function updatePresence($data)
    {
        return $this->db->update_batch('attendance', $data, 'id');
    }

    //STOCK
    public function getStock($id_toko, $startDate, $endDate)
    {
        $data = $this->db->query("SELECT stock.id, supplier.name, supplier.photo, stock.date FROM stock JOIN supplier ON stock.id_supplier = supplier.id WHERE supplier.id_toko = $id_toko AND stock.date BETWEEN '$startDate' AND '$endDate'")->result('array');

        if ($data == null) {
            return false;
        } else {
            return $data;
        }
    }

    public function addStock($id_toko, $data)
    {
    }
}
