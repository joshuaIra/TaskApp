<?php

if (! function_exists('mb_split')) {
    function mb_split(string $pattern, string $string, int $limit = -1)
    {
        $delimiter = '/';
        $escapedPattern = str_replace($delimiter, '\\'.$delimiter, $pattern);
        $regex = $delimiter.$escapedPattern.$delimiter.'u';

        return preg_split($regex, $string, $limit);
    }
}
