<?php

namespace App\Repositories\ImageInformation;

use App\Repositories\RepositoryInterface;

/**
 * 画像情報リポジトリインターフェース
 * Interface ImageInformationInterface
 * @package App\Repositories\ImageInformation
 */
interface ImageInformationRepositoryInterface extends RepositoryInterface
{
    /**
     * Save data import image zip
     * @param array $data
     */
    public function import($data = []);

    /**
     * Delete image
     * @param array $array_id
     * @return query
     */
    public function deleteImage($array_id = []);

    /**
     * Get data image info pagination
     * @param string $search
     * @param int $limit
     */
    public function getDataImageInfo($search, $limit);

    /**
     * Get data image info
     * @param int $id
     * @param string $file_name
     */
    public function findImageInfo($id, $file_name);

}
