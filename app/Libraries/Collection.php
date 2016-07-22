<?php
namespace App\Libraries;

class Collection
{
    protected $items;
    protected $next_url;
    protected $refresh_url;

    public function __construct(array $items, $next_url = null, $refresh_url = null)
    {
        $this->items = $items;
        $this->next_url = $next_url;
        $this->refresh_url = $refresh_url;
    }

    public function toArray()
    {
        return [
           'data' => $this->items,
           'next_url' => $this->next_url,
           'refresh_url' => $this->refresh_url
        ];
    }


}