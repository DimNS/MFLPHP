<?php
/**
 * Форматирование временной метки в дату для записи в базу
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class FormatTime
{
    /**
     * Конвертер
     *
     * @param integer $timestamp Временная метка
     *
     * @return string
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function convert($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
