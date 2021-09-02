<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class TokoHub extends RestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
        parent::__construct();
        $this->load->model('TokoHubModel');
    }

    public function getTokoAdmin_get()
    {
        $result = $this->TokoHubModel->getTokoAdmin();
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Success",
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal Mengambil Data"
            ], RestController::HTTP_OK);
        }
    }

    public function resetPasswordToko_post()
    {
        $data = array(
            'id' => $this->post('id'),
            'password' => password_hash($this->post("password"), PASSWORD_BCRYPT)
        );
        $result = $this->TokoHubModel->editTokoAdmin($data);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Berhasil Mengatur Ulang Kata Sandi"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal"
            ], RestController::HTTP_OK);
        }
    }

    public function addToko_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_profile/";
            $filePath = $path . $filename;

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
            if (!$this->upload->do_upload('photo')) {
                $this->response([
                    'status' => false,
                    'message' => 'Gagal Menggunggah Foto'
                ], RestController::HTTP_OK);
            }
        }
        $data = array(
            'name' => $this->post('name'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'photo' => $filePath,
            'username' => $this->post('username'),
            'password' => password_hash($this->post("password"), PASSWORD_BCRYPT)
        );

        $success = $this->TokoHubModel->addToko($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Tambah Toko'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function deleteToko_post()
    {
        $id = $this->post('id');
        $result = $this->TokoHubModel->deleteToko($id);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Toko Berhasil Dihapus"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal Menghapus Toko"
            ], RestController::HTTP_OK);
        }
    }
}
