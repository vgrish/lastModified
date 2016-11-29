<?php

class lastModifiedOnWebPagePrerender extends lastModifiedPlugin
{
    public function run()
    {
        $resource = $this->modx->resource;
        if (!$this->lm->isWorkingResource($resource)) {
            return;
        }

        $queryHash = $this->lm->getQueryHash();
        $outputHash = $this->lm->getOutputHash($resource);

        /** @var lastModifiedHash $lmHash */
        if (!$lmHash = $this->modx->getObject('lastModifiedHash', array('query_hash' => $queryHash))) {
            $lmHash = $this->modx->newObject('lastModifiedHash');
            $lmHash->fromArray(array(
                'query_hash'  => $queryHash,
                'output_hash' => $outputHash,
                'timestamp'   => time(),
                'rid'         => $resource->id,
            ), '', true);
            $lmHash->save();
        }

        if ($lmHash->get('output_hash') != $outputHash) {
            $lmHash->fromArray(array(
                'output_hash' => $outputHash,
                'timestamp'   => time(),
            ), '', true);
            $lmHash->save();
        }
        
        $lastModified = $lmHash->__get('timestamp');
        $ifModifiedSince = $this->lm->getIfModifiedSince();

        if ($ifModifiedSince AND $ifModifiedSince >= $lastModified) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
            exit;
        }
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s \G\M\T", $lastModified));

    }

}