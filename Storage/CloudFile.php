<?php

namespace Storage;

use Exception;
use Google\Cloud\Storage\StorageClient;

class CloudFile extends GoogleService
{
    public function upload($folder): Exception|string
    {
        try {
            $storage = new StorageClient();
            $file = fopen($this -> tmp_name, 'r');
            $bucket = $storage -> bucket($_ENV['CLOUD_STORAGE_BUCKET']);
            $bucket -> upload($file, [
                'name' => $folder . '/' . $this -> name,
                'resumable' => true,
            ]);
            return $this -> name;
        } catch (Exception $e) {
            return $e;
        }
    }
}