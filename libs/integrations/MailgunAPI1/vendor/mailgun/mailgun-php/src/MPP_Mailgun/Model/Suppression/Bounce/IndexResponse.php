<?php

/*
 * Copyright (C) 2013-2016 Mailgun
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace MPP_Mailgun\Model\Suppression\Bounce;

use MPP_Mailgun\Model\ApiResponse;
use MPP_Mailgun\Model\PaginationResponse;
use MPP_Mailgun\Model\PagingProvider;

/**
 * @author Sean Johnson <sean@mailgun.com>
 */
final class IndexResponse implements ApiResponse, PagingProvider
{
    use PaginationResponse;

    /**
     * @var Bounce[]
     */
    private $items;

    /**
     * @param Bounce[] $items
     * @param array    $paging
     */
    private function __construct(array $items, array $paging)
    {
        $this->items = $items;
        $this->paging = $paging;
    }

    /**
     * @param array $data
     *
     * @return IndexResponse
     */
    public static function create(array $data)
    {
        $bounces = [];
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $bounces[] = Bounce::create($item);
            }
        }

        return new self($bounces, $data['paging']);
    }

    /**
     * @return Bounce[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
