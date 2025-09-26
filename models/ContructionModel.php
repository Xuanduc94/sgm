<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class ContructionModel extends Model
{

    public function getAll()
    {
        $data =  $this->database->select('contrucstion', "*");
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
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $this->database->action(function () use ($highestRow, $worksheet) {
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $data = [
                                'WBS' => $worksheet->getCell("A{$row}")->getValue(),
                                'FunctionCode' => $worksheet->getCell("B{$row}")->getValue(),
                                'Date' => $worksheet->getCell("C{$row}")->getValue(),
                                'Status' => 1,
                                'User' => $worksheet->getCell("D{$row}")->getValue(),
                            ];
                            $this->database->insert('contrucstion', $data);
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
