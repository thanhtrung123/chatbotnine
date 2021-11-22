<?php

namespace App\Services\Admin\Traits;

use File;

/**
 * Trait for scenario service
 * Trait ScenarioTrait
 * @package App\Services\Admin\Traits
 */
trait ScenarioTrait
{
    /**
     * Get array node
     * @param array $arr
     * @return $arr
     */
    public function flatten($arr)
    {
        $lst = [];
        foreach ($arr as $item) {
            // Get the "prefix" of the URL
            $prefix = 's' . $item['id'];
            // Check if it has children
            if (array_key_exists('children', $item)) {
                //Get the suffixes recursively
                $suffixes = $this->flatten($item['children']);
                //Add it to the current prefix
                foreach ($suffixes as $suffix) {
                    $url = $prefix . '/' . $suffix;
                    array_push($lst, $url);
                }
            } else {
                //If there are no children, just add the 
                //current prefix to the list
                array_push($lst, $prefix);
            }
        }
        return $lst;
    }

    /**
     * Build tree scenario
     * @param array $data
     * @return array $data
     */
    public function buildTree(array $data)
    {
        $data = array_column($data, null, 'id');
        //reference to each node in loop
        foreach ($data as &$node) {
            if (!$node['parent_id']) {
                //record has no parents - null or empty array
                continue;
            }
            foreach ($node['parent_id'] as $id) {
                if (!isset($data[$id]['children'])) {
                    $data[$id]['children'] = array();
                }
                //assign a reference to the child node
                $data[$id]['children'][] = &$node; 
            }
        }
        return $data;
    }
}