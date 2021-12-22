<?php

namespace App\Repositories\ImageInformation;

use App\Models\ImageInformation;
use App\Repositories\AbstractRepository;
use App\Repositories\ImageInformation\ImageInformationRepositoryInterface;

/**
 * 画像情報リポジトリ
 * Class ImageInformationRepository
 * @package App\Repositories\ImageInformation
 */
class ImageInformationRepository extends AbstractRepository implements ImageInformationRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return ImageInformation::class;
    }

    /**
     * Save data import image zip
     * @param array $data
     * @return query
     */
    public function import($data = [])
    {
        try {
            $this->create($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Delete image
     * @param array $array_id
     * @return query
     */
    public function deleteImage($array_id = [])
    {
        return $this->clearQuery()->whereIn('id', $array_id)->delete();
    }

    /**
     * Get data image info pagination
     * @param array $search
     * @param int $limit
     * @return $query
     */
    public function getDataImageInfo($search, $limit)
    {
        $query = $this->clearQuery();
        if ($search['keyword'] ?? '') {
            $query = $query->where('file_name', 'like', '%' . $search['keyword'] . '%');
        }

        if (($search['image_update_datetime'] ?? '') == config('wysiwyg.config.flag_on')) {
            $query = $query->orderBy('update_at', 'ASC');
        } else {
            $query = $query->orderBy('update_at', 'DESC');
        }

        $query = $query->paginate($limit, ['*'], 'page', $search['page']);
        return $query;
    }

    /**
     * Get data image info
     * @param int $id
     * @param string $file_name
     * @return $query
     */
    public function findImageInfo($id, $file_name)
    {
        return $this->clearQuery()
            ->where('id', '!=', $id)
            ->where('file_name', $file_name)
            ->first();
    }

}
