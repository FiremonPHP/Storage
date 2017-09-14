<?php
namespace FiremonPHP\Storage;


use FiremonPHP\Manager\Configuration;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Manager;

class Storage
{
    /**
     * @var Manager
     */
    private $bucket;

    public function __construct(string $connectionName = 'default')
    {
        $this->bucket = new \MongoDB\GridFS\Bucket(
            Configuration::get($connectionName)->getMongoManager(),
            Configuration::getDatabaseName($connectionName)
        );
    }

    /**
     * This function accept multiple post files
     * @param array $files
     * @return array
     */
    public function upload(array $files)
    {
        $files = new FileStorage($files);
        $results = [];
        foreach ($files as $fileName => $file) {
            $results[] = $this->bucket->uploadFromStream($fileName, $file['data'], ['metadata' => $file['metadata']]);
        }
        return $results;
    }

    /**
     * @param array $filesId
     * @return array
     */
    public function download(array $filesId)
    {
        $files = [];
        foreach ($filesId as $fileId) {
            $stream = $this->bucket->openDownloadStream(
              new ObjectID($fileId)
            );
            $files[] = [
                'data' => $stream,
                'metadata' => $this->bucket->getFileDocumentForStream($stream)
            ];
        }
        return $files;
    }

    /**
     * @param array $filesId
     * @return array
     */
    public function delete(array $filesId)
    {
        $result = [];
        foreach ($filesId as $fileId) {
            $result[] = $this->bucket->delete(
              new ObjectID($fileId)
            );
        }
        return $result;
    }
}