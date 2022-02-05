<?php

if (!function_exists('tree')) {
    function tree($data, $node = array(0)): array
    {
        foreach($data as $e) {
            if($e['parent_id'] == ($node['id'] ?? 0)) {
                $node['children'][]= tree($data, $e);
            }
        }

        if (!count($data)) {
            return [];
        }

        return $node;
    }
}