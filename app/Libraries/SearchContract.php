<?php
namespace App\Libraries;

interface SearchContract
{
    /**
     * @param $q string Query (could be hashtag or username)
     * @param $num int num of returned result
     * @param $next_cursor string
     * @param $first_cursor string
     *
     * @return Collection
     */
    public function search($q, $num = 20, $next_cursor = null, $first_cursor = null);
}