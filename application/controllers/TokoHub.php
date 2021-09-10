<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class TokoHub extends RestController
{
    const RESULT_SUCCESS = "Success";
    const RESULT_EMPTY = "Empty";
    const RESULT_ERROR = "Error";

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
                'message' => self::RESULT_SUCCESS,
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

    ////////////////// TOKO
    public function getProduct_get($id_toko)
    {
        $result = $this->TokoHubModel->getProduct($id_toko);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function addProduct_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_product/";
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
            'price' => $this->post('price'),
            'stock' => $this->post('stock'),
            'unit' => $this->post('unit'),
            'id_toko' => $this->post('id_toko'),
            'photo' => $filePath
        );

        $success = $this->TokoHubModel->addProduct($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Tambah Produk'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function editProduct_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_product/";
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
            'price' => $this->post('price'),
            'stock' => $this->post('stock'),
            'unit' => $this->post('unit'),
            'id' => $this->post('id')
        );
        if ($filePath != null) {
            $data['photo'] = $filePath;
        }

        $success = $this->TokoHubModel->editProduct($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Ubah Produk'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function deleteProduct_post()
    {
        $id = $this->post('id');
        $result = $this->TokoHubModel->deleteProduct($id);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Produk Berhasil Dihapus"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal Menghapus Produk"
            ], RestController::HTTP_OK);
        }
    }

    // EMPLOYEE
    public function getEmployee_get($id_toko)
    {
        $result = $this->TokoHubModel->getEmployee($id_toko);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function addEmployee_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_employee/";
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
            'gender' => $this->post('gender'),
            'birthDate' => $this->post('birthDate'),
            'salary' => $this->post('salary'),
            'id_toko' => $this->post('id_toko'),
            'photo' => $filePath
        );

        $success = $this->TokoHubModel->addEmployee($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Tambah Pegawai'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function editEmployee_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_employee/";
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
            'gender' => $this->post('gender'),
            'birthDate' => $this->post('birthDate'),
            'salary' => $this->post('salary'),
            'id' => $this->post('id')
        );
        if ($filePath != null) {
            $data['photo'] = $filePath;
        }

        $success = $this->TokoHubModel->editEmployee($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Ubah Pegawai'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function deleteEmployee_post()
    {
        $id = $this->post('id');
        $result = $this->TokoHubModel->deleteEmployee($id);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Pegawai Berhasil Dihapus"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal Menghapus Pegawai"
            ], RestController::HTTP_OK);
        }
    }

    // SUPPLIER
    public function getSupplier_get($id_toko)
    {
        $result = $this->TokoHubModel->getSupplier($id_toko);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function addSupplier_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_supplier/";
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
            'email' => $this->post('email'),
            'phone1' => $this->post('phone1'),
            'phone2' => $this->post('phone2'),
            'address' => $this->post('address'),
            'id_toko' => $this->post('id_toko'),
            'photo' => $filePath
        );

        $success = $this->TokoHubModel->addSupplier($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Tambah Distributor'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function editSupplier_post()
    {
        $milliseconds = round(microtime(true) * 1000);
        $filePath = null;
        if ($this->post('photo') != '-') {
            //FILE PATH
            $filename = $milliseconds . '.' . 'png';
            $path = "photo_supplier/";
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
            'email' => $this->post('email'),
            'phone1' => $this->post('phone1'),
            'phone2' => $this->post('phone2'),
            'address' => $this->post('address'),
            'id' => $this->post('id')
        );
        if ($filePath != null) {
            $data['photo'] = $filePath;
        }

        $success = $this->TokoHubModel->editSupplier($data);
        if ($success) {
            $this->response([
                'status' => true,
                'message' => 'Berhasil Ubah Distributor'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Gagal Menyimpan Data'
            ], RestController::HTTP_OK);
        }
    }

    public function deleteSupplier_post()
    {
        $id = $this->post('id');
        $result = $this->TokoHubModel->deleteSupplier($id);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Distributor Berhasil Dihapus"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal Menghapus Distributor"
            ], RestController::HTTP_OK);
        }
    }

    //ATTENDANCE
    public function getAttendanceToko_get($id_toko, $startDate, $endDate)
    {
        $result = $this->TokoHubModel->getAttendanceToko($id_toko, $startDate, $endDate);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function getAttendanceEmployee_get($id_employee, $startDate, $endDate)
    {
        $result = $this->TokoHubModel->getAttendanceEmployee($id_employee, $startDate, $endDate);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function getAttendanceHistory_get($id_toko, $date)
    {
        $result = $this->TokoHubModel->getAttendanceHistory($id_toko, $date);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function getPresenceStatus_get($id_toko, $date)
    {
        $result = $this->TokoHubModel->getPresenceStatus($id_toko, $date);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => self::RESULT_SUCCESS,
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => self::RESULT_EMPTY
            ], RestController::HTTP_OK);
        }
    }

    public function submitPresence_post()
    {
        $id_toko = $this->post('id_toko');
        $date = $this->post('date');
        $data = $this->post('data');

        $result = $this->TokoHubModel->submitPresence($id_toko, $date, $data);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Berhasil Menyimpan Data Presensi"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => "Gagal Menyimpan Data Presensi"
            ], RestController::HTTP_OK);
        }
    }

    public function updatePresence_post()
    {
        $data = $this->post('data');
        $result = $this->TokoHubModel->updatePresence($data);
        if ($result) {
            $this->response([
                'status' => true,
                'message' => "Berhasil Menyimpan Perubahan Data Presensi"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => "Gagal Menyimpan Perubahan Data Presensi"
            ], RestController::HTTP_OK);
        }
    }
}
