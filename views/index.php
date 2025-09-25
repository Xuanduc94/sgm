<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SGM-Quản lý trình ký công trình</title>
    <link rel="stylesheet" href="public/lib/bulma/css/bulma.css" />

    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
    <script src="https://unpkg.com/gridjs-jquery/dist/gridjs.production.min.js"></script>
    <link
        rel="stylesheet"
        type="text/css"
        href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" />
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
                            <form id="formImport" method="post" action="http://<?php echo $_SERVER['HTTP_HOST'] . '?c=contrucstion&a=insert' ?>">
                                <label for="">Nhập dữ liệu</label>
                                <input onchange="submitForm()" type="file" name="file" id="" />
                            </form>
                        </div>

                        <div class="cell">
                            <label for="">Thời gian</label>
                            <input class="is-small" type="date" name="" value="<?php echo date('Y-m-d') ?>" />
                            -
                            <input class="is-small" type="date" name="" value="<?php echo date('Y-m-d') ?>" />
                        </div>
                    </div>
                </div>
                <div class="cell">
                    <div id="construction"></div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="public/js/http_client.js"></script>
<script src="public/js/index.js"></script>
<link rel="stylesheet" href="public/css/index.css" />

</html>