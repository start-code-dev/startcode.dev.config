<?php

namespace Startcode\Config;

class Config
{
    private $cachingEnabled = false;

    private $cachePath;

    private $data;

    private $path;

    private $section;

    private $useAggregator = false;


    public function getData($force = false)
    {
        if($force !== true && $this->cachingEnabled) {
            $this->data = $this->getFromCache();
        }

        if(null === $this->data) {
            $this->process();

            if($this->cachingEnabled) {
                $this->setToCache();
            }
        }

        return $this->data;
    }

    public function setPath(string $path) : self
    {
        $this->path = $path;
        return $this;
    }

    public function setSection(string $section) : self
    {
        $this->section = $section;
        return $this;
    }

    public function setCachingEnabled($flag) : self
    {
        $this->cachingEnabled = (bool) $flag;
        return $this;
    }

    public function setCachePath($cachePath) : self
    {
        $this->cachePath = (string) $cachePath;
        return $this;
    }

    public function useAggregator() : self
    {
        $this->useAggregator = true;
        return $this;
    }

    private function formatCacheFilename() : string
    {
        $segments = array(
            $this->path,
            __NAMESPACE__,
            __CLASS__,
            $this->section,
        );

        // section can be empty, so remove it
        $filename = md5(implode('~', array_filter($segments)));

        if(!is_writable($this->cachePath)) {
            throw new \Exception('Cache file path is not writable');
        }

        return $this->cachePath . $filename;
    }

    private function getFromCache()
    {
        $realCachePath = realpath($this->formatCacheFilename());

        if(false === $realCachePath) {
            return null;
        }

        if(!is_readable($realCachePath)) {
            throw new \Exception('Cache file path is not readable');
        }

        return require($realCachePath);
    }

    private function setToCache() : self
    {
        $realCachePath = $this->formatCacheFilename();

        $toSave = "<?php return \n" . var_export($this->data, true) . ';';

        if(!touch($realCachePath)) {
            throw new \Exception('Cache file path is not writable');
        }

        file_put_contents($realCachePath, $toSave);

        return $this;
    }

    private function process() : self
    {
        $this->data = (new Processor($this->path, $this->section, $this->useAggregator))->process();

        return $this;
    }
}
