<?php
namespace App\Libraries;

interface SearchContract
{
    /**
     * @param $q string Query (could be hashtag or username)
     *
     * @return array of Item
     */
    public function search($q);
}