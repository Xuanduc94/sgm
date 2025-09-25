<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class ContructionModel extends Model
{

    public function getAll()
    {
        $data =  $this->database->get('contrucstion', ['WBS', 'FunctionCode', 'Date', 'Status', 'User']);
        return $data;
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
                    // Đọc file Excel
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $data = [
                            'WBS' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                            'FunctionCode' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                            'Date' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                            'Status' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                            'User' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                        ];
                        $this->database->insert('contrucstion', $data);
                    }
                } catch (Exception $e) {
                    die("Lỗi đọc file: " . $e->getMessage());
                }
            } else {
                return "Chưa chọn file hoặc có lỗi khi upload.";
            }
        }
       return header("Refresh:0");
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
