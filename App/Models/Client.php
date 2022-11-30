<?php

namespace App\Models;

use App\Traits\Models\ClientUser;
use PDO;
use PDOException;
use PDOStatement;

class Client extends Model
{
    use ClientUser;

    private string $birthday;
    private string $address;
    private string|null $phone2;
    private string|null $address2;
    private int $city;

    public function __construct13(
        string $document,
        string $first_name,
        string|null $second_name,
        string $first_surname,
        string|null $second_surname,
        string $birthday,
        string $email,
        string $phone,
        string|null $phone2,
        string $address,
        string|null $address2,
        int $document_type,
        int $city
    ): void {
        $this -> __construct1($document);
        $this -> first_name = $first_name;
        $this -> second_name = $second_name;
        $this -> first_surname = $first_surname;
        $this -> second_surname = $second_surname;
        $this -> birthday = $birthday;
        $this -> email = $email;
        $this -> phone = $phone;
        $this -> phone2 = $phone2;
        $this -> address = $address;
        $this -> address2 = $address2;
        $this -> document_type = $document_type;
        $this -> city = $city;
    }

    public function index(): bool|array
    {
        return $this -> connection -> query(
            'select CONCAT(abreviacion,\' \', documento) AS `No. Documento`, CONCAT(Nombres, \' \', Apellidos) as Cliente, Departamento,Ciudad,CONCAT(direccion1,COALESCE(direccion2,\'\')) as Dirección,
       `Fecha de nacimiento`,Email,CONCAT(telefono1,COALESCE(telefono2,\'\')) as Telefono,`Fecha ingreso`,Estado from view_cliente'
        ) -> fetchAll();
    }

    public function total(): int
    {
        return $this -> connection -> query('select count(*) from view_cliente') -> fetchColumn();
    }

    public function create(): bool|string
    {
        try {
            $client = $this -> connection -> prepare(
                'call sp_insert_cliente(:document, :first_name,:second_name, 
        :first_surname, :second_surname, :birthday, :email, :phone, :phone2, :address, :address2, :document_type, :city)'
            );
            $this -> params($client);
            $client -> execute();
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function params(bool|PDOStatement $sql): void
    {
        $this -> beginParams($sql);
        $sql -> bindParam('birthday', $this -> birthday);
        $sql -> bindParam('email', $this -> email);
        $sql -> bindParam('phone', $this -> phone);
        $sql -> bindParam('phone2', $this -> phone2);
        $sql -> bindParam('address', $this -> address);
        $sql -> bindParam('address2', $this -> address2);
        $sql -> bindParam('document_type', $this -> document_type, PDO::PARAM_INT);
        $sql -> bindParam('city', $this -> city, PDO::PARAM_INT);
    }

    public function show(bool $all = true): bool|array
    {
        $columns = $all ? '*' : 'Nombres,Apellidos';
        $client = $this -> connection -> prepare("select $columns from view_cliente where documento = :document");
        $client -> bindParam('document', $this -> document);
        $client -> execute();
        return $client -> fetch();
    }

    public function filter(array ...$array): bool|array
    {
        return $this -> sqlFilter(
            'CONCAT(abreviacion,\' \', documento) AS `No. Documento`, CONCAT(Nombres, \' \', Apellidos) as Cliente, Departamento,Ciudad,CONCAT(direccion1,COALESCE(direccion2,\'\')) as Dirección,
       `Fecha de nacimiento`,Email,CONCAT(telefono1,COALESCE(telefono2,\'\')) as Telefono,`Fecha ingreso`,Estado',
            'view_cliente',
            ...$array
        );
    }

    public function search(string|int $data_search): bool|array
    {
        $search = $this -> connection -> prepare(
            'select CONCAT(abreviacion,\' \', documento) AS `No. Documento`, CONCAT(Nombres, \' \', Apellidos) as Cliente, Departamento, Ciudad,CONCAT(direccion1,COALESCE(direccion2,\'\')) as Direccion,
       `Fecha de nacimiento`,Email, CONCAT(telefono1,COALESCE(telefono2,\'\')) as Telefono,`Fecha ingreso`,Estado from view_cliente where CONCAT(abreviacion,\' \', documento) like :search or
        CONCAT(Nombres, \' \', Apellidos) like :search or Departamento like :search or Ciudad like :search or direccion1 like :search or direccion2 like :search or
        `Fecha de Nacimiento` like :search or Email like :search or
        telefono1 like :search or telefono2 like :search or Estado like :search'
        );
        $data = '%' . $data_search . '%';
        $search -> bindParam('search', $data);
        $search -> execute();
        return $search -> fetchAll();
    }

    public function update(string $document): void
    {
        $client = $this -> connection -> prepare(
            'call sp_update_cliente(:document_update, :document, :first_name,
        :second_name, :first_surname, :second_surname, :birthday, :email, :phone, :phone2, :address, :address2,
        :document_type, :city)'
        );
        $client -> bindParam('document_update', $document);
        $this -> params($client);
        $client -> execute();
    }

    public function delete(): void
    {
        $client = $this -> connection -> prepare("call sp_delete_cliente(:document)");
        $client -> bindParam('document', $this -> document);
        $client -> execute();
    }

    public function city(): bool|array
    {
        return $this -> connection -> query('select id_ciudad, ciudad from ciudad order by ciudad') -> fetchAll(
            PDO::FETCH_NUM
        );
    }

    public function department(): bool|array
    {
        return $this -> connection -> query('select * from departamento order by departamento') -> fetchAll(
            PDO::FETCH_NUM
        );
    }

    public function state(): bool|array
    {
        return $this -> connection -> query('select * from estado where id_estado < 4') -> fetchAll(PDO::FETCH_NUM);
    }

    public function filterCity(...$array): bool|array
    {
        return $this -> sqlFilter('id_ciudad,ciudad', 'ciudad', ...$array);
    }

    public function filterDepartment(...$array): bool|array
    {
        return $this -> sqlFilter('*', 'departamento', ...$array);
    }
}
