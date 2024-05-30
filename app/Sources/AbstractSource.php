<?php

namespace App\Sources;

abstract class AbstractSource
{

    protected $sourceTitle = '';
    public function __construct() {}

    abstract public function query($isbn): array;

    /**
     * @return string
     */
    public function getSourceTitle(): string
    {
        return $this->sourceTitle;
    }

    /**
     * @param string $sourceTitle
     */
    public function setSourceTitle(string $sourceTitle): void
    {
        $this->sourceTitle = $sourceTitle;
    }


}
