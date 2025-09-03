<?php
require_once(__DIR__ . '/model.select.php');
class cadastrador extends select
{
    function __construct($escola)
    {
        parent::__construct($escola);
    }
}
