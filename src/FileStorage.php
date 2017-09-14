<?php
namespace FiremonPHP\Storage;

class FileStorage implements \Iterator
{
    private $files = [];

    public function __construct(array $files)
    {
        $this->storeInternal($files);
    }



    /**
     * @param $files
     */
    private function storeInternal($files)
    {
        if ($this->isMultiplePostFiles($files)) {
            $this->byMultiplePostFiles($files);
            return;
        }

        $this->byMultipleDataString($files);
    }

    /**
     * @param array $dataFiles
     */
    private function byMultipleDataString(array $dataFiles)
    {
        foreach ($dataFiles as $fileKey => $file) {
            $indexFile = $this->generateName();
            $stream = $this->initStream($indexFile, true);
            fwrite($stream, $file['data']);
            $this->setFilesAttributes(
                $indexFile,
                $stream,
                $fileKey,
                $file['metadata'] ?? null
            );
        }
    }

    /**
     * @param array $files
     */
    private function byMultiplePostFiles(array $files)
    {
        $countFiles = count($files['name']);
        for ($i = 0; $i < $countFiles; $i++) {
            $indexFile = $this->generateName();
            $this->setFilesAttributes(
                $indexFile,
                $this->initStream($files['tmp_name'][$i]),
                $files['name'][$i],
                ['type' => $files['type'][$i] ?? 'application/octet-stream']
            );
        }
    }

    /**
     * @param array $file
     * @return bool
     */
    private function isMultiplePostFiles(array $file)
    {
        return isset($file['name']) && count($file['name']) > 0;
    }

    /**
     * @return string
     */
    private function generateName()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * @param string $fileName
     * @param bool $create
     * @return bool|resource
     */
    private function initStream(string $fileName, bool $create = false)
    {
        return $create ? tmpfile() : fopen($fileName, 'r');
    }

    /**
     * @param $index
     * @param $data
     * @param $name
     * @param null $metadata
     */
    private function setFilesAttributes($index, $data, $name, $metadata = null)
    {
        $this->files[$index]['data'] = $data;
        $metadata = array_merge($metadata, ['name' => $name]);
        $this->files[$index]['metadata'] = $metadata;
    }

    public function current()
    {
        return current($this->files);
    }

    public function next()
    {
        return next($this->files);
    }

    public function key()
    {
        return key($this->files);
    }

    public function valid()
    {
        return key($this->files) !== null;
    }

    public function rewind()
    {
        reset($this->files);
    }


}