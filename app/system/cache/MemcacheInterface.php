<?php

interface MemcacheInterface
{
    public function addServer();

    public function new(string $key, $value, int $flag = 0, int $expire = 0): bool;

    public function increment(string $key, int $value): int;
}
