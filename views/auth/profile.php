<?php if(empty($_SESSION['user'])): ?>
    <?php $_SESSION['msg'] = 'Пожалуйста, авторизируйтесь.' ?>
    <?php header('Location: /'); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
</body>
<body>
<form>
    <a href="#"><?= $_SESSION['user']['name'] ?></a>
    <a href="#"><?= $_SESSION['user']['phone_number'] ?></a>
    <a href="#"><?= $_SESSION['user']['email'] ?></a>
    <img width="200" height="200" src="<?= $_SESSION['user']['avatar'] ?>" alt="">
</form>

</form>
</body>
</html>