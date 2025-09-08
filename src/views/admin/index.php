<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Загрузка товаров</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/src/assets/js/admin.js"></script>
</head>
<body>
<form action="/api/import.php" method="POST" class="ajaxForm" enctype="multipart/form-data">
    <?php set_csrf();?>
    <input type="file" accept=".csv, .json" name="file">
    <button type="submit">Отправить</button>
</form>
</body>
</html>



