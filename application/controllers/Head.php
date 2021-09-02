<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Head extends RestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
        parent::__construct();
        $this->load->model('HeadModel');
        $this->load->model('MasterModel');
        $this->load->helper('text');
    }

    //Profile
    public function getProfile_get()
    {
        $id_member = $this->get("id_member");
        if ($id_member != null) {
            $result = $this->HeadModel->getProfile($id_member);
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

    public function editProfile_post()
    {
        $id_member = $this->post("id_member");
        if ($id_member != null) {
            $data = array(
                'name' => $this->post("name"),
                'birth_date' => $this->post("birth_date"),
                'blood' => $this->post("blood"),
                'disease' => $this->post("disease"),
                'phone1' => $this->post("phone1"),
                'phone2' => $this->post("phone2"),
                'address' => $this->post("address"),
                'place_of_birth' => $this->post("place_of_birth")
            );
            $this->HeadModel->editProfile($data, $id_member);
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

    public function verifyPassword($id_member, $password)
    {
        $password_hash = $this->HeadModel->getPassword($id_member);
        return password_verify($password, $password_hash);
    }

    public function editPassword_post()
    {
        $id_member = $this->post("id_member");
        $current_password = $this->post("current_password");
        $new_password = password_hash($this->post("new_password"), PASSWORD_BCRYPT);

        if ($id_member != null && $current_password != null && $new_password != null) {
            if ($this->verifyPassword($id_member, $current_password)) {
                $this->HeadModel->changePassword($id_member, $new_password);
                $this->response([
                    'status' => true,
                    'message' => "Success"
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'message' => "Your password is wrong"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Error"
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function editMemberPassword_post()
    {
        $id_member = $this->post("id_member");
        $new_password = password_hash($this->post("new_password"), PASSWORD_BCRYPT);

        if ($id_member != null && $new_password != null) {
            $this->HeadModel->changePassword($id_member, $new_password);
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

    public function editMemberCertificate_post()
    {
        $id_member = $this->post("id_member");
        $certificate = $this->post("certificate");

        if ($id_member != null) {
            $array = array();
            if ($certificate == null) {
                $array = array('certificate_number' => null);
            } else {
                $array = array('certificate_number' => $certificate);
            }

            $this->HeadModel->editMemberCertificate($id_member, $array);
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

    public function uploadPhotoProfile_post()
    {
        $id_member = $this->post("id_member");
        $milliseconds = round(microtime(true) * 1000);

        //FILE PATH
        $filename = $id_member . '-' . $milliseconds . '.' . 'png';
        $path = "photo_profile/";

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

        $success = $this->HeadModel->uploadPhotoProfile($path . $filename, $id_member);
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

    public function getDojoProfile_get()
    {
        $id_dojo = $this->get("id_dojo");
        if ($id_dojo != null) {
            $result = $this->HeadModel->getProfileDojo($id_dojo);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => $result[0]
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

    public function uploadDojoPhoto_post()
    {
        $id_dojo = $this->post("id_dojo");
        $position = $this->post("position");

        $milliseconds = round(microtime(true) * 1000);

        if ($id_dojo != null && $position != null) {
            $id_master = $this->HeadModel->getIDMaster($id_dojo);

            //FILE PATH
            $filename = $position . '-' . $milliseconds . '.' . 'png';
            $path = "photo_dojo/$id_master/$id_dojo/";
            $filePath = $path . $filename;

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
                    'status' => false,
                    'message' => 'Error'
                ], RestController::HTTP_OK);
            }

            $success = $this->HeadModel->uploadDojoPhoto($position, $filePath, $id_dojo);
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
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function getAllMemberForEdit_get()
    {
        $id_dojo = $this->get("id_dojo");
        if ($id_dojo != null) {
            $result = $this->HeadModel->getAllMemberForEdit($id_dojo);
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

    public function getRank_get()
    {
        $result = $this->HeadModel->getRank();
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

    public function isMemberUsernameUnix_get()
    {
        $username = $this->get("username");
        if ($username != null) {
            $result = $this->HeadModel->isMemberUsernameUnix($username);
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

    public function editDojo_post()
    {
        $id_dojo = $this->post("id_dojo");
        $id_head = $this->post("id_head");
        $id_treasurer = $this->post("id_treasurer");
        $id_secretary = $this->post("id_secretary");
        $id_assistant = $this->post("id_assistant");

        $data_id = array($id_head, $id_treasurer, $id_secretary, $id_assistant);

        $data_before = $this->HeadModel->getAdministrator($id_dojo)[0];

        $id_head_before = $data_before['id_head'];
        $id_treasurer_before = $data_before['id_treasurer'];
        $id_secretary_before = $data_before['id_secretary'];
        $id_assistant_before = $data_before['id_assistant'];

        if ($id_head != $id_head_before) {
            $this->HeadModel->changeRole($id_head, 1);
            if ($id_head_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id, $id_head_before)) {
                    $this->HeadModel->changeRole($id_head_before, 6);
                };
            }
        }
        if ($id_treasurer != $id_treasurer_before) {
            $this->HeadModel->changeRole($id_treasurer, 2);
            if ($id_treasurer_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id, $id_treasurer_before)) {
                    $this->HeadModel->changeRole($id_treasurer_before, 6);
                };
            }
        }
        if ($id_secretary != $id_secretary_before) {
            $this->HeadModel->changeRole($id_secretary, 3);
            if ($id_secretary_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id, $id_secretary_before)) {
                    $this->HeadModel->changeRole($id_secretary_before, 6);
                };
            }
        }
        if ($id_assistant != $id_assistant_before) {
            $this->HeadModel->changeRole($id_assistant, 5);
            if ($id_assistant_before != -1) {
                if (!$this->checkIfStillAdministrator($data_id, $id_assistant_before)) {
                    $this->HeadModel->changeRole($id_assistant_before, 6);
                };
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
            'id_head' => $id_head,
            'id_treasurer' => $id_treasurer,
            'id_secretary' => $id_secretary,
            'id_assistant' => $id_assistant,
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

        $this->HeadModel->editDojo($id_dojo, $array);

        $this->response([
            'status' => true,
            'message' => "Success"
        ], RestController::HTTP_OK);
    }

    public function editMapDojo_post()
    {
        $id_dojo = $this->post("id_dojo");
        $latitude = $this->post("latitude");
        $longitude = $this->post("longitude");

        if ($id_dojo != null && $latitude != null && $longitude != null) {
            $this->HeadModel->updateMapDojo($id_dojo, $latitude, $longitude);
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

    public function deletePhotoDojo_post()
    {
        $id_dojo = $this->post("id_dojo");
        $position = $this->post("position");

        if ($id_dojo != null && $position != null) {
            $this->HeadModel->deletePhotoDojo($id_dojo, $position);
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

    public function deletePhotoProfile_post()
    {
        $id_member = $this->post("id_member");

        if ($id_member != null) {
            $this->HeadModel->deletePhotoProfile($id_member);
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

    public function getPresenceDojo_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {
            $result = $this->HeadModel->getPresenceDojo($id_dojo);
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

    public function addPresence_post()
    {
        $id_dojo = $this->post("id_dojo");
        $id_schedule = $this->post("id_schedule");
        $id_head = $this->post("id_head");
        $datetime = $this->post("datetime");
        $isHeadAttend = $this->post("isHeadAttend");

        if ($id_schedule != null && $id_dojo != null && $id_head != null && $datetime != null && $isHeadAttend != null) {
            $instructor = $this->HeadModel->getCurrentInstructor($id_dojo);
            $data = array(
                'id_schedule' => $id_schedule,
                'id_dojo' => $id_dojo,
                'datetime' => $datetime,
                'treasurer_permission' => 0,
                'secretary_permission' => 0,
                'id_instructor' => $instructor
            );

            $id_presence = $this->HeadModel->addPresence($data);

            if ($isHeadAttend == "true") {
                $head_presence = array(
                    'id_presence' => $id_presence,
                    'id_member' => $id_head,
                    'id_dojo' => $id_dojo,
                    'datetime' => $datetime
                );
                $this->HeadModel->addHistoryPresence($head_presence, $id_head);
            }
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

    public function deletePresence_delete($id_presence = null)
    {
        if ($id_presence != null) {
            $this->HeadModel->deletePresence($id_presence);
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

    public function editPermissionPresence_post()
    {
        $id_presence = $this->post("id_presence");
        $treasurer_permission = $this->post("treasurer_permission");
        $secretary_permission = $this->post("secretary_permission");

        if ($id_presence != null && $treasurer_permission != null && $secretary_permission != null) {
            $this->HeadModel->editPermissionPresence($id_presence, $treasurer_permission, $secretary_permission);
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
            $result = $this->HeadModel->getDojoForSpinner($id_master);
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

    public function getMemberForManual_get()
    {
        $id_dojo = $this->get("id_dojo");
        $id_presence = $this->get("id_presence");

        if ($id_dojo != null && $id_presence != null) {
            $result = $this->HeadModel->getMemberForManual($id_dojo, $id_presence);
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

    public function addPresenceManual_post()
    {
        $id_presence = $this->post("id_presence");
        $id_member = $this->post("id_member");
        $datetime = date('Y-m-d H:i:s');

        if ($id_presence != null && $id_member != null) {

            $id_dojo = $this->HeadModel->getIDDojoByIDMember($id_member);

            if ($id_dojo ==  null) {
                $this->response([
                    'status' => false,
                    'message' => 'Error'
                ], RestController::HTTP_BAD_REQUEST);
            }

            $data = array(
                'id_presence' => $id_presence,
                'id_member' => $id_member,
                'id_dojo' => $id_dojo,
                'datetime' => $datetime,
                'is_manual' => 1
            );

            $this->HeadModel->addHistoryPresence($data, $id_member);
            $this->response([
                'status' => true,
                'message' => "Success"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function addPresenceByQR_post()
    {
        $id_presence = $this->post("id_presence");
        $id_member = $this->post("id_member");
        $datetime = date('Y-m-d H:i:s');

        if ($id_presence != null && $id_member != null) {

            $id_dojo = $this->HeadModel->getIDDojoByIDMember($id_member);

            if ($id_dojo ==  null) {
                $this->response([
                    'status' => false,
                    'message' => 'Error'
                ], RestController::HTTP_BAD_REQUEST);
            }

            $data = array(
                'id_presence' => $id_presence,
                'id_member' => $id_member,
                'id_dojo' => $id_dojo,
                'datetime' => $datetime,
                'is_manual' => 0
            );

            $this->HeadModel->addHistoryPresence($data, $id_member);
            $this->response([
                'status' => true,
                'message' => "Success"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function getPresenceData_get()
    {
        $id_presence = $this->get("id_presence");
        $id_member = $this->get("id_member");

        if ($id_presence != null && $id_member != null) {

            if ($this->HeadModel->isMemberNotYetPresence($id_member, $id_presence)) {

                $check = $this->HeadModel->checkPresenceTime($id_presence);
                if ($check == 'Success') {

                    $isMemberAdministrator = $this->HeadModel->isMemberAdministrator($id_member);
                    if ($isMemberAdministrator) {
                        $result = $this->HeadModel->getDataPresenceForQRCode($id_presence);
                        if ($result != null) {
                            $this->response([
                                'status' => true,
                                'message' => 'Success',
                                'data' => $result
                            ], RestController::HTTP_OK);
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => 'Error'
                            ], RestController::HTTP_BAD_REQUEST);
                        }
                    } else {
                        $id_schedule = $this->HeadModel->getIDScheduleByIDPresence($id_presence);

                        if ($this->HeadModel->isMemberHaveSchedule($id_member, $id_schedule)) {

                            $result = $this->HeadModel->getDataPresenceForQRCode($id_presence);
                            if ($result != null) {
                                $this->response([
                                    'status' => true,
                                    'message' => 'Success',
                                    'data' => $result
                                ], RestController::HTTP_OK);
                            } else {
                                $this->response([
                                    'status' => false,
                                    'message' => 'Error'
                                ], RestController::HTTP_BAD_REQUEST);
                            }
                        } else {
                            $this->response([
                                'status' => true,
                                'message' => 'You are not registered on this schedule'
                            ], RestController::HTTP_OK);
                        }
                    }
                } else {
                    $this->response([
                        'status' => true,
                        'message' => $check
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => true,
                    'message' => 'You already present'
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function uploadPhotoPresence_post()
    {
        $id_dojo = $this->post("id_dojo");
        $id_presence = $this->post("id_presence");

        $milliseconds = round(microtime(true) * 1000);

        if ($id_dojo != null && $id_presence != null) {
            $id_master = $this->HeadModel->getIDMaster($id_dojo);

            //FILE PATH
            $filename = $id_presence . '-' . $milliseconds . '.' . 'png';
            $path = "photo_presence/$id_master/$id_dojo/";
            $filePath = $path . $filename;

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
                    'status' => false,
                    'message' => 'Error'
                ], RestController::HTTP_OK);
            }

            $success = $this->HeadModel->uploadPhotoPresence($id_presence, $filePath);
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
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function deletePhotoPresence_post()
    {
        $id_presence = $this->post("id_presence");

        if ($id_presence != null) {
            $this->HeadModel->deletePhotoPresence($id_presence);
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

    ///// NOTIFICATION //////
    public function getNotification_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {
            $result = $this->HeadModel->getNotification($id_dojo);
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

    public function addNotification_post()
    {
        $title = $this->post("title");
        $message = $this->post("message");
        $date = date('Y-m-d');
        $id_dojo = $this->post("id_dojo");

        if ($title != null && $message != null && $id_dojo != null) {

            $data = array(
                'title' => $title,
                'message' => $message,
                'date' => $date,
                'id_dojo' => $id_dojo
            );

            $this->HeadModel->addNotification($data);

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

    public function editNotification_post()
    {
        $title = $this->post("title");
        $message = $this->post("message");
        $id_notification = $this->post("id_notification");

        if ($title != null && $message != null && $id_notification != null) {

            $data = array(
                'title' => $title,
                'message' => $message
            );

            $this->HeadModel->editNotification($data, $id_notification);

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

    public function deleteNotification_delete($id_notification)
    {
        if ($id_notification != null) {
            $this->HeadModel->deleteNotification($id_notification);

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

    public function getScheduleDojo_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {
            $result = $this->HeadModel->getScheduleDojo($id_dojo);
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

    public function addScheduleDojo_post()
    {
        $id_dojo = $this->post("id_dojo");
        $day = $this->post("day");
        $time = $this->post("time");

        if ($id_dojo != null && $day != null && $time != null) {
            $data = array(
                'id_dojo' => $id_dojo,
                'day' => $day,
                'time' => $time
            );

            $this->HeadModel->addScheduleDojo($data);

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

    public function editScheduleDojo_post()
    {
        $id_schedule = $this->post("id_schedule");
        $day = $this->post("day");
        $time = $this->post("time");

        if ($id_schedule != null && $day != null && $time != null) {
            $data = array(
                'day' => $day,
                'time' => $time
            );

            $this->HeadModel->editScheduleDojo($data, $id_schedule);

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

    public function deleteScheduleDojo_delete($id_schedule)
    {
        if ($id_schedule != null) {
            $this->HeadModel->deleteScheduleDojo($id_schedule);

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

    public function getMemberForSchedule_get()
    {
        $id_dojo = $this->get("id_dojo");
        $id_schedule = $this->get("id_schedule");

        if ($id_dojo != null && $id_schedule != null) {
            $result = $this->HeadModel->getMemberForSchedule($id_dojo, $id_schedule);
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

    public function getMemberForAddSchedule_get()
    {
        $id_dojo = $this->get("id_dojo");
        $id_schedule = $this->get("id_schedule");

        if ($id_dojo != null && $id_schedule != null) {
            $result = $this->HeadModel->getMemberForAddSchedule($id_dojo, $id_schedule);
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

    public function addScheduleMember_post()
    {
        $id_member = $this->post("id_member");
        $id_schedule = $this->post("id_schedule");

        if ($id_member != null && $id_schedule != null) {

            $data = array(
                'id_member' => $id_member,
                'id_schedule' => $id_schedule
            );

            $result = $this->HeadModel->addScheduleMember($data);
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

    public function deleteScheduleMember_delete($id_member, $id_schedule)
    {
        if ($id_member != null && $id_schedule != null) {
            $result = $this->HeadModel->deleteScheduleMember($id_member, $id_schedule);
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
            $result = $this->HeadModel->getDetailMember($id_dojo);
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

    public function getScheduleMember_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {
            $result = $this->HeadModel->getScheduleMember($id_member);
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

    public function deleteMember_delete($id_member)
    {
        if ($id_member != null) {
            $result = $this->HeadModel->deleteMember($id_member);
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

    public function getExam_get()
    {
        $id_master = $this->get("id_master");
        $id_dojo = $this->get("id_dojo");

        if ($id_master != null && $id_dojo != null) {

            $result = $this->HeadModel->getExam($id_master, $id_dojo);
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

    public function addExam_post()
    {
        $id_master = $this->post("id_master");
        $datetime = $this->post("datetime");
        $descrip = $this->post("descrip");

        if ($id_master != null && $datetime != null && $descrip != null) {

            $data = array(
                'id_master' => $id_master,
                'datetime' => $datetime,
                'descrip' => $descrip
            );

            $result = $this->HeadModel->addExam($data);
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

    public function editExam_post()
    {
        $id_exam = $this->post("id_exam");
        $datetime = $this->post("datetime");
        $descrip = $this->post("descrip");

        if ($id_exam != null && $datetime != null && $descrip != null) {

            $data = array(
                'datetime' => $datetime,
                'descrip' => $descrip
            );

            $result = $this->HeadModel->editExam($data, $id_exam);
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

    public function deleteExam_delete($id_exam)
    {
        if ($id_exam != null) {
            $result = $this->HeadModel->deleteExam($id_exam);
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

    public function getParticipant_get()
    {
        $id_exam = $this->get("id_exam");
        $id_dojo = $this->get("id_dojo");

        if ($id_exam != null && $id_dojo != null) {
            $result = $this->HeadModel->getParticipant($id_exam, $id_dojo);
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

    public function addParticipant_post()
    {
        $id_exam = $this->post("id_exam");
        $id_member = $this->post("id_member");
        $to_rank = $this->post("to_rank");
        $isRecommend = $this->post("isRecommend");

        if ($id_exam != null && $id_member != null && $to_rank != null && $isRecommend != null) {

            $from_rank = $this->HeadModel->getRankMember($id_member);

            $data = array(
                'id_exam' => $id_exam,
                'id_member' => $id_member,
                'from_rank' => $from_rank,
                'to_rank' => $to_rank,
                'isRecommend' => $isRecommend
            );

            $result = $this->HeadModel->addParticipan($data);
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

    public function editParticipant_post()
    {
        $id_participant = $this->post("id_participant");
        $to_rank = $this->post("to_rank");
        $isRecommend = $this->post("isRecommend");

        if ($id_participant != null && $to_rank != null && $isRecommend != null) {

            $data = array(
                'to_rank' => $to_rank,
                'isRecommend' => $isRecommend
            );

            $result = $this->HeadModel->editParticipant($data, $id_participant);
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

    public function deleteParticipant_delete($id_participant)
    {
        if ($id_participant != null) {
            $result = $this->HeadModel->deleteParticipant($id_participant);
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

    public function getExamHistory_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {
            $result = $this->HeadModel->getExamHistory($id_master);
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

    public function getExamHistoryMember_get()
    {
        $id_exam = $this->get("id_exam");
        $id_dojo = $this->get("id_dojo");

        if ($id_exam != null) {
            $result = $this->HeadModel->getParticipant($id_exam, $id_dojo);
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

    public function getMemberForExam_get()
    {
        $id_dojo = $this->get("id_dojo");
        $id_exam = $this->get("id_exam");

        if ($id_dojo != null && $id_exam != null) {
            $result = $this->HeadModel->getMemberForExam($id_dojo, $id_exam);
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

    public function finishExam_post()
    {
        $id_exam = $this->post("id_exam");
        $pass = $this->post("pass");
        $notPass = $this->post("notPass");

        if ($id_exam != null && $pass != null && $notPass != null) {

            $this->HeadModel->finishExam($id_exam, $pass, $notPass);
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

    public function getRecord_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getRecord($id_member);

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

    public function getRequestJoin_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {

            $result = $this->HeadModel->getRequestJoin($id_dojo);

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

    public function acceptRequestJoin_post()
    {
        $id_request = $this->post("id_request");
        $id_rank = $this->post("id_rank");
        $id_fee = $this->post("fee");
        $period = $this->post("period");
        $schedule = $this->post("schedule");

        if ($id_request != null && $id_rank != null && $id_fee != null && $period != null && $schedule != null) {

            $this->HeadModel->acceptRequestJoin($id_request, $id_rank, $id_fee, $period, $schedule);

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

    public function deleteRequestJoin_delete($id_request)
    {

        if ($id_request != null) {
            $result = $this->HeadModel->deleteRequestJoin($id_request);
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

    public function getMemberFee_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {

            $result = $this->HeadModel->getMemberFee($id_dojo);

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

    public function addMemberFee_post()
    {
        $id_member = $this->post("id_member");
        $id_dojo = $this->post("id_dojo");
        $month = $this->post("month");
        $expiration = $this->post("expiration");
        $fee = $this->post("fee");
        $is_late = $this->post("is_late");

        if ($id_member != null && $id_dojo != null && $month != null && $expiration != null && $fee != null && $is_late != null) {

            $this->HeadModel->addMemberFee($id_member, $id_dojo, $month, $expiration, $fee, $is_late);

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

    public function getHistoryFee_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {

            $result = $this->HeadModel->getHistoryFee($id_dojo);

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

    public function getEventMember_get()
    {
        $id_event = $this->get("id_event");
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null && $id_event != null) {
            $result = $this->HeadModel->getEventMember($id_event, $id_dojo);
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

    public function deleteEventMember_delete($id_participant)
    {
        if ($id_participant != null) {
            $this->HeadModel->deleteEventMember($id_participant);
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

    public function setQualifyEventMember_post()
    {
        $id_participant = $this->post("id_participant");
        $qualify = $this->post("qualify");

        if ($id_participant != null && $qualify != null) {
            $this->HeadModel->editEventMember($id_participant, array('is_qualify' => $qualify));
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

    public function addEventMember_post()
    {
        $id_member = $this->post("id_member");
        $id_event = $this->post("id_event");

        if ($id_member != null && $id_event != null) {

            $data = array(
                'id_member' => $id_member,
                'id_event' => $id_event
            );

            $this->HeadModel->addEventMember($data);
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

    public function getMemberForEvent_get()
    {
        $id_event = $this->get("id_event");
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null && $id_event != null) {
            $result = $this->HeadModel->getMemberForEvent($id_event, $id_dojo);
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

    public function getEvent_get()
    {
        $id_master = $this->get("id_master");
        $id_dojo = $this->get("id_dojo");

        if ($id_master != null && $id_dojo != null) {
            $result = $this->HeadModel->getEvent($id_master, $id_dojo);
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

    public function editEventPermission_post()
    {

        $id_event = $this->post("id_event");
        $id_dojo = $this->post("id_dojo");
        $treasurer = $this->post("treasurer");
        $secretary = $this->post("secretary");

        if ($id_event != null && $id_dojo != null && $treasurer != null && $secretary != null) {

            $id = $this->HeadModel->getIDEventPermission($id_dojo, $id_event);
            if ($id == null) {

                $data = array(
                    'id_event' => $id_event,
                    'id_dojo' => $id_dojo,
                    'treasurer' => $treasurer,
                    'secretary' => $secretary
                );

                $this->HeadModel->addEventPermission($data);
            } else {
                $data = array(
                    'treasurer' => $treasurer,
                    'secretary' => $secretary
                );
                $this->HeadModel->editEventPermission($id, $data);
            }

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

    public function getEventData_get()
    {

        $id_event = $this->get("id_event");
        $id_member = $this->get("id_member");

        if ($id_event != null && $id_member != null) {

            $result = $this->HeadModel->checkEventParticipant($id_event, $id_member);

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

    public function attendEventMember_get()
    {
        $id_event = $this->get("id_event");
        $id_participant = $this->get("id_participant");

        if ($id_event != null && $id_participant != null) {
            $currentDate = date('Y-m-d H:i:s');
            $data = array('datetime' => $currentDate);

            $this->HeadModel->editEventMember($id_participant, $data);

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

    public function getMemberEvent_get()
    {

        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getMemberEvent($id_member);

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

    public function getMemberPresence_get()
    {

        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getMemberPresence($id_member);

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

    public function getHistoryFeeMember_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getHistoryFeeMember($id_member);

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

    public function getListDojoInstructor_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getListDojoInstructor($id_member);

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

    public function getPresenceInstructor_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getPresenceInstructor($id_member);

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

    public function getPresenceAssistant_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {

            $result = $this->HeadModel->getPresenceAssistant($id_dojo);

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

    public function updateNote_post()
    {
        $id_presence = $this->post('id_presence');
        $note = $this->post('note');

        if ($id_presence != null && $note != null) {
            $this->HeadModel->updateNote($id_presence, $note);
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

    public function addNoteSpecial_post()
    {
        $id_presence = $this->post('id_presence');
        $id_rank = $this->post('id_rank');
        $note = $this->post('note');

        if ($id_presence != null && $id_rank != null && $note != null) {

            $array = array(
                'id_presence' => $id_presence,
                'id_rank' => $id_rank,
                'note' => $note
            );

            $this->HeadModel->addNoteSpecial($array);
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

    public function editNoteSpecial_post()
    {
        $id_note = $this->post('id_note');
        $id_rank = $this->post('id_rank');
        $note = $this->post('note');

        if ($id_note != null && $id_rank != null && $note != null) {

            $array = array(
                'id_rank' => $id_rank,
                'note' => $note
            );

            $this->HeadModel->editNoteSpecial($id_note, $array);
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

    public function deleteNoteSpecial_delete($id_note)
    {

        if ($id_note != null) {
            $this->HeadModel->deleteNoteSpecial($id_note);
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

    public function getSpecialNote_get()
    {
        $id_presence = $this->get("id_presence");

        if ($id_presence != null) {

            $result = $this->HeadModel->getSpecialNote($id_presence);

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

    public function getTreasurerSecretaryPresence_get()
    {
        $id_dojo = $this->get("id_dojo");
        $role = (int) $this->get("role");

        if ($id_dojo != null && $role != null) {

            $result = $this->HeadModel->getTreasurerSecretaryPresence($id_dojo, $role);

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

    public function getExamMember_get()
    {
        $id_member = $this->get("id_member");

        if ($id_member != null) {

            $result = $this->HeadModel->getExamMember($id_member);

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

    public function checkNameRequestCodeUnix_post()
    {
        $id_dojo = $this->post("id_dojo");
        $name = $this->post("name");
        $request_code = $this->post("request_code");

        if ($id_dojo != null && $name != null && $request_code != null) {

            $result = $this->HeadModel->checkNameRequestCodeUnix($id_dojo, $name, $request_code);

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

    public function checkDojo_get($id_dojo)
    {
        if ($id_dojo != null) {
            $period = $this->HeadModel->checkPeriodDojo($id_dojo);
            $code = $this->HeadModel->codeForJoinNotNull($id_dojo);

            if ($period !== null) {
                $this->response([
                    'status' => true,
                    'period' => $period,
                    'code' => $code
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


    public function submitCodeRequestDojo_post()
    {
        $id_dojo = $this->post("id_dojo");
        $code = $this->post("code");

        if ($id_dojo != null && $code != null) {

            $this->HeadModel->submitCodeRequestDojo($id_dojo, $code);

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

    public function checkRequestCodeUnix_get()
    {
        $id_dojo = $this->get("id_dojo");
        $code = $this->get("code");

        if ($id_dojo != null && $code != null) {
            $result = $this->HeadModel->checkRequestCodeUnix($id_dojo, $code);

            if ($result !== null) {
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

    public function getDojoName_get()
    {
        $id_master = $this->get("id_master");

        if ($id_master != null) {

            $result = $this->HeadModel->getDojoName($id_master);

            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result[0]
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Error Empty'
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function getFeeDojo_get()
    {
        $id_dojo = $this->get("id_dojo");

        if ($id_dojo != null) {

            $result = $this->HeadModel->getFeeDojo($id_dojo);

            if ($result != null) {
                $this->response([
                    'status' => true,
                    'message' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Error Empty'
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function editFeeMember_post()
    {
        $id_member = $this->post("id_member");
        $fee = $this->post("fee");

        if ($id_member != null && $fee != null) {
            $this->HeadModel->editFeeMember($id_member, $fee);
            $this->response([
                'status' => true,
                'message' => "Success"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Error'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function testtimezone_get()
    {
        $timezone = $this->get("timezone");
        $schedules = $this->HeadModel->getScheduleDojo(19);

        $result = array();

        foreach ($schedules as $schedule) {
            $schedule_date = new DateTime($schedule['time'], new DateTimeZone('Asia/Jakarta'));
            $schedule_date->setTimezone(new DateTimeZone($timezone));

            $temp = $schedule;
            $temp['time'] = $schedule_date->format('H:i:s');
            array_push($result, $temp);
        }

        $this->response([
            'status' => true,
            'message' => $result
        ], RestController::HTTP_OK);
    }

    public function test_get()
    {
        $result = $this->MasterModel->getDojoExcel(19);
        $this->response([
            'status' => true,
            'message' => $result
        ], RestController::HTTP_OK);
    }
}
