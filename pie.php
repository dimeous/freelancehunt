<?php

// таблица
$db = new HS2_DB();
$table='Projects';
$rawData = file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5');
$data = json_decode($rawData, true);
$curr=[];
foreach ($data as $v){
    if ($v['base_ccy']=='UAH')
    $curr[$v['ccy']]=$v['buy'];
}

$amount=500;
$a=[];
$a[]=$db->count($table,"(curr='UAH' and amount<$amount)
                                or (curr='EUR' and amount<=$amount/{$curr['EUR']}) 
                                or (curr='USD' and amount<=$amount/{$curr['USD']}) 
                                or (curr='RUB' and amount<=$amount/{$curr['RUR']})");
$amount=500;
$amount2=1000;
$a[]=$db->count($table,"(curr='UAH'  and amount<$amount2 and amount>=$amount)
                                or (curr='EUR' and amount<=$amount2/{$curr['EUR']} and amount>$amount/{$curr['EUR']}) 
                                or (curr='USD' and amount<=$amount2/{$curr['USD']} and amount>$amount/{$curr['USD']}) 
                                or (curr='RUB' and amount<=$amount2/{$curr['RUR']} and amount<=$amount/{$curr['RUR']})");
$amount=1000;
$amount2=5000;
$a[]=$db->count($table,"(curr='UAH' and amount<$amount2 and amount>=$amount)
                                or (curr='EUR' and amount<=$amount2/{$curr['EUR']} and amount>$amount/{$curr['EUR']}) 
                                or (curr='USD' and amount<=$amount2/{$curr['USD']} and amount>$amount/{$curr['USD']}) 
                                or (curr='RUB' and amount<=$amount2/{$curr['RUR']} and amount<=$amount/{$curr['RUR']})");
$amount=5000;
$a[]=$db->count($table,"(curr='UAH' and amount>$amount)
                                or (curr='EUR' and amount>$amount/{$curr['EUR']}) 
                                or (curr='USD' and amount>$amount/{$curr['USD']}) 
                                or (curr='RUB' and amount>$amount/{$curr['RUR']})");

$data = "{   name: '<500 грн',  y: {$a[0]}    },
            {   name: '500-1000 грн',  y: {$a[1]}    },
            {   name: '1000-5000 грн',  y: {$a[2]}    },
            {   name: ' > 5000 грн',  y: {$a[3]}    }
            ";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <title>Frelancehunt API</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
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
                <div id="container" style="width:100%; height:400px;"></div>
                <script>
                    Highcharts.chart('container', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Соотношение проектов'
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>  <br>{point.y}шт.'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} % '
                                }
                            }
                        },
                        series: [{
                            name: 'Проекты',
                            colorByPoint: true,
                            data: [   <?php
                               echo $data;
                                ?>]
                        }]
                    });
                    </script>
            </div>

        </div>

    </div>

</div>
<div id="footer" class="col-5 offset-md-4">
    All rights reserved 2020
</div>
</body>
</html>
