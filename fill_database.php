<?php
//Заполняем БД в соответствии со структурой
require('_dbstructure.php');
require_once 'FreelanceAPI.php';
$token='7a52c2cb1b22f37d259a4aa6a9e3f0f5f3fb2776';

$db = new HS2_DB();
$dbn='foothold_db';
$db->query("ALTER DATABASE $dbn DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
$ts = $db->fetchRows($db->query('SHOW TABLES'));
if (count($ts) > 0)
    foreach ($ts as $t)
        $db->query('DROP TABLE IF EXISTS ' . reset($t));
foreach ($_dbstru as $t => $cmnd)
    $db->query("CREATE TABLE $t ($cmnd) CHARACTER SET utf8 COLLATE utf8_general_ci");


$api = new FreelanceAPI($token);

//заполняем скилзы
$skills=$api->getSkills();
foreach ($skills['data'] as $skill) {
    if($skill['id']){
        $data = array(
            'skill_id'=>$skill['id'],
            'skill_name'=>$skill['name']
        );
        $db->insert('Skills_tbl',$data);
    }
}

//заполняем проекты
$result=$api->getProjects(0);
//определяем последнюю страницу
$last_page=explode('=',$result['links']['last']);
$last_page=$last_page[1];
//парсим данные и запоняем в бд
$saved=[];
for ($i=1;$i<=$last_page;$i++){
    $result=$api->getProjects($i);
    foreach ($result['data'] as $prj) {
        if($prj['id']){
            //сохраняем данные проекта
            $data = array(
                'id'=>$prj['id'],
                'name'=>$prj['attributes']['name'],
                'self_link'=>$prj['links']['self']['web'],
                'em_login'=>$prj['attributes']['employer']['login'],
                'em_first_name'=>$prj['attributes']['employer']['first_name'],
                'em_last_name'=>$prj['attributes']['employer']['last_name'],
                'amount'=>$prj['attributes']['budget']['amount'],
                'curr'=>$prj['attributes']['budget']['currency'],
            );

            if(!isset($saved[$prj['id']])){
                $db->insert('Projects', $data);
                $saved[$prj['id']] = 1;
            }
            //заполняем скилзы проекта
            foreach ($prj['attributes']['skills'] as $skill){
                $data = array(
                    'skill_id'=>$skill['id'],
                    'project_id'=>$prj['id']
                );
                $db->insert('Skills_prj',$data);
            }
        }
    }
}

echo "<a href='/'> End. Вернуться";
die();
