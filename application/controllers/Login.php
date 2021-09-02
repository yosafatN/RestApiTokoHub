<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Login extends RestController
{
	public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
        parent::__construct();
        $this->load->model('LoginModel');
    }

    public function index_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        if ($username != null && $password != null) {
            if ($this->verifyPassword($username, $password)) {
                $checkExpiration = $this->LoginModel->isMasterActive($username);
                
                if (substr($username, 0, 2) == 'A-') {
                    $this->response([
                        'status' => true,
                        'message' => "Success",
                    ], RestController::HTTP_OK);
                }
                
                else if ($checkExpiration['status']) {
                    if (substr($username, 0, 2) == 'M-') {
                        $id = $this->LoginModel->getIDMaster($username);
                        $this->response([
                            'status' => true,
                            'message' => "Success",
                            'id' => $id
                        ], RestController::HTTP_OK);
                    } else {
                        $role = $this->LoginModel->getRole($username);
                        
                        $id_member = $this->LoginModel->getIDMember($username);
                        $id_dojo = $this->LoginModel->getIDDojo($id_member);
                        $id_master = $this->LoginModel->getIDMasterByIDMember($id_member);
                        
                        $this->response([
                            'status' => true,
                            'message' => "Success",
                            'role' => $role,
                            'id' => $id_master,
                            'id_dojo' => $id_dojo,
                            'id_member' => $id_member
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => true,
                        'message' => $checkExpiration['message']
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => true,
                    'message' => "Username or Password Incorrect"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function verifyPassword($username, $password)
    {
        $password_hash = null;
        if (substr($username,0,2) == 'M-') {
            $password_hash = $this->LoginModel->getPasswordMaster($username);
        }
        else if (substr($username,0,2) == 'A-') {
            $password_hash = $this->LoginModel->getPasswordAdmin($username);
        }
        else {
            $password_hash = $this->LoginModel->getPasswordMember($username);
        }
        
        if ($password_hash != null) {
            return password_verify($password, $password_hash);
        }
        else {
            return false;
        }
    }
    
    public function getDojoByCode_get() {
        $code = $this->get("code");
        
        if ($code != null) {
            $result = $this->LoginModel->getDojoByCode($code);
            
            if ($result == null) {
                $this->response([
                    'status' => true,
                    'id_dojo' => null,
                    'message' => "Dojo not found"
                ], RestController::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => true,
                    'id_dojo' => $result['id_dojo'],
                    'name' => $result['name'],
                    'message' => 'Sucess'
                ], RestController::HTTP_OK);
            }
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getDojoByID_get() {
        $id_dojo = $this->get("id_dojo");
        
        if ($id_dojo != null) {
            $result = $this->LoginModel->getDojoByID($id_dojo);
            
            if ($result == null) {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            }
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function addJoinRequest_post() {
        $id_dojo = $this->post("id_dojo");
        $name = $this->post("name");
        $username = $this->post("username");
        $password = $this->post("password");
        $birth_date = $this->post("birth_date");
        $phone1 = $this->post("phone1");
        $phone2 = $this->post("phone2");
        $address = $this->post("address");
        $place_of_birth = $this->post("place_of_birth");
        $schedule = $this->post("schedule");
        
        if ($id_dojo != null && $name != null && $username != null && $password != null && $birth_date != null && $phone1 != null && $phone2 != null && $address != null && $schedule != null && $place_of_birth != null) {
            
            if ($phone2 == "-") {
                $phone2 = null;
            }
            else if ($place_of_birth == "-") {
                $place_of_birth = null;
            }
            
            $data = array(
                'id_dojo' => $id_dojo,
                'name' => $name,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'birth_date' => $birth_date,
                'phone1' => $phone1,
                'phone2' => $phone2,
                'address' => $address,
                'place_of_birth' => $place_of_birth);
            
            $this->LoginModel->addJoinRequest($data,$schedule);
            
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error1"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getAbout_get() {
        $data = $this->LoginModel->getAbout();
        if ($data != null) {
            $this->response([
                'status' => true,
                'message' => $data[0]
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}