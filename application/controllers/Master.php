<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Master extends RestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
        parent::__construct();
        $this->load->model('MasterModel');
    }

    //Profile
    public function index_get()
    {
        $id_master = $this->get("id_master");
        if ($id_master != null) {
            $result = $this->MasterModel->getMaster($id_master);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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

    public function index_post()
    {
        $id_master = $this->post("id_master");
        $name = $this->post('name');
        $phone = $this->post('phone');
        $dojo_name = $this->post('dojo_name');
        $up_rank = $this->post('up_rank');
        $low_rank = $this->post('low_rank');
        if ($id_master != null && $name != null && $phone != null && $dojo_name != null && $up_rank != null && $low_rank != null) {
            $data = array(
                'name' => $name,
                'phone' => $phone,
                'dojo_name' => $dojo_name,
                'up_rank' => $up_rank,
                'low_rank' => $low_rank);
            
            $result = $this->MasterModel->editProfile($id_master,$data);
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

    public function changePassword_post()
    {
        $id_master = $this->post("id_master");
        $password = $this->post('password');
        $newPassword = $this->post('new_password');
        if ($id_master != null && $password != null) {
            if ($this->verifyPassword($id_master, $password)) {
                $result = $this->MasterModel->editPassword(password_hash($newPassword, PASSWORD_BCRYPT), $id_master);
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
                    'status' => true,
                    'message' => "Password Incorrect"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function verifyPassword($id_master, $password)
    {
        $password_hash = $this->MasterModel->getPassword($id_master);
        $string = array_values($password_hash[0]);
        return password_verify($password, $string[0]);
    }
    
    //List Dojo
    public function getAllDetailDojo_get() {
        $id_master = $this->get("id_master");
        if ($id_master != null) {
            $result = $this->MasterModel->getAllDetailDojo($id_master);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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
    
    public function getDetailDojo_get() {
        $id_dojo = $this->get("id_dojo");
        if ($id_dojo != null) {
            $result = $this->MasterModel->getDojo($id_dojo);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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
    
    public function getAllMemberForEdit_get() {
        $id_master = $this->get("id_master");
        $id_dojo = $this->get("id_dojo");
        if ($id_master != null && $id_dojo != null) {
            $result = $this->MasterModel->getAllMemberForEdit($id_master,$id_dojo);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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
    
    public function getAllMemberForAdd_get() {
        $id_master = $this->get("id_master");
        if ($id_master != null) {
            $result = $this->MasterModel->getAllMemberForAdd($id_master);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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
    
    public function getAllInstructorForAdd_get() {
        $id_master = $this->get("id_master");
        if ($id_master != null) {
            $result = $this->MasterModel->getAllInstructorForAdd($id_master);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result
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
    
    public function getRank_get() {
        $result = $this->MasterModel->getRank();
        if ($result) {
            $this->response([
                'status' => true,
                'message' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    private function checkIfStillAdministrator($data, $id_check)
    {
        foreach ($data as $value) {
            if ($value == $id_check) {
                return true;
            }
        }
        return false;
    }
    
    public function editDojo_post() {
        $id_dojo = $this->post("id_dojo");
        $id_head = $this->post("id_head");
        $id_treasurer = $this->post("id_treasurer");
        $id_secretary = $this->post("id_secretary");
        $id_assistant = $this->post("id_assistant");
        
        $data_id = array($id_head, $id_treasurer, $id_secretary, $id_assistant);
        
        $data_before = $this->MasterModel->getAdministrator($id_dojo)[0];
        
        $id_head_before = $data_before['id_head'];
        $id_treasurer_before = $data_before['id_treasurer'];
        $id_secretary_before = $data_before['id_secretary'];
        $id_assistant_before = $data_before['id_assistant'];
        
        if ($id_head != $id_head_before) {
            $this->MasterModel->changeRole($id_head,1);
            //bukan membuat member baru, tetapi mengambil member yang sudah ada
            if ($id_head_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id,$id_head_before)) {
                    $this->MasterModel->changeRole($id_head_before,6);
                }
                $this->MasterModel->memberChangeDojo($id_dojo,$id_head);
            }
        }
        if ($id_treasurer != $id_treasurer_before) {
            $this->MasterModel->changeRole($id_treasurer,2);
            if ($id_treasurer_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id,$id_treasurer_before)) {
                    $this->MasterModel->changeRole($id_treasurer_before,6);
                };
                $this->MasterModel->memberChangeDojo($id_dojo,$id_treasurer);
            }
        }
        if ($id_secretary != $id_secretary_before) {
            $this->MasterModel->changeRole($id_secretary,3);
            if ($id_secretary_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id,$id_secretary_before)) {
                    $this->MasterModel->changeRole($id_secretary_before,6);
                };
                $this->MasterModel->memberChangeDojo($id_dojo,$id_secretary);
            }
        }
        if ($id_assistant != $id_assistant_before) {
            $this->MasterModel->changeRole($id_assistant,5);
            if ($id_assistant_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id,$id_assistant_before)) {
                    $this->MasterModel->changeRole($id_assistant_before,6);
                };
                $this->MasterModel->memberChangeDojo($id_dojo,$id_assistant);
            }
        }
        
        $name = $this->post("name");
        $request_code = $this->post("request_code");
        $fee1 = $this->post("fee1");
        $fee2 = $this->post("fee2");
        $fee3 = $this->post("fee3");
        $address = $this->post("address");
        $email = $this->post("email");
        $pengprov = $this->post("pengprov");
        $pengcab = $this->post("pengcab");
        
        if ($email == '') {
            $email = null;
        }
        if ($pengprov == '') {
            $pengprov = null;
        }
        if ($pengcab == '') {
            $pengcab = null;
        }
        
        $array = array(
            'name' => $name,
            'request_code' => $request_code,
            'fee1' => $fee1,
            'fee2' => $fee2,
            'fee3' => $fee3,
            'address' => $address,
            'email' => $email,
            'pengprov' => $pengprov,
            'pengcab' => $pengcab
        );
        
        $this->HeadModel->editDojo($id_dojo,$array);
        
        $this->response([
            'status' => true,
            'message' => "Success"
        ], RestController::HTTP_OK);
    }
    
    public function addNewDojo_post()
    {
        $id_master = $this->post("id_master");
        $name = $this->post('name');
        $id_head = $this->post('id_head');
        $id_instructor = $this->post('id_instructor');
        
        if ($id_master != null && $name !=null && $id_head != null && $id_instructor != null) {
            
            if ($this->MasterModel->isAddAvailable($id_master)) {
                if ($this->MasterModel->isDojoNameUnix($name,$id_master)) {
                    $isNewInstructor = false;
                    
                    if ($id_head == -1) {
                        $data = array(
                            'name'      => $this->post("head_name"),
                            'username'  => $this->post("head_username"),
                            'password'  => password_hash($this->post('head_password'), PASSWORD_BCRYPT),
                            'expiration'=> date('Y-m-d', strtotime('+90 days')),
                            'id_rank'   => $this->post("head_id_rank"),
                            'id_role'   => 1
                        );
                        $id_head = $this->MasterModel->addMember($data,$this->post("head_username"));
                    }
                    else {
                        $this->MasterModel->changeRole($id_head,1);
                    }
                    if ($id_instructor == -1) {
                        $data = array(
                            'name'      => $this->post("instructor_name"),
                            'username'  => $this->post("instructor_username"),
                            'password'  => password_hash($this->post('instructor_password'), PASSWORD_BCRYPT),
                            'expiration'=> date('Y-m-d', strtotime('+90 days')),
                            'id_rank'   => $this->post("instructor_id_rank"),
                            'id_role'   => 4
                        );
                        
                        $id_instructor = $this->MasterModel->addMember($data,$this->post("instructor_username"));
                        
                        $isNewInstructor = true;
                    }
                    else {
                        $this->MasterModel->changeRole($id_instructor,4);
                    }
                    
                    $activeMember = 0;
                    if ($isNewInstructor) {
                        $activeMember = 2;
                    }
                    else {
                        $activeMember = 1;
                    }
                    
                    $data_dojo = array(
                            'name' => $name,
                            'id_head' => $id_head,
                            'id_instructor' => $id_instructor,
                            'id_master' => $id_master,
                            'active_member' => $activeMember
                        );
                    
                    $id_dojo = $this->MasterModel->addNewDojo($data_dojo,$name,$id_master);
                    
                    $this->MasterModel->memberChangeDojo($id_dojo,$id_head);
                    
                    if($isNewInstructor) {
                        $this->MasterModel->memberChangeDojo($id_dojo,$id_instructor);
                    }
                    
                    $this->response([
                        'status' => true,
                        'message' => "Success"
                    ], RestController::HTTP_OK);
                }
                else {
                    $this->response([
                        'status' => true,
                        'message' => "Name already in use"
                    ], RestController::HTTP_OK);
                }
            }
            else {
                $this->response([
                    'status' => true,
                    'message' => "Capacity is full. Contact admin to increase capacity"
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
    
    public function isNameUnix_get() {
        $id_master = $this->get("id_master");
        $name = $this->get("name");
        if ($name != null && $id_master != null) {
            $result = $this->MasterModel->isDojoNameUnix($name,$id_master);
            $this->response([
                'status' => true,
                'message' => $result
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error" 
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function isMemberUsernameUnix_get() {
        $username = $this->get("username");
        if ($username != null) {
            $result = $this->MasterModel->isMemberUsernameUnix($username);
            $this->response([
                'status' => true,
                'message' => $result
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => "Error" 
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function deleteDojo_delete($id_master = null, $id_dojo = null)
	{
		if ($id_master != null && $id_dojo != null) {
		    $this->MasterModel->deleteDojoByDojoID($id_master,$id_dojo);
			$this->response([
				'status' => true,
				'message' => 'Deleted'
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Provide an ID'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function editMapDojo_post()
	{
	    $id_dojo = $this->post("id_dojo");
	    $latitude = $this->post("latitude");
	    $longitude = $this->post("longitude");
	    
	    if ($id_dojo != null && $latitude != null && $longitude != null) {
		    $this->MasterModel->updateMapDojo($id_dojo,$latitude,$longitude);
			$this->response([
				'status' => true,
				'message' => 'Success'
			], RestController::HTTP_OK);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function getDetailMember_get()
	{
	    $id_dojo = $this->get("id_dojo");
	    
	    if ($id_dojo != null) {
		    $result = $this->MasterModel->getDetailMember($id_dojo);
			if ($result != null) {
			    $this->response([
    				'status' => true,
    				'message' => $result
    			], RestController::HTTP_OK);
			}
			else {
			    $this->response([
				'status' => false,
    				'message' => 'Error'
    			], RestController::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function getCurriculum_get()
	{
	    $id_master = $this->get("id_master");
	    
	    if ($id_master != null) {
		    $result = $this->MasterModel->getCurriculum($id_master);
			if ($result != null) {
			    $this->response([
    				'status' => true,
    				'message' => $result
    			], RestController::HTTP_OK);
			}
			else {
			    $this->response([
				    'status' => true,
    				'message' => 'Empty'
    			], RestController::HTTP_OK);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function addCurriculum_post()
    {
        $id_master = $this->post('id_master');
        $rank = $this->post('id_rank');
        $link = $this->post('link');

        if ($id_master != null && $rank != null) {
            $data = array(
                'id_master' => $id_master,
                'id_rank' => $rank,
                'link' => $link == null ? null : $link
            );

            $this->MasterModel->addCurriculum($data);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function deleteCurriculum_delete($id_curriculum = null)
    {
        if ($id_curriculum != null) {
            $this->MasterModel->deleteCurriculum($id_curriculum);
            $this->response([
                'status' => true,
                'message' => 'Deleted'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getContent_get()
    {
        $id_curriculum = $this->get("id_curriculum");

        if ($id_curriculum != null) {
            $result = $this->MasterModel->getContent($id_curriculum);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function addContent_post()
    {
        $id_curriculum = $this->post("id_curriculum");
        $content = $this->post("content");

        if ($id_curriculum != null && $content != null) {
            $data = array(
                'id_curriculum' => $id_curriculum,
                'content' => $content
            );

            $this->MasterModel->addContent($data);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function editContent_post()
    {
        $id_content = $this->post("id_content");
        $content = $this->post("content");

        if ($id_content != null && $content != null) {
            $this->MasterModel->editContent(array('content' => $content), $id_content);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function deleteContent_delete($id_content = null)
    {
        if ($id_content != null) {
            $this->MasterModel->deleteContent($id_content);
            $this->response([
                'status' => true,
                'message' => 'Deleted'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function editLink_post()
    {
        $id_curriculum = $this->post("id_curriculum");
        $link = $this->post("link");

        if ($id_curriculum != null && $link != null) {
            
            if ($link == '-') {
                $link = null;
            }
            
            $this->MasterModel->editLink($id_curriculum, $link);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    //TRANSFER
    public function getTransfer_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getTransfer($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function addTransfer_post()
    {
        $id_master = $this->post("id_master");
        $id_member = $this->post("id_member");
        $id_dojo_from = $this->post("id_dojo_from");
        $id_dojo_to = $this->post("id_dojo_to");
        $date = $this->post("date");

        if ($id_master != null && $id_member != null && $id_dojo_from != null && $id_dojo_to != null && $date != null) {
            $data = array(
                'id_master' => $id_master,
                'id_member' => $id_member,
                'id_dojo_from' => $id_dojo_from,
                'id_dojo_to' => $id_dojo_to,
                'date' => $date
            );

            $this->MasterModel->addTransfer($data);
            $this->MasterModel->memberChangeDojo($id_dojo_to,$id_member);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getDojoForSpinner_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getDojoForSpinner($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getMemberForTransfer_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {
            $result = $this->MasterModel->getMemberForTransfer($id_dojo);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    //List Instructor
    public function getAllInstructor_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getAllInstructor($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getInstructorDojo_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {
            $result = $this->MasterModel->getInstructorDojo($id_member);
            if ($result != null) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getDojoAndInstructor_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getDojoAndInstructor($id_master);
            if ($result != null) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function editInstructorDojo_post()
    {
        $id_new_instructor = $this->post("id_new_instructor");
        $id_dojo = $this->post("id_dojo");

        if ($id_new_instructor != null && $id_dojo != null) {
            $this->MasterModel->editDojoInstpector($id_dojo, $id_new_instructor);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function uploadDojoPhoto_post()
    {
        $id_dojo = $this->post("id_dojo");
        $id_master = $this->post("id_master");
        $position = $this->post("position");
        
        $milliseconds = round(microtime(true) * 1000);
        
        if ($id_dojo != null && $id_master != null && $position != null) {
            //FILE PATH
            $filename = $position. '-' . $milliseconds . '.' . 'png';
            $path = "photo_dojo/$id_master/$id_dojo/";
            $filePath = $path . $filename;
            
            // if (file_exists($filePath)) {
            //     chown($filePath, 666);
            //     unlink(realpath($filePath));
            // }
    
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, TRUE)) {
                    $this->response([
                        'status' => true,
                        'message' => 'Error Make Path'
                    ], RestController::HTTP_OK);
                }
            }
    
            //UPLOAD
            $config = array(
                'upload_path' => $path,
                'allowed_types' => "gif|jpg|png|jpeg",
                'max_size' => '10024',
                'max_width' => '6000',
                'max_height' => '6000',
                'file_name' => $filename
            );
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image')) {
                $this->response([
                    'status' => true,
                    'message' => $this->upload->display_errors()
                ], RestController::HTTP_OK);
            }
    
            $success = $this->MasterModel->uploadDojoPhoto($position, $filePath, $id_dojo);
            if ($success) {
                $this->response([
                    'status' => true,
                    'message' => 'Success'
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Error'
                ], RestController::HTTP_BAD_REQUEST);
            }
        }
        else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getAttendance_get()
    {
        $id_dojo = $this->get("id_dojo");
        $date_min = $this->get("date_min");
        $date_max = $this->get("date_max");

        if ($date_max != null) {
            $date_max = $date_max . ' 23:59:59';
        }

        if ($id_dojo != null) {
            $result = $this->MasterModel->getAttendance($id_dojo, $date_min, $date_max);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getAttendanceList_get()
    {
        $id_presence = $this->get("id_presence");

        if ($id_presence != null) {
            $result = $this->MasterModel->getAttendanceList($id_presence);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getNote_get()
    {
        $id_presence = $this->get("id_presence");

        if ($id_presence != null) {
            $result = $this->MasterModel->getNote($id_presence);
            if ($result != null) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getEvent_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getEvent($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function addEvent_post()
    {
        $id_master = $this->post("id_master");
        $datetime = $this->post("datetime");
        $description = $this->post("description");

        if ($id_master != null && $datetime != null && $description != null) {
            
            $array = array(
                'id_master' => $id_master,
                'datetime' => $datetime,
                'descrip' => $description);
            
            $this->MasterModel->addEvent($array);
            
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function editEvent_post()
    {
        $id_event = $this->post("id_event");
        $datetime = $this->post("datetime");
        $description = $this->post("description");

        if ($id_event != null && $datetime != null && $description != null) {
            
            $array = array(
                'datetime' => $datetime,
                'descrip' => $description);
            
            $this->MasterModel->editEvent($id_event,$array);
            
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function deleteEvent_delete($id_event) {
        if ($id_event != null) {
            $this->MasterModel->deleteEvent($id_event);
            $this->response([
                'status' => true,
                'message' => 'Deleted'
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function finishEvent_post()
    {
        $id_event = $this->post("id_event");

        if ($id_event != null) {
            
            $array = array(
                'is_done' => 1);
            
            $this->MasterModel->editEvent($id_event,$array);
            
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getEventHistory_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getEventHistory($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getEventHistoryMember_get()
    {
        $id_event = $this->get("id_event");
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {
            $result = $this->MasterModel->getEventHistoryMember($id_event,$id_dojo);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getEventHistoryDojo_get()
    {
        $id_event = $this->get("id_event");
        $id_master = $this->get("id_master");

        if ($id_master != null && $id_event != null) {
            $result = $this->MasterModel->getEventHistoryDojo($id_master,$id_event);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Error 2'
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error 1'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function uploadPhotoProfile_post()
    {
        $id_master = $this->post("id_master");
        $milliseconds = round(microtime(true) * 1000);

        //FILE PATH
        $filename = $id_master . '-' . $milliseconds . '.' . 'png';
        $path = "photo_master/";

        //UPLOAD
        $config = array(
            'upload_path' => $path,
            'allowed_types' => "gif|jpg|png|jpeg",
            'max_size' => '10024',
            'max_width' => '6000',
            'max_height' => '6000',
            'file_name' => $filename
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('image')) {
            $this->response([
                'status' => true,
                'message' => 'Error Upload'
            ], RestController::HTTP_OK);
        }

        $success = $this->MasterModel->uploadPhotoProfile($path.$filename, $id_master);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function deletePhotoProfile_delete($id_master) {
        if ($id_master != null) {
            $this->MasterModel->deletePhotoProfile($id_master);
            $this->response([
                'status' => true,
                'message' => 'Deleted'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function activateDojo_post() {
        $id_dojo = $this->post('id_dojo');
        $period = $this->post('period');
        
        if ($id_dojo != null && $period != null) {
            $this->MasterModel->activateDojo($id_dojo, $period);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function deactivateDojo_post() {
        $id_dojo = $this->post('id_dojo');
        
        if ($id_dojo != null) {
            $this->MasterModel->deactivateDojo($id_dojo);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getAd_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->MasterModel->getAd($id_master);
            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'Empty'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error 1'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function uploadNewAd_post()
    {
        $id_master = $this->post("id_master");
        $scale = $this->post("scale");
        $number = $this->post("number");
        
        $milliseconds = round(microtime(true) * 1000);

        //FILE PATH
        $filename = $id_master . '-' . $number . '-' . $milliseconds . '.' . 'png';
        $path = "photo_ad/";

        //UPLOAD
        $config = array(
            'upload_path' => $path,
            'allowed_types' => "gif|jpg|png|jpeg",
            'max_size' => '10024',
            'max_width' => '6000',
            'max_height' => '6000',
            'file_name' => $filename
        );
        
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('image')) {
            $this->response([
                'status' => true,
                'message' => 'Error Upload'
            ], RestController::HTTP_OK);
        }
        
        $array = array(
            'id_master' => $id_master,
            'number' => $number,
            'link' => $path . $filename,
            'scale' => $scale);

        $this->MasterModel->addAd($array);
        
        $this->response([
            'status' => true,
            'message' => 'Success'
        ], RestController::HTTP_OK);
    }
    
    public function deleteAd_delete($id_ad) {
        if ($id_ad != null) {
            $this->MasterModel->deleteAd($id_ad);
            $this->response([
                'status' => true,
                'message' => 'Deleted'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function editScaleAd_post()
    {
        $id_ad = $this->post("id_ad");
        $scale = $this->post("scale");

        if ($id_ad != null && $scale != null) {
            $this->MasterModel->editScaleAd($id_ad,$scale);
            $this->response([
                'status' => true,
                'message' => 'Success'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error 1'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function getParticipant_get()
	{
	    $id_exam = $this->get("id_exam");
	    
	    if ($id_exam != null) {
		    $result = $this->MasterModel->getParticipant($id_exam);
			if ($result != null) {
			    $this->response([
    				'status' => true,
    				'message' => $result
    			], RestController::HTTP_OK);
			}
			else {
			    $this->response([
				'status' => true,
    				'message' => 'Empty'
    			], RestController::HTTP_OK);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function getExam_get()
	{
	    $id_master = $this->get("id_master");
	    
	    if ($id_master != null) {
	        
		    $result = $this->MasterModel->getExam($id_master);
			if ($result != null) {
			    $this->response([
    				'status' => true,
    				'message' => $result
    			], RestController::HTTP_OK);
			}
			else {
			    $this->response([
				'status' => true,
    				'message' => 'Empty'
    			], RestController::HTTP_OK);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
	
	public function getExamHistoryMember_get()
	{
	    $id_exam = $this->get("id_exam");
	    
	    if ($id_exam != null) {
		    $result = $this->MasterModel->getParticipant($id_exam);
		    
			if ($result != null) {
			    $this->response([
    				'status' => true,
    				'message' => $result
    			], RestController::HTTP_OK);
			}
			else {
			    $this->response([
				'status' => false,
    				'message' => 'Error'
    			], RestController::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Error'
			], RestController::HTTP_BAD_REQUEST);
		}
	}
}
