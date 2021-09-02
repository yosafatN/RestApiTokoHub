<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Jakarta");
class LoginModel extends CI_Model
{
    public function getPasswordMaster($username)
    {
        $result = $this->db->query("SELECT password from master WHERE username = '$username'")->result('array');
        if ($result != null) {
            return $result[0]['password'];
        }
        else {
            return null;
        }
    }
    
    public function getPasswordMember($username)
    {
        $result =  $this->db->query("SELECT password from member WHERE username = '$username'")->result('array');
        
        if ($result != null) {
            return $result[0]['password'];
        }
        else {
            return null;
        }
    }
    
    public function getPasswordAdmin($username)
    {
        $result = $this->db->query("SELECT password from admin WHERE username = '$username'")->result('array');
        
        if ($result != null) {
            return $result[0]['password'];
        }
        else {
            return null;
        }
    }
    
    public function getRole($username)
    {
        $role =  $this->db->query("SELECT role.name from member join role on member.id_role = role.id WHERE username = '$username'")->result('array');
        if ($role != null) {
            return $role[0]['name'];
        }
        else {
            return null;
        }
    }
    
    public function getIDMember($username)
    {
        $id_member =  $this->db->query("SELECT id_member from member WHERE username = '$username'")->result('array');
        if ($id_member != null) {
            return $id_member[0]['id_member'];
        }
        else {
            return null;
        }
    }
    
    public function getIDMaster($username)
    {
        $id_master =  $this->db->query("SELECT id_master from master WHERE username = '$username'")->result('array');
        if ($id_master != null) {
            return $id_master[0]['id_master'];
        }
        else {
            return null;
        }
    }
    
    public function getIDMasterByIDMember($id_member)
    {
        $id_master =  $this->db->query("SELECT dojo.id_master FROM member JOIN dojo ON member.id_dojo = dojo.id_dojo WHERE member.id_member = $id_member")->result('array');
        if ($id_master != null) {
            return $id_master[0]['id_master'];
        }
        else {
            return null;
        }
    }
    
    public function getIDDojo($id_member)
    {
        $id_dojo =  $this->db->query("SELECT id_dojo from member WHERE id_member = '$id_member'")->result('array');
        if ($id_dojo != null) {
            return $id_dojo[0]['id_dojo'];
        }
        else {
            return null;
        }
    }
    
    public function isMasterActive($username)
    {
        $id_master = -1;
        $role = substr($username, 0, 2);
        if ($role == 'M-') {
            $id_master = $this->db->query("SELECT id_master FROM master WHERE username = '$username'")->result('array')[0]["id_master"];
        } else if ($role == 'A-') {
            return true;
        } else {
            $id_master = $this->db->query("SELECT dojo.id_master FROM master JOIN dojo ON dojo.id_master = master.id_master JOIN member ON dojo.id_dojo = member.id_dojo WHERE member.username = '$username'")->result('array')[0]["id_master"];
        }

        $isActive = $this->db->query("SELECT status FROM master WHERE id_master = $id_master")->result('array')[0];

        if ($isActive['status'] == 0) {
            $isExpired = $this->db->query("SELECT expired FROM master WHERE id_master = $id_master")->result('array')[0]['expired'];

            if ($isExpired > date('Y-m-d', time())) {
                if ($role == 'M-') {
                    return array(
                        'status' => true,
                        'message' => 'Success 1'
                    );
                } else {
                    $id_member = $this->db->query("SELECT id_member FROM member WHERE username = '$username'")->result('array')[0]['id_member'];

                    $id_dojo = $this->db->query("SELECT id_dojo FROM member WHERE id_member = $id_member")->result('array')[0]['id_dojo'];
                    
                    $this->setPeriodDojo($id_dojo);
                    
                    $isDojoActive = $this->db->query("SELECT isActive FROM dojo WHERE id_dojo = $id_dojo")->result('array')[0]['isActive'];
                    
                    if ($isDojoActive == 1) {
                        $isMemberExpired = $this->db->query("SELECT expiration FROM member WHERE id_member = $id_member")->result('array')[0]['expiration'];
    
                        if ($isMemberExpired ==  null) {
                            return array(
                                'status' => true,
                                'message' => 'Success 2'
                            );
                        } else {
                            if ($isMemberExpired > date('Y-m-d', time())) {
                                return array(
                                    'status' => true,
                                    'message' => 'Success 3'
                                );
                            } else {
                                return array(
                                    'status' => false,
                                    'message' => 'Member Expired'
                                );
                            }
                        }
                    }
                    else {
                        return array(
                            'status' => false,
                            'message' => 'The dojo is inactive'
                        );
                    }
                }
            } else {
                return array(
                    'status' => false,
                    'message' => 'Master Expired'
                );
            }
        } else {
            return array(
                'status' => false,
                'message' => 'Master Not Active'
            );
        }
    }
    
    public function getDojoByCode ($code) {
        $getDojo = $this->db->query("SELECT id_dojo, name FROM dojo WHERE request_code = '$code'")->result('array');
        
        if ($getDojo != null) {
            return $getDojo[0];
        }
        else {
            return null;
        }
    }
    
    public function getDojoByID ($id_dojo) {
        return $this->db->query("SELECT id_schedule, day, time FROM dojo_schedule WHERE id_dojo = $id_dojo ORDER BY day, time")->result('array');
    }
    
    public function addJoinRequest($data,$schedules) {
        $this->db->insert('join_request',$data);
        $id = $this->db->insert_id();
        
        foreach($schedules as $schedule) {
            $this->db->insert('join_request_schedule', array('id_request' => $id, 'id_schedule' => $schedule['id_schedule']));
        }
    }
    
    public function setPeriodDojo($id_dojo)
    {
        $data = $this->db->query("SELECT period, isActive FROM dojo WHERE id_dojo = $id_dojo")->result('array');
        $period = $data[0]['period'];
        $isActive = $data[0]['isActive'];
        
        if ($isActive == 1) {
            $threshold = date('Y-m-d', strtotime("+1 year", strtotime($period)));

            if (date('Y-m-d') >= $threshold) {
                $datetime = $period . ' 00:00:00';
                $check = $this->db->query("SELECT exam.datetime, exam.id_exam FROM participant JOIN member ON participant.id_member = member.id_member JOIN exam ON participant.id_exam = exam.id_exam WHERE member.id_dojo = $id_dojo AND exam.datetime > '$datetime' GROUP BY exam.id_exam")->result('array');

                if ($check == null) {
                    $this->db->query("UPDATE dojo SET isActive = 0 WHERE id_dojo = $id_dojo");
                } else {
                    if (count($check) < 2) {
                        $this->db->query("UPDATE dojo SET isActive = 0 WHERE id_dojo = $id_dojo");
                    } else {
                        $this->db->query("UPDATE dojo SET period = '$threshold' WHERE id_dojo = $id_dojo");
                    }
                }
            }
        }
    }
    
    public function getAbout() {
        return $this->db->query("SELECT * FROM about")->result('array');
    }
}