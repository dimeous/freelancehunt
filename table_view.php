<h1>Таблица открытых проектов в категориях Веб-программирование, PHP и Базы данных: </h1>
<table class="table">

    <?php

        $str='';
        $_selfLink='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            if ($a = explode('?', $_selfLink))
                $_selfLink = $a[0];
        $linkparams=(isset($_GET['page']))?"&page=".$_GET['page']:'';
        $pl_params= $list[1];
        $fields=[
                'id'=>['ID'],
                'name'=>['Название'],
                'amount'=>['Сумма'],
                'curr'=>['Валюта'],
                'em_login'=>['Login'],
                'em_first_name'=>['Имя'],
                'em_last_name'=>['Фамилия'],
                ];
		foreach ($fields as $f=>$v) {
            $str .= '<th class="header">';
            if ($pl_params['Orders'][$f]) {
                if (textLeft($pl_params['Order'], -1) == $f) {
                    $z = 1 + textRight($pl_params[Order], 1);
                    $str .= "<sup>$z</sup>";
                } else
                    $z = '';
                $str .= "<a href='$_selfLink?sort=$f$z$linkparams'>{$v[0]}	</a>";
            }
            else
                $str .= $v[0];

            }
             $str .=   '</th >';
		echo $str;

    foreach($list[0] as $row)
    {
        echo
            '<tr><td>'.$row['id'].'</td>
                <td><a href="'.$row['self_link'].'">'.$row['name'].'</a></td>
                <td>'.$row['amount'].'</td>
                <td>'.$row['curr'].'</td>
                <td>'.$row['em_login'].' </td>
                <td>'.$row['em_first_name'].' </td>
                <td>'.$row['em_last_name'].' </td>
            </tr>';
    }
    $str='';
    $linkparams=(isset($_GET['sort']))?"&sort=".$_GET['sort']:'';
    if (count($pl_params['Pages']) > 0){
        $str.='<nav aria-label="Страницы">
                 <ul class="pagination">
                 <li class="page-item disabled justify-content-center">
        <a class="page-link">Страница '.$pl_params['Page'].' из '.$pl_params['PagesCount'].'</a></li>';

        if (count($pl_params['Pages']) > 0)
            $str.='&nbsp;&nbsp;&nbsp;';
        foreach ($pl_params['Pages'] as $i=>$pn) {

            $str .= ' <li class="page-item"><a class="page-link" href="' . $_selfLink . '?page=' . $pn[1] . $linkparams . '" class="' ;
            $str .=(($pn[1] == $pl_params['Page']) ? 'pgactive' : 'pgbutton' ). '">';
            if ($pn[0] == '&lt;&lt;')
                $str .= 'Первая';
            elseif ($pn[0] == '&lt;')
                $str .= 'Назад';
            elseif ($pn[0] == '&gt;')
                $str .= 'Вперед';
            elseif ($pn[0] == '&gt;&gt;')
                $str .= 'Последняя';
            else
                $str .= $pn[0] . '</a></li>';
        }
        $str .='</ul></nav>';
        echo $str;
    }
    ?>
</table>
