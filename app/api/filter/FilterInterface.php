<?php
namespace API\Filter;

interface FilterInterface
{
    public function check();

    public function validate();
}
