<?php

namespace Akaash\System\Cache;

interface Service
{
    const DEFAULT_EXPIRATION = 3600; // in seconds

    public function add($value, int $flag = 0, int $expire = 0): string;

    public function get(string $key, int $flag = 0);

    public function put(string $key, $value, int $flag = 0, $expire = null): bool;

    public function set(string $key, $value, int $flag = 0, $expire = null): bool;

    public function remove(string $key): bool;

    public function delete(string $key): bool;

    public function flush(): bool;
}
