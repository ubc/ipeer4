<?php
if (!function_exists('escapeLike')) {
    /**
     * Escape special characters used for db query's LIKE operator. This is:
     * % match any number of characters
     * _ matches any single character
     *
     * From: https://stackoverflow.com/a/42028380
     *
     * @param $value - the search term
     * @param $escape - The \ character is the default escape char, but it can
     * be configured to some other character so it's a configurable parameter
     * here.
     * @return boolean
     */
    function escapeLike(string $value, string $escape = '\\'): string
    {
        return str_replace(
            [$escape, '%', '_'],
            [$escape.$escape, $escape.'%', $escape.'_'],
            $value
        );
    }
}
