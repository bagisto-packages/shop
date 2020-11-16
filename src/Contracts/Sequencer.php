<?php

namespace BagistoPackages\Shop\Contracts;

interface Sequencer
{
    /**
     * create and return the next sequence number for e.g. an order
     *
     * @return string
     */
    public static function generate(): string;
}
