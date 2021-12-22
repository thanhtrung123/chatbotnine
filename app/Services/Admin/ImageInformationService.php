<?php

namespace App\Services\Admin;

use App\Http\Controllers\Traits\FormTrait;
use App\Repositories\ImageInformation\ImageInformationRepositoryInterface;
use App\Repositories\RepositoryInterface;
use App\Services\RepositoryServiceInterface;
use Carbon\Carbon;
use ZipArchive;
use DB;

/**
 * 画像情報サービス
 * Class ImageInformationService
 * @package App\Services\Admin
 */
class ImageInformationService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    use FormTrait;
    /**
     * @var ImageInformationRepositoryInterface
     */
    private $repository;

    /**
     * ImageInformationService constructor.
     * @param ImageInformationRepositoryInterface $repository
     */
    public function __construct(ImageInformationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return ImageInformationRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Import file zip
     * @param Request $request
     * @param string $path_dir
     * @return bool TRUE|FALSE
     */
    public function import($request, $path_dir)
    {
        try {
            DB::beginTransaction();
            $zip_path = storage_path('app/' . $path_dir);
            $zip = new ZipArchive;
            $name_folder_zip = '';
            //整合性チェック処理
            $image_lists_db = $this->getDataImage();
            $images_list_dir = $this->getListImageDir();
            $this->checkImageDBWithDir($image_lists_db, $images_list_dir);
            $this->checkImageDirWithDB($image_lists_db, $images_list_dir);
            $data = array();
            if ($zip->open($zip_path, ZipArchive::CREATE) === true) {
                for ($i = 0; $i < $zip->count(); $i++) {
                    //folder
                    if ($i == 0) {
                        $name_folder_zip = $zip->statIndex($i)['name'];
                    }
                    //image
                    if ($i > 0) {
                        $data[$i]['file_name'] = substr($zip->getNameIndex($i), strlen($name_folder_zip));
                        $data[$i]['file_path'] = env('QA_IMAGES_PATH') . '/' . substr($zip->getNameIndex($i), strlen($name_folder_zip));
                        $this->getRepository()->clearQuery();
                        $image = $this->getRepository()->findOneBy(['file_path' => $data[$i]['file_path']]);
                        if (empty($image)) {
                            $data[$i]['post_at'] = Carbon::now();
                            $data[$i]['update_at'] = Carbon::now();
                            $this->getRepository()->import($data[$i]);
                        } else {
                            $data_update = [
                                'update_at' => Carbon::now(),
                            ];
                            $this->getRepository()->update($image['id'], $data_update);
                        }
                    }
                }
                $path = pathinfo(realpath($zip_path), PATHINFO_DIRNAME);
                if (!$zip->extractTo($path)) {
                    return false;
                }
                $zip->close();
                $real_path = public_path(env('QA_IMAGES_PATH') . '/');
                if (!is_dir($real_path)) {
                    mkdir($real_path, 0777, true);
                }
                $tmp_path = $path . '/' . $name_folder_zip;
                $result = $this->copyImage($tmp_path, $real_path);
                if (!$result) {
                    return false;
                }
                rmdir($tmp_path);
                // Remove Zip
                unlink($zip_path);
                DB::commit();
                return true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * DB上に画像情報がある & 画像ディレクトリ上に実画像ファイルがない
     * @param array $image_lists_db
     * @param array $images_list_dir
     * @return
     */
    public function checkImageDBWithDir($image_lists_db, $images_list_dir)
    {
        $image_list_diff = array_diff($image_lists_db, $images_list_dir);
        if (count($image_list_diff) > 0) {
            $this->getRepository()->deleteImage(array_keys($image_list_diff));
        }
        return;
    }

    /**
     * DB上に画像情報がない & 画像ディレクトリ上に実画像ファイルがある
     * @param array $image_lists_db
     * @param array $images_list_dir
     * @return
     */
    public function checkImageDirWithDB($image_lists_db, $images_list_dir)
    {
        $image_list_diff = array_diff($images_list_dir, $image_lists_db);
        if (count($image_list_diff) > 0) {
            $data_insert = [];
            $i = 0;
            foreach ($image_list_diff as $value) {
                $i++;
                $file_name = substr($value, strlen(env('QA_IMAGES_PATH')) + 1);
                $data_insert[$i]['file_name'] = $file_name;
                $data_insert[$i]['file_path'] = $value;
                $data_insert[$i]['post_at'] = Carbon::now();
                $data_insert[$i]['update_at'] = Carbon::now();
            }
            $this->getRepository()->createMulti($data_insert);
        }
        return;
    }

    /**
     * get all image in directory
     * @return array
     */
    public function getListImageDir()
    {
        // get all image in directory
        $images_list_dir = glob(public_path(env('QA_IMAGES_PATH') . '/*'));
        // convert file path
        foreach ($images_list_dir as $key => $value) {
            if (!is_file($value)) {
                unset($images_list_dir[$key]);
            } else {
                $images_list_dir[$key] = substr($value, strlen(public_path()));
            }
        }
        return $images_list_dir;
    }

    /**
     * get data image from tbl_image_information
     * @return array
     */
    public function getDataImage()
    {
        $this->getRepository()->clearQuery();
        // get all data in db
        $datas = $this->getRepository()->getData();
        $image_lists_db = [];
        // get file path by id
        foreach ($datas as $key => $value) {
            $image_lists_db[$value->id] = $value->file_path;
        }
        return $image_lists_db;
    }

    /**
     * copy image tmp folder to real folder
     * @param String $tmp_path
     * @param String $real_path
     * @return bool TRUE|FALSE
     */
    public function copyImage($tmp_path, $real_path)
    {
        try {
           // Get array of all source files
            $files = scandir($tmp_path);
            // Cycle through all source files
            foreach ($files as $file) {
                if (in_array($file, array(".", ".."))) {
                    continue;
                }
                // If we copied this successfully, mark it for deletion
                if (copy($tmp_path . $file, $real_path . $file)) {
                    $delete[] = $tmp_path . $file;
                }
            }
            // Delete all successfully-copied files
            foreach ($delete as $file) {
                unlink($file);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
