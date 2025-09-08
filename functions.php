<?php
function rdate($format, $datetime = 'default'): string
{
    if ($datetime == 'default') $datetime = new Datetime();

    $result = '';
    $monthA = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    $monthB = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $weekA = ['', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    $weekB = ['', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье'];
    $weekC = ['', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];

    foreach (str_split($format) as $f) {
        switch ($f) {
            case 'F':
                $result .= $monthA[$datetime->format('n')];
                break;
            case 'f':
                $result .= $monthB[$datetime->format('n')];
                break;
            case 'D':
                $result .= $weekA[$datetime->format('N')];
                break;
            case 'l':
                $result .= $weekB[$datetime->format('N')];
                break;
            case 'w':
                $result .= $weekC[$datetime->format('N')];
                break;
            default:
                $result .= $datetime->format($f);
                break;
        }
    }

    return $result;
}