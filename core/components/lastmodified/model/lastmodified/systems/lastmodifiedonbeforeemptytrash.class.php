<?php

class lastModifiedOnBeforeEmptyTrash extends lastModifiedPlugin
{
    public function run()
    {
        $ids = (array)$this->modx->getOption('ids', $this->scriptProperties, array(), true);
        $this->modx->removeCollection('lastModifiedHash', array('rid:IN' => $ids));
    }

}