<?php

namespace App\Models;

use App\Traits\Models\ClientUser;
use PDO;
use PDOException;
use PDOStatement;

class User extends Model
{
    use ClientUser;

    private string|null $password;
    private string|null $profile_picture;
    private int $role;
    private int $state;

    public function __construct2(string $user, string $password): void
    {
        if (str_contains($user, '@')) {
            $this -> email = $user;
        } else {
            $this -> document = $user;
        }
        $this -> password = $password;
    }

    public function __construct12(
        string $document,
        string $first_name,
        string|null $second_name,
        string $first_surname,
        string|null $second_surname,
        string $email,
        string $phone,
        string|null $password,
        string|null $profile_picture,
        int $document_type,
        int $role,
        int $state,
    ): void {
        $this -> __construct11(
            $document,
            $first_name,
            $second_name,
            $first_surname,
            $second_surname,
            $email,
            $phone,
            $password,
            $profile_picture,
            $document_type,
            $role
        );
        $this -> state = $state;
    }

    public function __construct11(
        string $document,
        string $first_name,
        string|null $second_name,
        string $first_surname,
        string|null $second_surname,
        string $email,
        string $phone,
        string|null $password,
        string|null $profile_picture,
        int $document_type,
        int $role,
    ): void {
        $this -> document = $document;
        $this -> first_name = $first_name;
        $this -> second_name = $second_name;
        $this -> first_surname = $first_surname;
        $this -> second_surname = $second_surname;
        $this -> email = $email;
        $this -> phone = $phone;
        $this -> password = $password;
        $this -> profile_picture = $profile_picture;
        $this -> document_type = $document_type;
        $this -> role = $role;
    }

    public function index(): bool|array
    {
        return $this -> connection -> query('select CONCAT(abreviacion,\' \',documento) as `No. Documento`, Foto, CONCAT(Nombres,\' \', Apellidos) as Nombre, Email, Celular, Rol from 
        view_usuario') ->
        fetchAll();
    }

    public function create(): bool|string
    {
        try {
            $user = $this -> connection -> prepare(
                'call sp_insert_usuario(:document, :first_name, :second_name,
                                                                                :first_surname, :second_surname, 
                                                                                :email, :phone, :password,
                                                                                :profile_picture, :document_type,
                                                                                :role, :state)'
            );
            $this -> params($user);
            $user -> bindParam('state', $this -> state, PDO::PARAM_INT);
            $user -> execute();
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function params(bool|PDOStatement $sql): void
    {
        $this -> beginParams($sql);
        $sql -> bindParam('email', $this -> email);
        $sql -> bindParam('phone', $this -> phone);
        $sql -> bindParam('password', $this -> password);
        $sql -> bindParam('profile_picture', $this -> profile_picture);
        $sql -> bindParam('document_type', $this -> document_type, PDO::PARAM_INT);
        $sql -> bindParam('role', $this -> role, PDO::PARAM_INT);
    }

    public function show(): bool|array
    {
        $user = $this -> connection -> prepare('select id_tipo_documento, descripcion_documento, documento, 
        Foto, Nombres, Apellidos, Email, Celular, id_rol, Rol from view_usuario where
        documento = :document');
        $user -> bindParam('document', $this -> document);
        $user -> execute();
        return $user -> fetch();
    }

    public function login(): bool|array
    {
        $user = $this -> connection -> prepare('call sp_login(:email, :document, :password)');
        $email =  $this -> email ?? '';
        $document = $this -> document ?? '';
        $user -> bindParam('email', $email);
        $user -> bindParam('document', $document);
        $user -> bindParam('password', $this -> password);
        $user -> execute();
        return $user -> fetch();
    }

    public function update($email): void
    {
        $user = $this -> connection -> prepare(
            'call sp_update_usuario(:document2, :document, :first_name,
                                                                               :second_name, :first_surname,
                                                                               :second_surname, :email, :phone,
                                                                               :password, :profile_picture, :document_type, :role)'
        );
        $user -> bindParam('document2', $email);
        $this -> params($user);
        $user -> execute();
    }

    public function delete(): void
    {
        $user = $this -> connection -> prepare('call sp_delete_usuario(:document)');
        $user -> bindParam('document', $this -> document);
        $user -> execute();
    }

    public function inactivate(): void
    {
        $user = $this -> connection -> prepare('call sp_inactivate_usuario(:document)');
        $user -> bindParam('document', $this -> document);
        $user -> execute();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this -> first_name;
    }

    /**
     * @return string
     */
    public function getFirstSurname(): string
    {
        return $this -> first_surname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this -> email;
    }

    public function roles(): bool|array
    {
        return $this -> connection -> query('SELECT id_rol, rol FROM rol') -> fetchAll(PDO::FETCH_NUM);
    }
}
