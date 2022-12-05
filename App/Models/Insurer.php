<?php

namespace App\Models;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use Storage\CloudFile;

class Insurer extends Model
{
    private int $id;
    private string $insurer;
    private CloudFile|null|string $logo;
    private array $insurances;

    public function __construct1(int $id): void
    {
        $this -> id = $id;
    }

    public function __construct3(string $insurer, CloudFile $logo, array $insurances): void
    {
        $this -> insurer = $insurer;
        $this -> logo = $logo;
        $this -> insurances = $insurances;
    }

    public function __construct4(int $id, string $insurer, ?CloudFile $logo, array $insurances): void
    {
        $this -> id = $id;
        $this -> insurer = $insurer;
        $this -> logo = $logo;
        $this -> insurances = $insurances;
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
            'SELECT group_concat(s.seguro) AS seguros, GROUP_CONCAT(estado) AS estados, a.logo AS logo, a.id_aseguradora AS id_aseguradora, a.aseguradora AS aseguradora 
                                                      FROM aseguradora a 
                                                      LEFT JOIN seguro_aseguradora sa ON sa.aseguradora = a.id_aseguradora 
                                                      LEFT JOIN seguro s ON sa.seguro = s.id_seguro  GROUP BY a.aseguradora'
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
            $this -> insert_insurances($this -> insurances);
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function insert_insurances(array $insurances) {
        try {
            foreach ($insurances as $insurance) {
                $insurance_insurer = $this -> connection -> prepare('call sp_insert_seguro_aseguradora(:insurance,:insurer)');
                $id_insurance = $insurance -> getId();
                $insurance_insurer -> bindParam('insurance',$id_insurance);
                $insurance_insurer -> bindParam('insurer',$this -> id);
                $insurance_insurer -> execute();
            }
        } catch (PDOException $e) {
            echo $e -> getMessage();
        }
    }

    public function params(bool|PDOStatement $sql): void
    {
        $sql -> bindParam('insurer', $this -> insurer);
        $sql -> bindParam('logo', $this -> logo);
    }

    public function show(): bool|array
    {
        $insurance = $this -> connection -> prepare('SELECT a.aseguradora AS aseguradora, a.logo as logo, GROUP_CONCAT(s.id_seguro) AS id_seguros, GROUP_CONCAT(sa.id_seguro_aseguradora)AS id_seguros_aseguradora
                                                      FROM aseguradora a
                                                      LEFT JOIN seguro_aseguradora sa ON sa.aseguradora = a.id_aseguradora
                                                      LEFT JOIN seguro s ON sa.seguro = s.id_seguro WHERE id_aseguradora = :id_insurer AND estado = 1');
        $insurance -> bindParam('id_insurer', $this -> id, PDO::PARAM_INT);
        $insurance -> execute();
        return $insurance -> fetch();
    }

    public function update(): bool|string
    {
        try {
            $insurer = $this -> connection -> prepare('call sp_update_aseguradora(:id_insurer,:insurer,:logo)');
            $insurer -> bindParam('id_insurer', $this -> id, PDO::PARAM_INT);
            $this -> params($insurer);
            $insurer -> execute();
            $insurer -> closeCursor();
            foreach ($this -> insurances['collection_create'] as $insurance) {
                $insurance_insurer = $this -> connection -> prepare('call sp_insert_seguro_aseguradora(:insurance,:insurer)');
                $id_insurance = $insurance -> getId();
                $insurance_insurer -> bindParam('insurance',$id_insurance);
                $insurance_insurer -> bindParam('insurer',$this -> id);
                $insurance_insurer -> execute();
            }
            $sql = 'UPDATE seguro_aseguradora SET estado = 2 WHERE aseguradora = :insurer AND seguro NOT IN(';
            $insurances = array_merge($this -> insurances['collection_create'],$this ->
            insurances['collection_keep']);
            $tam = count($insurances) - 1;
            for($i = 0; $i < $tam; $i++) {
                $sql .= ':id_insurance' . $i . ',';
            }
            $sql .= ':id_insurance' . $tam . ')';
            $insurances_insurer_inactivate = $this -> connection -> prepare($sql);
            $insurances_insurer_inactivate -> bindParam('insurer', $this -> id, PDO::PARAM_INT);
            foreach ($insurances as $index => $insurance) {
                $param = 'id_insurance' . $index;
                $$param = $insurance -> getId();
                $insurances_insurer_inactivate -> bindParam($param, $$param, PDO::PARAM_INT);
            }
            $insurances_insurer_inactivate -> execute();
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
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

    public function upload(): Exception|string
    {
        return $this -> logo -> upload('Aseguradoras');
    }

    /**
     * @param string $logo
     */
    public function setLogo(string $logo) :void
    {
        $this -> logo = $logo;
    }
}
