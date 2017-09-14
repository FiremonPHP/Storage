<?php
namespace FiremonPHP\Storage;


use FiremonPHP\Manager\Configuration;
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
     * @param array $files
     * @return array
     */
    public function upload(array $files)
    {
        $files = new FileStorage($files);
        $results = [];
        foreach ($files as $fileName => $file) {
            $results[] = $this->bucket->uploadFromStream($fileName, $file['data'], $file['metadata']);
        }
        return $results;
    }

    public function download()
    {

    }

    public function delete()
    {

    }
}