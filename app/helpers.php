<?php

/**
 *
 *
 * @param string $str
 *
 * @return string
 */
function sanitize(string $str): string
{
    return preg_replace('/\R/u', '\n', $str);
}
