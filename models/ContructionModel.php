<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class ContructionModel extends Model
{

    public function getAll()
    {
        $data =  $this->database->select('contrucstion', "*");
        return $data;
    }

    public function inputWBS()
    {
        $data = [
            'WBS' => $_POST['WBS'],
            'FunctionCode' =>  $_POST['FunctionCode'],
            'Date' =>  $_POST['Date'],
            'Status' => 1,
            'User' => $_POST['User'],
        ];
        $check = $this->database->has('contrucstion', ['WBS' => $data['WBS']]);
        if ($check == false) {
            $this->database->insert('contrucstion', $data);
        }
        return "OK";
    }
    function convertFormatDate($date)
    {
        $date = DateTime::createFromFormat("d/m/Y", $date);
        return $date->format("Y-m-d");
    }
    public function insert()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['file']['tmp_name'];
                $fileName = $_FILES['file']['name'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                // Kiểm tra định dạng file
                $allowedExtensions = ['xls', 'xlsx'];
                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    die("Chỉ chấp nhận file Excel (.xls, .xlsx)");
                }
                try {
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $this->database->action(function () use ($highestRow, $worksheet) {
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $date = $this->convertFormatDate(trim($worksheet->getCell("C{$row}")->getValue()));
                            $data = [
                                'WBS' => trim($worksheet->getCell("A{$row}")->getValue()),
                                'FunctionCode' => trim($worksheet->getCell("B{$row}")->getValue()),
                                'Date' => $date,
                                'Status' => 1,
                                'User' => $worksheet->getCell("D{$row}")->getValue(),
                            ];
                            $check = $this->database->has('contrucstion', ['WBS' => trim($worksheet->getCell("A{$row}")->getValue())]);
                            if ($check == false) {
                                $this->database->insert('contrucstion', $data);
                            }
                        }
                    });
                } catch (Exception $e) {
                    die("Lỗi đọc file: " . $e->getMessage());
                }
            } else {
                echo "Chưa chọn file hoặc có lỗi khi upload.";
            }
        }
    }

    public function update()
    {
        $data = json_decode($_GET['data']);
        $id = $_GET['id'];
        $this->database->update('contrucstion', $data, $id);
        return $this->database;
    }


    public function delete()
    {
        $id = $_GET['id'];
        $this->database->delete('contrucstion', $id);
        return "success";
    }
}
