<?php

namespace App\Http\Contracts;

/**
 * Interface BaseModelInterface
 * @package App\Http\Contracts
 */
interface BaseModelInterface
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * @param int $id
     * @param array $attributes
     * @return mixed
     */
    public function update(int $id, array $attributes);

    /**
     * @param int $id
     * @return mixed
     */
    public function findOne(int $id);

    /**
     * @param int $id
     * @param array $attributes
     * @return mixed
     */
//    public function findWhere(int $id, array $attributes);
}