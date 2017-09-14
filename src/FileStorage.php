<?php
namespace FiremonPHP\Storage;

class FileStorage implements \Iterator
{
    private $files = [];

    const DEFAULT_FILE_TYPE = 'application/octet';

    public function __construct(array $files)
    {
        $this->storeInternal($files);
    }

    /**
     * Store on internal attribute $files, files by type of data files
     * @param $files
     */
    private function storeInternal($files) : void
    {
        if ($this->isSinglePostFile($files)) {
            $this->bySinglePostFile($files);
            return;
        }

        if ($this->isMultiplePostFiles($files)) {
            $this->byMultiplePostFiles($files);
            return;
        }

        $this->byMultipleDataString($files);
    }

    /**
     * Set all files on correct indexes
     * @param array $dataFiles
     */
    private function byMultipleDataString(array $dataFiles) : void
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
    private function byMultiplePostFiles(array $files) : void
    {
        $countFiles = count($files['name']);
        for ($i = 0; $i < $countFiles; $i++) {
            $indexFile = $this->generateName();
            $this->setFilesAttributes(
                $indexFile,
                $this->initStream($files['tmp_name'][$i]),
                $files['name'][$i],
                ['type' => $files['type'][$i] ?? self::DEFAULT_FILE_TYPE]
            );
        }
    }

    /**
     * @param array $dataFile
     */
    private function bySinglePostFile(array $dataFile) : void
    {
        $indexFile = $this->generateName();
        $this->setFilesAttributes(
            $indexFile,
            $this->initStream($dataFile['tmp_name']),
            $dataFile['name'],
            ['type' => $dataFile['type'] ?? self::DEFAULT_FILE_TYPE]
        );
    }

    /**
     * @param array $file
     * @return bool
     */
    private function isMultiplePostFiles(array $file) : bool
    {
        return isset($file['name']) && is_array($file['name']);
    }

    /**
     * @param array $file
     * @return bool
     */
    private function isSinglePostFile(array $file) : bool
    {
        return isset($file['name']) && ! is_array($file['name']);
    }

    /**
     * @return string
     */
    private function generateName() : string
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
    private function setFilesAttributes($index, $data, $name, $metadata = null) : void
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