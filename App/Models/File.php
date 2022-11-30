<?php

namespace App\Models;

use PDOException;
use ReflectionClass;
use ReflectionException;

class File extends Model
{
    private string $id;
    private string $filename;
    private Policy|Sinister|Annex $owner;

    public function __construct1(string $id)
    {
        $this -> id = $id;
    }

    public function __construct2(string $id, string $filename): void
    {
        $this -> id = $id;
        $this -> filename = $filename;
    }

    public function __construct3(string $id, string $filename, Policy|Sinister|Annex $owner): void
    {
        $this -> id = $id;
        $this -> filename = $filename;
        $this -> owner = $owner;
    }

    public function create(): bool|string
    {
        try {
            $classes = strtolower(((new ReflectionClass($this)) -> getProperty('owner')) -> getType());
            $owner = 'id_'.strtolower((new ReflectionClass($this -> owner)) -> getShortName());
            $file = $this -> connection -> prepare('call sp_insert_archivo(:id,:filename,:id_annex,:id_sinister,:id_policy)');
            $file -> bindParam('id', $this -> id);
            $file -> bindParam('filename', $this -> filename);
            foreach (explode('|', $classes) as $value) {
                $class = 'id_'.substr($value, strripos($value, '\\') + 1, strlen($value));
                if ($class === $owner) {
                    $$class = $owner === 'id_policy' ? $this -> owner -> getCode() : $this -> owner -> getId();
                } else {
                    $$class = null;
                }
                $file -> bindParam($class, $$class);
            }
            $file -> execute();
            return true;
        } catch (ReflectionException|PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function update(): bool|string
    {
        try {
            $file = $this -> connection -> prepare('call sp_update_archivo(:id, :filename)');
            $file -> bindParam('id', $this -> id);
            $file -> bindParam('filename', $this -> filename);
            $file -> execute();
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function delete(): bool|string
    {
        try {
            $file = $this -> connection -> prepare('call sp_delete_archivo(:id)');
            $file -> bindParam('id', $this -> id);
            $file -> execute();
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }
}
