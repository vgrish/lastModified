<?php

class lastModifiedOnDocFormSave extends lastModifiedPlugin
{
    public function run()
    {
        if (!$this->lm->getOption('working_on_resource_save', null)) {
            return;
        }

        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties, null, true);
        if (!$this->lm->isWorkingResource($resource)) {
            return;
        }

        $this->lm->getResourceOutput($resource);
    }

}