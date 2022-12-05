<?php

use Enum\Routes;

function url_base()
{
    return $_ENV['APP_URL'];
}

function css($url) :string
{
    return url_base() . Routes::CSS . $url;
}

function helper_js($url) :string
{
    return url_base() . Routes::HELPER . $url;
}

function js($url) :string
{
    return url_base() . Routes::JS . $url;
}

function icon($url = null) :string
{
    return url_base() . Routes::ICON . $url;
}

function view($url) :string
{
    return Routes::VIEW . $url;
}

function error($url) :string
{
    return Routes::ERROR . $url;
}

function helper($url) :string
{
    return Routes::HELPER . $url;
}
