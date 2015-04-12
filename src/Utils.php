<?php

namespace PromotedListings;

final class Utils
{
    public static function mergeDefaults(array $defaults = [], array $data = [])
    {
        return array_replace_recursive(
            $defaults,
            array_intersect_key($data, $defaults)
        );
    }
}
