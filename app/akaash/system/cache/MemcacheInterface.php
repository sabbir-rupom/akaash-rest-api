<?php
namespace Akaash\System\Cache;

interface MemcacheInterface
{
    public function addServer();

    public function new(string $key, $value, int $flag = 0, $expire = null): bool;

    public function increment(string $key, int $value): int;
}
