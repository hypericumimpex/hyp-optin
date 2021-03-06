<?php

/*
 * Copyright (C) 2013-2016 Mailgun
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace MPP_Mailgun\Hydrator;

use MPP_Mailgun\Exception\HydrationException;
use MPP_Mailgun\Model\ApiResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Serialize an HTTP response to domain object.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ModelHydrator implements Hydrator
{
    /**
     * @param ResponseInterface $response
     * @param string            $class
     *
     * @return ResponseInterface
     */
    public function hydrate(ResponseInterface $response, $class)
    {
        $body = $response->getBody()->__toString();
        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== 0 && strpos($contentType, 'application/octet-stream') !== 0) {
            throw new HydrationException('The ModelHydrator cannot hydrate response with Content-Type: '.$contentType);
        }

        $data = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new HydrationException(sprintf('Error (%d) when trying to json_decode response', json_last_error()));
        }

        if (is_subclass_of($class, ApiResponse::class)) {
            $object = call_user_func($class.'::create', $data);
        } else {
            $object = new $class($data);
        }

        return $object;
    }
}
