<?php

namespace App\Models;

use Enum\Directories;
use PDO;
use PDOException;
use PDOStatement;
use Storage\FileService;

class Insurer extends Model
{
    private int $id;
    private string $insurer;
    private string|FileService $file_service;
    private array $insurances;

    public function __construct1(int $id): void
    {
        $this -> id = $id;
    }

    public function __construct3(string $insurer, FileService $file_service, array $insurances): void
    {
        $this -> insurer = $insurer;
        $this -> file_service = $file_service;
        $this -> insurances = $insurances;
    }

    public function __construct4(int $id, string $insurer, FileService $file_service, array $insurances): void
    {
        $this -> __construct1($id);
        $this -> __construct3($insurer,$file_service,$insurances);
    }

    public function index(): bool|array
    {
        return $this -> connection -> query(
            'select a.id_aseguradora, a.aseguradora from aseguradora a
        inner join seguro_aseguradora sa on sa.aseguradora = a.id_aseguradora where estado = 1 group by a.id_aseguradora'
        ) -> fetchAll();
    }

    public function card(): bool|array
    {
        return $this -> connection -> query(
            'SELECT group_concat(s.seguro) AS seguros ,a.logo AS logo, a.id_aseguradora AS id_aseguradora, a.aseguradora AS aseguradora 
                                                      FROM aseguradora a 
                                                      LEFT JOIN seguro_aseguradora sa ON sa.aseguradora = a.id_aseguradora 
                                                      LEFT JOIN seguro s ON sa.seguro = s.id_seguro GROUP BY a.aseguradora'
        ) -> fetchAll();
    }

    public function create(): array|bool
    {
        try {
            $insurer = $this -> connection -> prepare('call sp_insert_aseguradora(:insurer,:logo)');
            $this -> params($insurer);
            $insurer -> execute();
            $this -> id = $insurer -> fetchColumn();
            $insurer -> closeCursor();
            foreach ($this -> insurances as $insurance) {
                $insurance_insurer = $this -> connection -> prepare('call sp_insert_seguro_aseguradora(:insurance,:insurer)');
                $id_insurance = $insurance -> getId();
                $insurance_insurer -> bindParam('insurance',$id_insurance);
                $insurance_insurer -> bindParam('insurer',$this -> id);
                $insurance_insurer -> execute();
            }
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function upload(): array|string
    {
        return $this -> file_service -> upload(Directories::INSURERS);
    }

    public function params(bool|PDOStatement $sql): void
    {
        $sql -> bindParam('insurer', $this -> insurer);
        $sql -> bindParam('logo', $this -> file_service);
    }

    public function show(): bool|array
    {
        $insurance = $this -> connection -> prepare('select * from aseguradora where id_aseguradora = :id_insurer');
        $insurance -> bindParam('id_insurer', $this -> id, PDO::PARAM_INT);
        $insurance -> execute();
        return $insurance -> fetch();
    }

    public function update(): void
    {
        $insurer = $this -> connection -> prepare('call sp_update_aseguradora(:id_insurer,:insurer,:logo)');
        $insurer -> bindParam('id_insurer', $this -> id, PDO::PARAM_INT);
        $this -> params($insurer);
        $insurer -> execute();
    }

    public function delete(): void
    {
        $insurer = $this -> connection -> prepare('call sp_delete_aseguradora(:id_insurer)');
        $insurer -> bindParam('id_insurer', $this -> id, PDO::PARAM_INT);
        $insurer -> execute();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this -> id;
    }

    public function setFileService(string $file_service) :void
    {
        $this -> file_service = $file_service;
    }
}
