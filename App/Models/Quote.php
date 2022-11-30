<?php

namespace App\Models;

use PDO;
use PDOException;
use PDOStatement;

class Quote extends Model
{
    private int $id;
    private Client $client_document;
    private float $insured_value;
    private Insurance $insurance;
    private User $user;
    private array|int $options;

    public function __constructIdQuote(int $id) :void
    {
        $this -> id = $id;
    }

    public function __constructClientDocument(Client $document_client) :void
    {
        $this -> client_document = $document_client;
    }

    public function __constructAcceptedOption(int $accepted_option) :void
    {
        $this -> options = $accepted_option;
    }

    public function __constructCreate(
        Client $client_document,
        Insurance $insurance,
        float $insured_value,
        User $user,
        array $options,
    ) :void{
        $this -> client_document = $client_document;
        $this -> insurance = $insurance;
        $this -> user = $user;
        $this -> options = $options;
        $this -> insured_value = $insured_value;
    }

    public function __constructUpdate(
        int $id,
        Client $client_document,
        Insurance $insurance,
        float $insured_value,
        array $options,
    ) :void{
        $this -> __constructIdQuote($id);
        $this -> __constructClientDocument($client_document);
        $this -> insurance = $insurance;
        $this -> insured_value = $insured_value;
        $this -> options = $options;
    }

    public function index() :bool|array
    {
        return $this -> connection -> query(
            'select `No. Cotizacion`, Fecha, Cliente, Seguro, Aseguradora, Estado,
        `Fecha de actualización` from view_cotizacion'
        ) -> fetchAll();
    }

    public function total() :int
    {
        return $this -> connection -> query('select count(*) from view_cotizacion') -> fetchColumn();
    }

    public function recent() :bool|array
    {
        return $this -> connection -> query(
            'select `No. Cotizacion`, Cliente, Seguro, Aseguradora from view_cotizacion limit 25'
        ) -> fetchAll();
    }

    public function create() :bool|string
    {
        try {
            $user = $this -> user -> getDocument();
            $quote = $this -> connection -> prepare(
                'call sp_insert_cotizacion(:client_document, :insurance, :insured_value, :user)'
            );
            $this -> params($quote);
            $quote -> bindParam('user', $user);
            $quote -> execute();
            $this -> id = $quote -> fetchColumn();
            $quote -> closeCursor();
            $this -> insertInsurerCoverages($this -> options);
            return true;
        } catch (PDOException $e) {
            return $e -> getMessage();
        }
    }

    public function insertInsurerCoverages(array $data)
    {
        foreach ($data as $insurer) {
            $contributing_insurer = $this -> connection -> prepare(
                'call sp_insert_aseguradora_cotizante(:id_quote, :id_insurer, :premium_value)'
            );
            $contributing_insurer -> bindParam('id_quote', $this -> id);
            $contributing_insurer -> bindParam('id_insurer', $insurer['insurer']);
            $contributing_insurer -> bindParam('premium_value', $insurer['premium_value']);
            $contributing_insurer -> execute();
            $id_contributing_insurer = $contributing_insurer -> fetchColumn();
            $contributing_insurer -> closeCursor();
            foreach ($insurer['coverages'] as $coverage) {
                $quote_coverage = $this -> connection -> prepare(
                    'call sp_insert_cobertura_cotizacion(:id_contributing_insurer, :id_coverage, :price)'
                );
                $quote_coverage -> bindParam('id_contributing_insurer', $id_contributing_insurer);
                $quote_coverage -> bindParam('id_coverage', $coverage['id']);
                $quote_coverage -> bindParam('price', $coverage['price']);
                $quote_coverage -> execute();
            }
        }
    }

    public function params(bool|PDOStatement $sql) :void
    {
        $client_document = $this -> client_document -> getDocument();
        $insurance = $this -> insurance -> getId();
        $sql -> bindParam('client_document', $client_document);
        $sql -> bindParam('insurance', $insurance, PDO::PARAM_INT);
        $sql -> bindParam('insured_value', $this -> insured_value);
    }

    public function option()
    {
        $option = $this -> connection -> prepare(
            'SELECT s.seguro AS seguro , a.aseguradora AS aseguradora, valor_prima, valor_asegurado 
        FROM aseguradora_cotizante ac 
            INNER JOIN cotizacion c ON ac.cotizacion = c.id_cotizacion 
            INNER JOIN aseguradora a ON ac.aseguradora = a.id_aseguradora 
            INNER JOIN seguro s ON c.seguro = s.id_seguro 
        WHERE id_aseguradora_cotizante = :option'
        );
        $option -> bindParam('option', $this -> options);
        $option -> execute();
        return $option -> fetch();
    }

    /**
     * @return array|int
     */
    public function getOptions() :array|int
    {
        return $this -> options;
    }

    public function show() :bool|array
    {
        $quote = $this -> connection -> prepare(
            'SELECT Documento, Cliente, id_seguro, Seguro, id_aseguradora, nombre_aseguradora, id_cobertura, 
        nombre_cobertura, Precio, Deducible, `Valor asegurado`, `Valor prima` FROM view_cotizacion WHERE `No. Cotizacion` = :id_quote'
        );
        $quote -> bindParam('id_quote', $this -> id, PDO::PARAM_INT);
        $quote -> execute();
        return $quote -> fetch();
    }

    public function accordingClient(Policy|null $policy = null) :bool|array
    {
        $quotes = $this -> connection -> prepare('SELECT Cliente, Seguro, Aseguradora, id_aseguradora_cotizante
        FROM view_cotizacion WHERE Documento = :client_document AND (opcion_seleccionada IS NULL '.($policy ? 'OR poliza = :policy' : NULL).')');
        $client_document = $this -> client_document -> getDocument();
        $quotes -> bindParam('client_document', $client_document);
        if ($policy) {
            $code = $policy -> getCode();
            $quotes -> bindParam('policy',$code);
        }
        $quotes -> execute();
        return $quotes -> fetchAll();
    }

    public function filter(array ...$array) :bool|array
    {
        return $this -> sqlFilter(
            '`No. Cotizacion`, Fecha, Cliente, Seguro, Aseguradora, Estado, `Fecha de actualización`',
            'view_cotizacion',
            ...$array
        );
    }

    public function update() :bool|string
    {
        try {
            $quote = $this -> connection -> prepare(
                'call sp_update_cotizacion(:id_quote, :client_document, :insurance, :insured_value)'
            );
            $quote -> bindParam('id_quote', $this -> id, PDO::PARAM_INT);
            $this -> params($quote);
            $quote -> execute();
            $quote -> closeCursor();
            $this -> insertInsurerCoverages($this -> options);
            return true;
        } catch (PDOException $e) {
            echo $e -> getMessage();
            return $e -> getMessage();
        }
    }

    public function delete() :void
    {
        $quote = $this -> connection -> prepare('call sp_delete_cotizacion(:id_quote)');
        $quote -> bindParam('id_quote', $this -> id, PDO::PARAM_INT);
        $quote -> execute();
    }

    public function estado() :bool|array
    {
        return $this -> connection -> query("select * from estado where id_estado > 3 and id_estado < 9") -> fetchAll(
            PDO::FETCH_NUM
        );
    }
}
