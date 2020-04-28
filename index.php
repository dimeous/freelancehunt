<?php

require_once 'dbi.php';
require_once 'lib.php';

//заполнение базы с апи
if (isset($_GET['fill'])){
   require_once 'fill_database.php';
    exit();
}

//круговая диаграмма
if (isset($_GET['pie'])){
    require_once 'pie.php';
    exit();
}
// таблица
$db = new HS2_DB();

$id_field='id';
$table='Projects';

// выбираем проекты нужных категорий
$prj_ids = $db->fetchRows($db->select('Skills_prj','project_id',
                'skill_id=1 or skill_id=99 or skill_id=86',array(),'','','project_id'
                        ))  ;
$prj_ids2=[];
foreach ($prj_ids as $v)
    $prj_ids2[]=$v['project_id'];


$list = opPageGet(_GETN('page'), 10, "$table",
    "*",
    "id ?i",
    array($prj_ids2),
    array(
        'id' => array('id desc', 'id'),
        'amount' => array('amount desc', 'amount'),
        'curr' => array('curr desc', 'curr')
    ),
    _GET('sort'), $id_field
);

//xstop($list);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <title>Frelancehunt API</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<div id="wrapper">
    <div id="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active"><a href="/"  class="nav-link">Главная</a></li>
                <li  class="nav-item"><a href="/?pie"  class="nav-link">Pie chart</a></li>
                <li  class="nav-item"><a href="/?fill"  class="nav-link">Заполнить базу</a></li>
            </ul>

        </nav>
    </div>
    <div id="page">

        <div id="content">
            <div class="box">
                <?php
                    include 'table_view.php';
                ?>
            </div>

        </div>

    </div>

</div>
<div id="footer" class="col-5 offset-md-4">
    All rights reserved 2020
</div>
</body>
</html>
