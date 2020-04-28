<?php

function validMail($s)
{
    $mask = '|^.+@.+\..+$|';
    return preg_match($mask, $s);
}

function textLeft($txt, $cnt = 1)
{
    return substr($txt, 0, $cnt);
}

function textRight($txt, $cnt = 1)
{
    return substr($txt, -$cnt);
}


function ss1Elem(&$s)
{
    if (!is_array($s))
        $s = stripslashes($s);
    else
        foreach ($s as $i => $v)
            ss1Elem($s[$i]);
}

function mTrim(&$s)
{
    if (!is_array($s))
        $s = trim($s);
    else
        foreach ($s as $i => $v)
            mTrim($s[$i]);
}


function fromGPC($s) // if from GET/POST/COOKIE
{
    if (!is_null($s))
    {
        if (get_magic_quotes_gpc())
            ss1Elem($s); // strip slashes
        mTrim($s); // trim
    }
    return $s;
}

function _COOKIE($p, $mask = '')
{
    return (isset($_COOKIE[$p]) ? filterInput(fromGPC($_COOKIE[$p]), $mask) : NULL);
}


function filterInput($s, $mask = '')
{
    if (is_null($s) or !$mask)
        return $s;
    if ($mask == '*')
        return strip_tags($s);
    preg_match('/^' . $mask . '$/', $s, $a);
    return $a[0];
}


function _GET($p, $mask = '')
{
    return (isset($_GET[$p]) ? filterInput(fromGPC($_GET[$p]), $mask) : NULL);
}
function _GETN($p)
{
    return intval(@$_GET[$p]);
}

function sEmpty($s)
{
    return ('' === trim($s));
}

function xEcho()
{
    global $_GS;
    foreach ((func_num_args() ? func_get_args() : array('- - - - -')) as $message)
    {
        if (is_array($message) or is_object($message))
            $message = print_r($message, true);
        $message .= HS2_NL;
        if (!$_GS['as_term'])
            $message = nl2br($message);
        echo($message);
    }
}

function xStop()
{
    foreach (func_get_args() as $message)
        xEcho($message);
    exit;
}
function xSysInfo($message, $type = 0)
{
    $_GS['info'][$type][] = $message;
    if ($type < 2)
        return;
    xAddToLog($message, 'system');
    if ($type == 2)
        xAbort($message);
    xStop($message);
}
function xAbort($message = '')
{
    throw new Exception($message);
}
function xSysWarning($message)
{
    xSysInfo($message, 1);
}

function xSysError($message)
{
    xSysInfo($message, 2);
}


function xAddToLog($message, $topic = '', $clear_before = false)
{
// log
}

function asArray($a, $dlm = ',', $skip_empty = true)
{
    if (is_array($a))
        return $a;
    $r = array();
    foreach (explode($dlm, $a) as $v)
    {
        $v = trim($v);
        if (!$skip_empty or !sEmpty($v))
            $r[] = $v;
    }
    return $r;
}



function opPageGet($page, $page_size, $table,
                   $fields = '*', $filter = '', $ph_values = null, $orders = array(), $order = '', $id_field = HS2_DB_DEF_ID_FIELD)
{
    $page_range = 10; // line size
    global $db, $_GS;
    foreach ($orders as $f => $a)
        if (!$a)
            if ($f == $id_field)
                $orders[$f] = array("$f desc", $f);
            else
                $orders[$f] = array($f, "$f desc");
    $form = 'main';
    $pl = @$_SESSION['_PL'][$form];
    $params = array('Orders' => $orders, 'Order' => '');
    if ($orders)
    {
        if (!$order)
        {
            $order = $pl['Order'];
            if (!$order)
                $order = key($orders);
        }
        else
            $pl['Page'] = 0;
        $a = textRight($order, 1);
        if (is_numeric($a))
            $order = textLeft($order, -1);
        if (!$orders[$order][$a])
            $a = 0;
        if ($sorder = $orders[$order][$a])
        {
            $order .= $a;
            $pl['Order'] = $order;
            $params['Order'] = $order;
        }
    }
    else
        $sorder = '';
    $rows_count = $db->count($table, $filter, $ph_values);
    $pages_count = ceil($rows_count / $page_size);
    $params['PagesCount'] = $pages_count;
    if ($page == 0)
        $page = @$pl['Page'];
    $page = 0 + $page;
    if ($page > $pages_count)
        $page = $pages_count;
    if ($page < 1)
        $page = 1;
    $pl['Page'] = $page;
    $params['Page'] = $page;
    if ($pages_count > 1)
    {
        $ir2 = floor(($page_range - 1) / 2);
        $ir = 2 * $ir2; // index range
        if ($pages_count <= ($ir + 1))
        {
            $is = 1;
            $ie = $pages_count;
        }
        else
        {
            $is = $page - $ir2; // index start
            if ($is < 1)
                $is = 1;
            elseif (($is + $ir) > $pages_count)
            {
                $is = $pages_count - $ir;
                if ($is < 1)
                    $is = 1;
            }
            $ie = $is + $ir; // range done (index end)
        }
        $pages = array();
        if ($is > 1)
            $pages[] = array('<<', 1);
        if ($page > 1)
            $pages[] = array('<', $page - 1);
        if ($page - 50 > 1)
            $pages[] = array($page - 50, $page - 50);
        if ($page - 10 > 1)
            $pages[] = array($page - 10, $page - 10);
        for ($i = $is; $i <= $ie; $i++)
            $pages[] = array($i, $i);
        if ($page + 10 < $pages_count)
            $pages[] = array($page + 10, $page + 10);
        if ($page + 50 < $pages_count)
            $pages[] = array($page + 50, $page + 50);
        if ($page < $pages_count)
            $pages[] = array('>', $page + 1);
        if ($ie < $pages_count)
            $pages[] = array('>>', $pages_count);
        $params['Pages'] = $pages;
    }
    $_SESSION['_PL'][$form] = $pl;
    return array($db->fetchIDRows(
        $db->select($table, $fields, $filter, $ph_values, $sorder,
            '' . (($page - 1) * $page_size) . ',' . $page_size), false, $id_field),$params);
}


function inet_request($url, $par = array(), $cookiefl = '', $agent = '', $onlyheader = false) {
    global $ch;
    try {
        if ($ch < 0) xAbort();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        curl_setopt($ch, CURLOPT_HEADER, $onlyheader);
        curl_setopt($ch, CURLOPT_USERAGENT, ($agent ? $agent : 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13'));
        if ($onlyheader)
            curl_setopt($ch, CURLOPT_NOBODY, true);
        elseif (empty($par))
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $par);
        }
        @curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefl);
        @curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefl);
        $answ = curl_exec($ch);
        if (curl_errno($ch) != 0) $answ = false;
    } catch (Exception $e) {
        $answ = false;
    }
    return $answ;
}
