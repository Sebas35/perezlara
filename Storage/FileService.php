<?php

namespace Storage;

use App\Traits\Models\TConstruct;
use Exception;
use Google\Client;
use Google\Service\Drive;

class FileService
{
    use TConstruct;

    private string $id;
    private string $name;
    private string $type;
    private string $tmp_name;
    private Drive $drive_service;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=upload-367301-fd3e19b47aa0.json');
        $client = new Client();
        $client -> useApplicationDefaultCredentials();
        $client -> addScope(Drive::DRIVE_FILE);
        $this -> drive_service = new Drive($client);
        call_user_func_array([$this, 'construct'], func_get_args());
    }

    public function __construct1(string $id) :void
    {
        $this -> id = $id;
    }

    public function __construct2(string $id, string $name) :void
    {
        $this -> id = $id;
        $this -> name = $name;
    }

    public function __construct3(string $name, string $type, string $tmp_name) :void
    {
        $this -> name = $name;
        $this -> type = $type;
        $this -> tmp_name = $tmp_name;
    }

    public function __construct4(string $id, string $name, string $type, string $tmp_name) :void
    {
        $this -> id = $id;
        $this -> __construct3($name, $type, $tmp_name);
    }

    public function upload(string $folder) :array|string
    {
        try {
            $file_metadata = new Drive\DriveFile([
                'name' => $this -> name,
                'parents' => [$folder],
                'mimeType' => $this -> type
            ]);
            $content = file_get_contents($this -> tmp_name);
            $file = $this -> drive_service -> files -> create($file_metadata, [
                'data' => $content,
                'mimeType' => $this -> type,
                'uploadType' => 'resumable',
                'fields' => 'id,name',
            ]);
            return ['id' => $file -> id, 'name' => $file -> name];
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    public function update() :array|string
    {
        try {
            $file_metadata = new Drive\DriveFile([
                'name' => $this -> name,
                'mimeType' => $this -> type
            ]);
            $content = file_get_contents($this -> tmp_name);
            $updated_file = $this -> drive_service -> files -> update($this -> id, $file_metadata, [
                'data' => $content,
                'mimeType' => $this -> type,
                'fields' => 'id,name',
            ]);
            return ['id' => $updated_file -> id, 'name' => $updated_file -> name];
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    public function delete() :bool|string
    {
        try {
            $this -> drive_service -> files -> delete($this -> id);
            return true;
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    /**
     * @return string
     */
    public function getId() :string
    {
        return $this -> id;
    }
}
