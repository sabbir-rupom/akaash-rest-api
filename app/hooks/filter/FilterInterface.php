<?php
namespace Hooks\Filter;

interface FilterInterface
{
    public function check();

    public function validate();
}
