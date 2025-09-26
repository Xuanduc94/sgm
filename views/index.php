<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SGM-Quản lý trình ký công trình</title>
    <link rel="stylesheet" href="public/lib/bulma/css/bulma.css" />

    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
</head>

<body>
    <div class="container">
        <div class="fixed-grid has-1-cols">
            <div class="grid">
                <div class="cell">
                    <h3 class="text-center">SGM-Quản lý trình ký công trình</h3>
                </div>
                <div class="cell">
                    <div class="grid">
                        <div class="cell">
                            <label for="">Nhập dữ liệu</label>
                            <input accept=".xls,.xlsx" required type="file" name="file" id="file-excel" />
                            <input onclick="submitForm()" class="button is-small is-info" type="button" value="Nhập dữ liệu">
                        </div>

                        <div class="cell">
                            <label for="">Thời gian</label>
                            <input class="is-small" type="date" id="minDate" value="<?php echo date('Y-m-d') ?>" />
                            -
                            <input class="is-small" type="date" id="maxDate" value="<?php echo date('Y-m-d') ?>" />
                        </div>
                        <div class="cell">
                            <a href="public/files/template.xlsx" class="button is-small is-info">Tải biểu mẫu</a>
                        </div>
                    </div>
                </div>
                <div class="cell">
                    <table id="construction">
                        <thead class="thead-light">
                            <tr>
                                <th>Mã công trình</th>
                                <th>Mã trạm</th>
                                <th>Ngày ký</th>
                                <th>Người ký</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="public/js/http_client.js"></script>
<script src="public/js/index.js"></script>
<link rel="stylesheet" href="public/css/index.css" />

</html>