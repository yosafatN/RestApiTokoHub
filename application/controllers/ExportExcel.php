<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use chriskacerguis\RestServer\RestController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportExcel extends RestController
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

    public function getAllMember_get($id_dojo)
    {
        $Data = $this->HeadModel->getDetailMemberExcel($id_dojo);

        if ($Data == null) {
            return;
        }
        $dojo = $Data['dojo'];
        $dojo_name = $Data['dojo_name'];
        $Members = $Data['member'];

        $spreadsheet = new Spreadsheet;

        $start = 3;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'Username')
            ->setCellValue('E' . $start, 'Rank')
            ->setCellValue('F' . $start, 'Role')
            ->setCellValue('G' . $start, 'Period')
            ->setCellValue('H' . $start, 'Attendance')
            ->setCellValue('I' . $start, 'Attendance at ' . $dojo_name)
            ->setCellValue('J' . $start, 'Expiration')
            ->setCellValue('K' . $start, 'Fee')
            ->setCellValue('L' . $start, 'Place of Birth')
            ->setCellValue('M' . $start, 'Birth Date')
            ->setCellValue('N' . $start, 'Blood Group')
            ->setCellValue('O' . $start, 'Disease')
            ->setCellValue('P' . $start, 'Phone Number 1')
            ->setCellValue('Q' . $start, 'Phone Number 2')
            ->setCellValue('R' . $start, 'Address')
            ->setCellValue('S' . $start, 'Status');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('J' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('L' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('M' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('N' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('O' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('P' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('Q' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('R' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('S' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($Members as $member) {
            $attendance = $member['attendance'];
            $own = $member['own'];
            if ($own != 0) {
                $own = $own / $attendance;
            }

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('D' . $coloum, $member['username'])
                ->setCellValue('E' . $coloum, $member['rank'])
                ->setCellValue('F' . $coloum, $member['role'])
                ->setCellValue('G' . $coloum, $member['period'])
                ->setCellValue('H' . $coloum, $attendance)
                ->setCellValue('I' . $coloum, $own)
                ->setCellValue('J' . $coloum, $member['expiration'])
                ->setCellValue('K' . $coloum, $member['fee'])
                ->setCellValue('L' . $coloum, $member['place_of_birth'])
                ->setCellValue('M' . $coloum, $member['birth_date'])
                ->setCellValue('N' . $coloum, $member['blood'])
                ->setCellValue('O' . $coloum, $member['disease'])
                ->setCellValue('P' . $coloum, $member['phone1'])
                ->setCellValue('Q' . $coloum, $member['phone2'])
                ->setCellValue('R' . $coloum, $member['address'])
                ->setCellValue('S' . $coloum, $member['status']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('I' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('J' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('K' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('L' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('M' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('N' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('O' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('P' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('Q' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('R' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('S' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        $startValue = $start + 1;
        $spreadsheet->getActiveSheet()->getStyle('G' . $startValue . ':G' . $coloum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $spreadsheet->getActiveSheet()->getStyle('J' . $startValue . ':J' . $coloum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $spreadsheet->getActiveSheet()->getStyle('K' . $startValue . ':K' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');
        $spreadsheet->getActiveSheet()->getStyle('I' . $startValue . ':I' . $coloum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':S' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 19; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $dojo . ' Members List');
        $spreadsheet->getActiveSheet()->mergeCells("A1:S1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Member ' . $dojo . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getExamMember_get($id_dojo, $id_exam)
    {
        $Data = $this->HeadModel->getExamMemberExcel($id_dojo, $id_exam);

        if ($Data == null) {
            return;
        }

        $exam = $Data['exam'];
        $participants = $Data['participant'];

        $spreadsheet = new Spreadsheet;

        $start = 5;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'From Rank')
            ->setCellValue('E' . $start, 'To Rank')
            ->setCellValue('F' . $start, 'By Recommendation')
            ->setCellValue('G' . $start, 'Username')
            ->setCellValue('H' . $start, 'Current Rank');

        //Style isi table
        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($participants as $member) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('D' . $coloum, $member['from_rank'])
                ->setCellValue('E' . $coloum, $member['to_rank'])
                ->setCellValue('F' . $coloum, $member['isRecommend'])
                ->setCellValue('G' . $coloum, $member['username'])
                ->setCellValue('H' . $coloum, $member['rank']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        //Judul Kolom
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':H' . $start)->applyFromArray($styleArray);

        //Auto size kolom
        for ($i = 0; $i < 21; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        //Judul
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'List of examinees on ' . $exam['datetime']);
        $spreadsheet->getActiveSheet()->mergeCells("A1:H1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->mergeCells("A2:B2");
        $spreadsheet->getActiveSheet()->mergeCells("A3:B3");
        $spreadsheet->getActiveSheet()->mergeCells("C2:H2");
        $spreadsheet->getActiveSheet()->mergeCells("C3:H3");
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Datetime');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C2', $exam['datetime']);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Description');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C3', $exam['descrip']);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Exam ' . $exam['datetime'] . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getExamResult_get($id_dojo, $id_exam, $timezone)
    {
        $Data = $this->HeadModel->getExamResultExcel($id_dojo, $id_exam, $timezone);

        if ($Data == null) {
            return;
        }

        $exam = $Data['exam'];
        $participants = $Data['participant'];

        $spreadsheet = new Spreadsheet;

        $start = 5;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'Pass')
            ->setCellValue('E' . $start, 'From Rank')
            ->setCellValue('F' . $start, 'To Rank')
            ->setCellValue('G' . $start, 'By Recommendation')
            ->setCellValue('H' . $start, 'Username')
            ->setCellValue('I' . $start, 'Current Rank');

        //Style isi table
        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($participants as $member) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('D' . $coloum, $member['status'])
                ->setCellValue('E' . $coloum, $member['from_rank'])
                ->setCellValue('F' . $coloum, $member['to_rank'])
                ->setCellValue('G' . $coloum, $member['isRecommend'])
                ->setCellValue('H' . $coloum, $member['username'])
                ->setCellValue('I' . $coloum, $member['rank']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('I' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        //Judul Kolom
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':I' . $start)->applyFromArray($styleArray);

        //Auto size kolom
        for ($i = 0; $i < 21; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        //Judul
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Results of the exam on ' . $exam['datetime_new']);
        $spreadsheet->getActiveSheet()->mergeCells("A1:I1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->mergeCells("A2:B2");
        $spreadsheet->getActiveSheet()->mergeCells("A3:B3");
        $spreadsheet->getActiveSheet()->mergeCells("C2:I2");
        $spreadsheet->getActiveSheet()->mergeCells("C3:I3");
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Datetime');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C2', $exam['datetime_new']);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Description');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C3', $exam['descrip']);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Exam Result ' . $exam['datetime'] . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getEventParticipant_get($id_dojo, $id_event, $timezone)
    {
        $Data = $this->HeadModel->getEventParticipantExcel($id_dojo, $id_event, $timezone);

        if ($Data == null) {
            return;
        }
        $event = $Data['event'];
        $Members = $Data['member'];

        $spreadsheet = new Spreadsheet;

        $start = 5;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'Qualify')
            ->setCellValue('E' . $start, 'Username')
            ->setCellValue('F' . $start, 'Rank');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($Members as $member) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('D' . $coloum, $member['is_qualify'])
                ->setCellValue('E' . $coloum, $member['username'])
                ->setCellValue('F' . $coloum, $member['rank']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':F' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 6; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'List of participants at the event on ' . $event['datetime_new']);
        $spreadsheet->getActiveSheet()->mergeCells("A1:F1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->mergeCells("A2:B2");
        $spreadsheet->getActiveSheet()->mergeCells("A3:B3");
        $spreadsheet->getActiveSheet()->mergeCells("C2:F2");
        $spreadsheet->getActiveSheet()->mergeCells("C3:F3");
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Datetime');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C2', $event['datetime_new']);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Description');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C3', $event['descrip']);


        $writer = new Xlsx($spreadsheet);

        $filename = 'Event Participant ' . $event['datetime'] . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getEventParticipantAttend_get($id_dojo, $id_event, $timezone)
    {
        $Data = $this->HeadModel->getEventParticipantAttendExcel($id_dojo, $id_event, $timezone);

        if ($Data == null) {
            return;
        }
        $event = $Data['event'];
        $Members = $Data['member'];

        $spreadsheet = new Spreadsheet;

        $start = 5;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'Present At')
            ->setCellValue('E' . $start, 'Username')
            ->setCellValue('F' . $start, 'Rank');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($Members as $member) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('D' . $coloum, $member['attend'])
                ->setCellValue('E' . $coloum, $member['username'])
                ->setCellValue('F' . $coloum, $member['rank']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':F' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 6; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Participants who attended the event on ' . $event['datetime_new']);
        $spreadsheet->getActiveSheet()->mergeCells("A1:F1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->mergeCells("A2:B2");
        $spreadsheet->getActiveSheet()->mergeCells("A3:B3");
        $spreadsheet->getActiveSheet()->mergeCells("C2:F2");
        $spreadsheet->getActiveSheet()->mergeCells("C3:F3");
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Datetime');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C2', $event['datetime_new']);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Description');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C3', $event['descrip']);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Event Participant Presence' . $event['datetime'] . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getInstructor_get($id_master)
    {
        $Data = $this->MasterModel->getInstructorExcel($id_master);

        if ($Data == null) {
            return;
        }
        $master = $Data['master'];
        $Members = $Data['member'];

        $spreadsheet = new Spreadsheet;

        $start = 3;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Certificate Number')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, $Data['dojo_name'])
            ->setCellValue('E' . $start, 'Username')
            ->setCellValue('F' . $start, 'Rank')
            ->setCellValue('G' . $start, 'Period')
            ->setCellValue('H' . $start, 'Expiration')
            ->setCellValue('I' . $start, 'Fee')
            ->setCellValue('J' . $start, 'Birth Date')
            ->setCellValue('K' . $start, 'Blood Group')
            ->setCellValue('L' . $start, 'Disease')
            ->setCellValue('M' . $start, 'Phone Number 1')
            ->setCellValue('N' . $start, 'Phone Number 2')
            ->setCellValue('O' . $start, 'Address');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('J' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('L' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('M' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('N' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('O' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($Members as $member) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $member['certificate_number'])
                ->setCellValue('C' . $coloum, $member['name'])
                ->setCellValue('E' . $coloum, $member['username'])
                ->setCellValue('F' . $coloum, $member['rank'])
                ->setCellValue('G' . $coloum, $member['period'])
                ->setCellValue('H' . $coloum, $member['expiration'])
                ->setCellValue('I' . $coloum, $member['fee'])
                ->setCellValue('J' . $coloum, $member['birth_date'])
                ->setCellValue('K' . $coloum, $member['blood'])
                ->setCellValue('L' . $coloum, $member['disease'])
                ->setCellValue('M' . $coloum, $member['phone1'])
                ->setCellValue('N' . $coloum, $member['phone2'])
                ->setCellValue('O' . $coloum, $member['address']);

            $dojos = $member['dojo'];
            $dojo_size = count($dojos);
            $merge = $coloum + $dojo_size - 1;

            $spreadsheet->getActiveSheet()->mergeCells('A' . $coloum . ':A' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('B' . $coloum . ':B' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('C' . $coloum . ':C' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('E' . $coloum . ':E' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('F' . $coloum . ':F' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('G' . $coloum . ':G' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('H' . $coloum . ':H' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('I' . $coloum . ':I' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('J' . $coloum . ':J' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('K' . $coloum . ':K' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('L' . $coloum . ':L' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('M' . $coloum . ':M' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('N' . $coloum . ':N' . $merge);
            $spreadsheet->getActiveSheet()->mergeCells('O' . $coloum . ':O' . $merge);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum . ':A' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum . ':B' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum . ':C' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum . ':E' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum . ':F' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum . ':G' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H' . $coloum . ':H' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('I' . $coloum . ':I' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('J' . $coloum . ':J' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('K' . $coloum . ':K' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('L' . $coloum . ':L' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('M' . $coloum . ':M' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('N' . $coloum . ':N' . $merge)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('O' . $coloum . ':O' . $merge)->applyFromArray($styleArray);

            foreach ($dojos as $dojo) {
                $spreadsheet->getActiveSheet()->setCellValue('D' . $coloum, $dojo['name']);
                $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
                $coloum++;
            }

            $number++;
        }

        $startValue = $start + 1;
        $spreadsheet->getActiveSheet()->getStyle('H' . $startValue . ':H' . $coloum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $spreadsheet->getActiveSheet()->getStyle('G' . $startValue . ':G' . $coloum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $spreadsheet->getActiveSheet()->getStyle('I' . $startValue . ':I' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':O' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 17; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'List of Instructors ' . $master);
        $spreadsheet->getActiveSheet()->mergeCells("A1:O1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->setTitle('List of Instructors');

        $writer = new Xlsx($spreadsheet);

        $filename = 'List of Instructors ' . $master . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getDojo_get($id_master)
    {
        $Data = $this->MasterModel->getDojoExcel($id_master);

        if ($Data == null) {
            return;
        }
        $master = $Data['master'];
        $dojos = $Data['dojo'];

        $spreadsheet = new Spreadsheet;

        $start = 3;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Name')
            ->setCellValue('C' . $start, 'Active Member')
            ->setCellValue('D' . $start, 'Inactive Member')
            ->setCellValue('E' . $start, 'Code of Join Request')
            ->setCellValue('F' . $start, 'Chairman')
            ->setCellValue('G' . $start, 'Treasurer')
            ->setCellValue('H' . $start, 'Secretary')
            ->setCellValue('I' . $start, 'Instructor')
            ->setCellValue('J' . $start, 'Assistant Instructor')
            ->setCellValue('K' . $start, 'Address')
            ->setCellValue('L' . $start, 'E-mail')
            ->setCellValue('M' . $start, 'PengProv')
            ->setCellValue('N' . $start, 'PengCab')
            ->setCellValue('O' . $start, 'Fee 1')
            ->setCellValue('P' . $start, 'Fee 2')
            ->setCellValue('Q' . $start, 'Fee 3')
            ->setCellValue('R' . $start, 'Latitude')
            ->setCellValue('S' . $start, 'Langitude');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('J' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('L' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('M' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('N' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('O' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('P' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('Q' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('R' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('S' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;

        foreach ($dojos as $dojo) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $dojo['name'])
                ->setCellValue('C' . $coloum, $dojo['active_member'])
                ->setCellValue('D' . $coloum, $dojo['inactive_member'])
                ->setCellValue('E' . $coloum, $dojo['request_code'])
                ->setCellValue('F' . $coloum, $dojo['head'])
                ->setCellValue('G' . $coloum, $dojo['treasurer'])
                ->setCellValue('H' . $coloum, $dojo['secretary'])
                ->setCellValue('I' . $coloum, $dojo['instructor'])
                ->setCellValue('J' . $coloum, $dojo['assistant'])
                ->setCellValue('K' . $coloum, $dojo['address'])
                ->setCellValue('L' . $coloum, $dojo['email'])
                ->setCellValue('M' . $coloum, $dojo['pengprov'])
                ->setCellValue('N' . $coloum, $dojo['pengcab'])
                ->setCellValue('O' . $coloum, $dojo['fee1'])
                ->setCellValue('P' . $coloum, $dojo['fee2'])
                ->setCellValue('Q' . $coloum, $dojo['fee3'])
                ->setCellValue('R' . $coloum, $dojo['latitude'])
                ->setCellValue('S' . $coloum, $dojo['longitude']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('I' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('J' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('K' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('L' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('M' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('N' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('O' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('P' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('Q' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('R' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('S' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
        }

        $startValue = $start + 1;
        $spreadsheet->getActiveSheet()->getStyle('O' . $startValue . ':O' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');
        $spreadsheet->getActiveSheet()->getStyle('P' . $startValue . ':P' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');
        $spreadsheet->getActiveSheet()->getStyle('Q' . $startValue . ':Q' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':S' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 20; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $master . ' ' . $Data['dojo_name'] . 's List');
        $spreadsheet->getActiveSheet()->mergeCells("A1:S1");
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Dojo ' . $master . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function getHistoryFee_get($id_dojo, $minDate, $maxDate)
    {
        $Data = $this->HeadModel->getHistoryFeeExcel($id_dojo, $minDate, $maxDate);

        if ($Data == null) {
            return;
        }

        $title = "Payment History";
        if ($minDate != 0 && $maxDate != 0) {
            $title = $title . " " . $minDate . " - " . $maxDate;
        } else if ($minDate != 0) {
            $title = $title . " After " . $minDate;
        } else if ($maxDate != 0) {
            $title = $title . " Before " . $maxDate;
        }

        $spreadsheet = new Spreadsheet;

        $start = 3;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, 'No')
            ->setCellValue('B' . $start, 'Payment Date')
            ->setCellValue('C' . $start, 'Name')
            ->setCellValue('D' . $start, 'Role')
            ->setCellValue('E' . $start, 'Late Payment')
            ->setCellValue('F' . $start, 'Months')
            ->setCellValue('G' . $start, 'Amount Paid');

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $start)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $start)->applyFromArray($styleArray);

        $coloum = $start + 1;
        $number = 1;
        $total = 0;

        foreach ($Data as $dojo) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $coloum, $number)
                ->setCellValue('B' . $coloum, $dojo['date'])
                ->setCellValue('C' . $coloum, $dojo['name'])
                ->setCellValue('D' . $coloum, $dojo['role'])
                ->setCellValue('E' . $coloum, $dojo['late'])
                ->setCellValue('F' . $coloum, $dojo['month'])
                ->setCellValue('G' . $coloum, $dojo['fee']);

            $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);

            $coloum++;
            $number++;
            $total = $total + $dojo['fee'];
        }

        $spreadsheet->getActiveSheet()->getStyle('A' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F' . $coloum)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G' . $coloum)->applyFromArray($styleArray);

        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $coloum, 'Total');
        $spreadsheet->getActiveSheet()->mergeCells("A" . $coloum . ":F" . $coloum);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $coloum, $total);
        $coloum++;

        $startValue = $start + 1;
        $spreadsheet->getActiveSheet()->getStyle('G' . $startValue . ':G' . $coloum)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A' . $start . ':G' . $start)->applyFromArray($styleArray);

        for ($i = 0; $i < 20; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $title);
        $spreadsheet->getActiveSheet()->mergeCells("A1:G1");

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);

        $filename = $title . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
