<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Admin extends RestController
{
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
		header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
		parent::__construct();
		$this->load->model('MasterModel');
	}

	public function index_get()
	{
		$id = $this->get('id');
		if ($id == null) {
			$response = $this->MasterModel->getMaster();
			if ($response) {
				$this->response([
					'status' => true,
					'message' => $response
				], RestController::HTTP_OK);
			} else {
				$this->response([
					'status' => true,
					'message' => "Empty"
				], RestController::HTTP_OK);
			}
		} else {
			$response = $this->MasterModel->getMaster($id);
			if ($response) {
				$this->response([
					'status' => true,
					'message' => $response
				], RestController::HTTP_OK);
			} else {
				$this->response([
					'status' => false,
					'message' => "Error"
				], RestController::HTTP_BAD_REQUEST);
			}
		}
	}

	public function index_post()
	{
		$data = array(
			'name'		=> $this->post('name'),
			'username'	=> $this->post('username'),
			'password'	=> password_hash($this->post('password'), PASSWORD_BCRYPT),
			'max_dojo'	=> $this->post('max_dojo'),
			'expired'	=> $this->post('expired'),
			'phone'		=> $this->post('phone')
		);

		$response = $this->MasterModel->newMaster($data);

		if ($response) {
			$this->response([
				'status' => true,
				'message' => "Success"
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function editExpiration_post()
	{
		$expired = $this->post('expired');
		$id_master = $this->post('id_master');

		$response = $this->MasterModel->editExpiration($expired, $id_master);

		if ($response) {
			$this->response([
				'status' => true,
				'message' => "Success"
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function editUnixKey_post()
	{
		$unix_key = $this->post('unix_key');
		$id_master = $this->post('id_master');

		$response = $this->MasterModel->editUnixKey($unix_key, $id_master);

		if ($response) {
			$this->response([
				'status' => true,
				'message' => "Success"
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function editPassword_post()
	{
		$password = password_hash($this->post('password'), PASSWORD_BCRYPT);
		$id_master = $this->post('id_master');

		$response = $this->MasterModel->editPassword($password, $id_master);

		if ($response) {
			$this->response([
				'status' => true,
				'message' => "Success"
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function editActivate_post()
	{
		$status = $this->post('status');
		$id_master = $this->post('id_master');

		$response = $this->MasterModel->editActivate($status, $id_master);

		if ($response) {
			$this->response([
				'status' => true,
				'message' => "Success"
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function getAllDojo_get()
	{
		$id_master = $this->get('id_master');
		if ($id_master != null) {
			$response = $this->MasterModel->getAllDojo($id_master);
			if ($response) {
				$this->response([
					'status' => true,
					'message' => $response
				], RestController::HTTP_OK);
			} else {
				$this->response([
					'status' => true,
					'message' => "Empty"
				], RestController::HTTP_OK);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => "Error"
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function index_delete($id_master = null)
	{
		if ($id_master != null) {
			if ($this->MasterModel->deleteMaster($id_master) > 0) {
				$this->response([
					'status' => true,
					'id' => $id_master,
					'message' => 'Deleted'
				], RestController::HTTP_OK);
			} else {
				$this->response([
					'status' => false,
					'message' => 'ID Not Found'
				], RestController::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Provide an ID'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function isUsernameUnix_get()
	{
	    $username = $this->get("username");
		if ($username != null) {
		    $result = $this->MasterModel->isUsernameUnix($username);
			if ($result) {
				$this->response([
					'status' => true,
					'message' => true
				], RestController::HTTP_OK);
			} else {
				$this->response([
					'status' => true,
					'message' => false
				], RestController::HTTP_OK);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function isAddAvailable_get()
	{
	    $id_master = $this->get("id_master");
		if ($id_master != null) {
		    $result = $this->MasterModel->isAddAvailable($id_master);
			$this->response([
				'status' => true,
				'message' => $result
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function changePassword_post()
    {
        $username = $this->post("username");
        $password = $this->post('password');
        if ($username != null && $password != null) {
            $result = $this->MasterModel->editPasswordAdmin(password_hash($password, PASSWORD_BCRYPT), $username);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => "Success"
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Error"
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function editAbout_post() {
        $id = $this->post("id");
        $data = $this->post("about");
        
        if ($id != null && $data != null) {
            
            $this->MasterModel->editAbout($id,$data);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error Data"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
