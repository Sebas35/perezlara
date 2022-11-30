<?php

namespace App\Models;

use App\Traits\Models\TConstruct;
use Core\Connection;
use PDO;

class Model
{
    use TConstruct;
    protected PDO $connection;

    public function __construct(...$args)
    {
        $this -> connection = (new Connection()) -> connect();
        call_user_func_array([$this,'construct'], $args);
    }

    public function sqlFilter(string $columns, string $table, array ...$data): bool|array
    {
        $sql = 'select ' . $columns . ' from ' . $table . ' where ';
        $array_conditions = $data[count($data) - 1];
        foreach ($array_conditions as $index => $condition) {
            $sql .= ucfirst($condition). ' in(';
            $tam = count($data[$index]) - 1;
            for($i = 0; $i < $tam; $i++){
                $sql .= ':'.$condition.$i.',';
            }
            $sql .= ':'.$condition.$tam.')';
            if ($index !== count($array_conditions) - 1) {
                $sql .= ' and ';
            }
        }
        $filter = $this -> connection -> prepare($sql);
        foreach ($array_conditions as $index => $condition) {
            for ($i = 0; $i < count($data[$index]); $i++) {
                $filter -> bindParam($condition . $i, $data[$index][$i]);
            }
        }
        $filter -> execute();
        return $filter -> fetchAll();
    }
}
