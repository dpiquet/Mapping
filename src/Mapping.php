<?php

namespace Dpiquet\Mapping;

use Dpiquet\Mapping\Exception\MappingOverlapException;
use Dpiquet\Mapping\Exception\MappingIncompleteException;

/**
 * Informations de mapping pour les fichiers d'import
 *
 */
class Mapping {

    /**
     * @var array
     */
    private $mappings;


    public function __construct() {
        $this->mappings = [];
    }


    /**
     * Add a mapping expectation
     *
     * @param string $key Mapping identifier
     * @param array $accepted_names Allowed columns names
     * @param boolean $required Column is required
     * @return $this
     * @throws MappingOverlapException
     */
    public function addMapping($key, array $accepted_names, $required = true) {
        $lower_accepted_names = [];

        foreach($accepted_names as $name) {
            $lower_name = strtolower($name);

            if ($this->getMapping($lower_name) !== false) {
                throw new MappingOverlapException(sprintf('Column name %s already mapped on %s', $name, $key));
            }

            $lower_accepted_names[] = $lower_name;
        }

        $this->mappings[$key] = [
            'accepted_names' => $lower_accepted_names,
            'required' => $required,
            'index' => null,
        ];

        return $this;
    }


    /**
     * Get Mapping keys
     *
     * @return array
     */
    public function getMappingKeys() {
        return array_keys($this->mappings);
    }


    /**
     * Get a mapped column index
     *
     * @param string $name
     * @return int|false
     */
    protected function getMapping($name) {
        foreach($this->mappings as $key => $data) {
            if (in_array(strtolower($name), $data['accepted_names'])) {
                return $key;
            }
        }

        return false;
    }


    /**
     * Map columns on array
     *
     * @param array $columns Array to map
     * @return array mappings
     * @throws MappingIncompleteException
     */
    public function map(array $columns) {
        $maps = [];

        foreach($columns as $index => $column_name) {
            $key = $this->getMapping($column_name);

            if ($key === false) {
                continue;
            }

            $this->mappings[$key]['index'] = $index;

            $maps[$key] = $index;
        }

        // Valider que tous les mappings requis sont satisfaits
        foreach($this->mappings as $key => $data) {
            if ($data['required'] && !array_key_exists($key, $maps)) {
                throw new MappingIncompleteException(sprintf('%s not found', $key));
            }
        }

        return $maps;
    }

}