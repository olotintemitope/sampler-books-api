<?php

namespace App\Http\Contracts;

/**
 * Interface BaseModelInterface
 * @package App\Http\Contracts
 */
interface BaseModelInterface
{
    /**
     * Get all the models
     *
     * @return mixed
     */
    public function getAll();

    /**
     * Create a new model
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Delete a model
     *
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * Update a model
     *
     * @param int $id
     * @param array $attributes
     * @return mixed
     */
    public function update(int $id, array $attributes);

    /**
     * Find a single model
     *
     * @param int $id
     * @return mixed
     */
    public function findOne(int $id);
}