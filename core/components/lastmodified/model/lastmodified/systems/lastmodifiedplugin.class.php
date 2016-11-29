<?php

abstract class lastModifiedPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var lastModified $lm */
    protected $lm;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->modx = &$modx;
        $this->scriptProperties =& $scriptProperties;
        $this->lm = $this->modx->lastmodified;

        if (!$this->lm OR !($this->lm instanceof lastModified)) {
            return false;
        }

    }

    abstract public function run();
}