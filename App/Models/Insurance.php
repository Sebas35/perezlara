<?php

namespace App\Models;

use PDO;
use PDOException;
use PDOStatement;

class Insurance extends Model
{
    private int $id;
    private string $insurance;
    private string $image;
    private array $insurers;

    public function __construct1(int $id): void
    {
        $this -> id = $id;
    }

    public function __construct3(string $insurance, string $image, array $insurers): void
    {
        $this -> insurance = $insurance;
        $this -> image = $image;
        $this -> insurers = $insurers;
    }

    public function __construct4(int $id, string $insurance, string $image, array $insurers): void
    {
        $this -> __construct1($id);
        $this -> __construct3($insurance, $image, $insurers);
    }

    public function index(): bool|array
    {
        return $this -> connection -> query(
            'select s.id_seguro, s.seguro from seguro_aseguradora 
    inner join seguro s on seguro_aseguradora.seguro = s.id_seguro where estado = 1 group by s.id_seguro'
        ) -> fetchAll();
    }

    public function create(): bool|string
    {
        try {
            $insurance = $this -> connection -> prepare('call sp_insert_seguro(:insurance,:image)');
            $this -> params($insurance);
            $insurance -> execute();
            $this -> id = $insurance -> fetchColumn();
            $insurance -> closeCursor();
            foreach ($this -> insurers as $insurer) {
                $insurance_insurer = $this -> connection -> prepare('call sp_insert_seguro_aseguradora(:insurance,:insurer)');
                $id_insurer = $insurer -> getId();
                $insurance_insurer -> bindParam('insurance', $this -> id);
                $insurance_insurer -> bindParam('insurer',$id_insurer);
                $insurance_insurer -> execute();
            }
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function params(bool|PDOStatement $sql): void
    {
        $sql -> bindParam('insurance', $this -> insurance);
        $sql -> bindParam('image', $this -> image);
    }

    public function show(): bool|array
    {
        $insurance = $this -> connection -> prepare(
            'select seguro, imagen from seguro where id_seguro = :id_insurance'
        );
        $insurance -> bindParam('id_insurance', $this -> id, PDO::PARAM_INT);
        $insurance -> execute();
        return $insurance -> fetch();
    }

    public function card(): bool|array
    {
        return $this -> connection -> query('select * from seguro') -> fetchAll();
    }

    public function update(): void
    {
        $insurance = $this -> connection -> prepare('call sp_update_seguro(:id_insurance,:insurance,:image)');
        $insurance -> bindParam('id_insurance', $this -> id, PDO::PARAM_INT);
        $this -> params($insurance);
        $insurance -> execute();
    }

    public function delete(): void
    {
        $insurance = $this -> connection -> prepare('call sp_delete_seguro(:id_insurance)');
        $insurance -> bindParam('id_insurance', $this -> id, PDO::PARAM_INT);
        $insurance -> execute();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this -> id;
    }
}
