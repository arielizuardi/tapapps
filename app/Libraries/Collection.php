<?php
namespace App\Libraries;

class Collection
{
    public $items;
    public $next_url;
    public $refresh_url;

    public function __construct(array $items, $next_url = null, $refresh_url = null)
    {
        $this->items = $items;
        $this->next_url = $next_url;
        $this->refresh_url = $refresh_url;
    }

    public function toArray()
    {
        if (empty($this->items)) {
            $this->items = [];
        }

        return [
           'data' => $this->items,
           'next_url' => $this->next_url,
           'refresh_url' => $this->refresh_url
        ];
    }


}