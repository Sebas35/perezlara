<?php

namespace Storage;

use App\Traits\Models\TConstruct;

class GoogleService
{
    use TConstruct;

    protected string $name;
    protected string $type;
    protected string $tmp_name;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=upload-367301-fd3e19b47aa0.json');
        call_user_func_array([$this, 'construct'], func_get_args());
    }

    public function __construct3(string $name, string $type, string $tmp_name) :void
    {
        $this -> name = $name;
        $this -> type = $type;
        $this -> tmp_name = $tmp_name;
    }
}