<?php

namespace Ubermanu\Flamingo\Helper;

/**
 * Interface HelperInterface
 * @package Ubermanu\Flamingo\Helper
 */
interface HelperInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function run($data);
}