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

                            <button onclick="openModalAdd()" class="button is-primary is-small">Nhập trình ký</button>
                        </div>

                        <div class="cell">
                            <label for="">Thời gian</label>
                            <input class="is-small" type="date" id="minDate" value="<?php echo date('Y-m-01') ?>" />
                            -
                            <input class="is-small" type="date" id="maxDate" value="<?php echo date('Y-m-d') ?>" />

                            <a href="public/files/template.xlsx">Tải biểu mẫu</a>
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
        <!-- Modal -->
        <div class="modal" id="addModal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Nhập trình ký</p>
                    <button onclick="closeModalAdd()" class="delete" aria-label="close" id="closeModal"></button>
                </header>
                <section class="modal-card-body">
                    <div class="grid ">
                        <div class="cell">
                            <div class="control mb-3">
                                <input type="text" id="WBS" class="input" placeholder="Mã công trình" />
                            </div>
                            <div class="control mb-3">
                                <input id="FunctionCode" class="input" type="text" placeholder="Mã trạm" />
                            </div>
                            <div class="control mb-3">
                                <input id="Date" type="date" value="<?php echo date('Y-m-d') ?>" class="input" placeholder="Ngày ký" />
                            </div>
                            <div class="control mb-3">
                                <input id="User" type="text" class="input" placeholder="Người ký" />
                            </div>
                        </div>

                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button onclick="save()" class="button is-success mr-2">Lưu</button>
                    <button onclick="closeModalAdd()" class="button" id="cancelModal">Hủy</button>
                </footer>
            </div>
        </div>
        <p style="text-align: center;">Copyright © <?php echo date('Y') ?> By Nekonekonomi</p>
    </div>

</body>
<script src="public/js/http_client.js"></script>
<script src="public/js/index.js"></script>
<link rel="stylesheet" href="public/css/index.css" />

</html>