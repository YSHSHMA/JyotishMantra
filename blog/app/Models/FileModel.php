<?php namespace App\Models;

use CodeIgniter\Model;

class FileModel extends BaseModel
{
    protected $uploadModel;
    protected $builderImages;
    protected $builderFiles;

    public function __construct()
    {
        parent::__construct();
        $this->uploadModel = new UploadModel();
        $this->builderImages = $this->db->table('images');
        $this->builderFiles = $this->db->table('files');
    }

    /*
    *------------------------------------------------------------------------------------------
    * IMAGES
    *------------------------------------------------------------------------------------------
    */

    //upload image
    public function uploadImage()
    {
        $tempData = $this->uploadModel->uploadTempImage('file');
        if (!empty($tempData)) {
            $tempPath = $tempData['path'];
            if ($tempData['ext'] == 'gif') {
                $gifPath = $this->uploadModel->postGifImageupload($tempData['name']);
                $data['image_big'] = $gifPath;
                $data['image_mid'] = $gifPath;
                $data['image_slider'] = $gifPath;
                $data['image_small'] = $gifPath;
                $data['image_mime'] = 'gif';
                $data['file_name'] = pathinfo($tempData['orj_name'], PATHINFO_FILENAME);
            } else {
                $data['image_big'] = $this->uploadModel->uploadPostImage($tempPath, 'big');
                $data['image_mid'] = $this->uploadModel->uploadPostImage($tempPath, 'mid');
                $data['image_slider'] = $this->uploadModel->uploadPostImage($tempPath, 'slider');
                $data['image_small'] = $this->uploadModel->uploadPostImage($tempPath, 'small');
                $data["image_mime"] = $this->uploadModel->getFileExtension($data['image_big']);
                $data['file_name'] = pathinfo($tempData['orj_name'], PATHINFO_FILENAME);
            }
            $data['user_id'] = user()->id;
            $data['storage'] = $this->generalSettings->storage;
            $db = \Config\Database::connect(null, false);
            $db->table('images')->insert($data);
            $db->close();
            $this->uploadModel->deleteTempFile($tempPath);
            //move to s3
            if ($data['storage'] == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->uploadFile($data['image_big']);
                if ($data['image_mime'] != 'gif') {
                    $awsModel->uploadFile($data['image_mid']);
                    $awsModel->uploadFile($data['image_slider']);
                    $awsModel->uploadFile($data['image_small']);
                }
            }
        }
    }

    //get image
    public function getImage($id)
    {
        return $this->builderImages->where('id', cleanNumber($id))->get()->getRow();
    }

    //get images
    public function getImages($limit)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderImages->where('user_id', user()->id);
        }
        return $this->builderImages->orderBy('id', 'DESC')->get($limit)->getResult();
    }

    //get more images
    public function getMoreImages($lastId, $limit)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderImages->where('user_id', user()->id);
        }
        return $this->builderImages->where('id <', cleanNumber($lastId))->orderBy('id', 'DESC')->get($limit)->getResult();
    }

    //search images
    public function searchImages($search)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderImages->where('user_id', user()->id);
        }
        return $this->builderImages->like('file_name', cleanStr($search))->orderBy('id', 'DESC')->get()->getResult();
    }

    //delete image
    public function deleteImage($id)
    {
        $image = $this->getImage($id);
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            if ($image->user_id != user()->id) {
                return false;
            }
        }
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteFile($image->image_big);
                if ($image->image_mime != 'gif') {
                    $awsModel->deleteFile($image->image_mid);
                    $awsModel->deleteFile($image->image_small);
                    $awsModel->deleteFile($image->image_slider);
                }
            } else {
                deleteFileFromServer($image->image_big);
                deleteFileFromServer($image->image_mid);
                deleteFileFromServer($image->image_small);
                deleteFileFromServer($image->image_slider);
            }
            $this->builderImages->where('id', $image->id)->delete();
        }
    }

    /*
    *------------------------------------------------------------------------------------------
    * FILES
    *------------------------------------------------------------------------------------------
    */

    //upload file
    public function uploadFile()
    {
        if ($this->generalSettings->storage == 'aws_s3') {
            $awsModel = new AwsModel();
            $file = $awsModel->uploadFileDirect('file', 'uploads/files/', 'file_');
            if (!empty($file['orj_name'])) {
                $data = [
                    'file_name' => $file['orj_name'],
                    'file_path' => $file['path'],
                    'user_id' => user()->id,
                    'storage' => 'aws_s3'
                ];
            }
        } else {
            $file = $this->uploadModel->uploadFile('file');
            if (!empty($file)) {
                $data = [
                    'file_name' => $file['orj_name'],
                    'file_path' => $file['path'],
                    'user_id' => user()->id,
                    'storage' => 'local'
                ];
            }
        }
        if (!empty($data)) {
            $db = \Config\Database::connect(null, false);
            $db->table('files')->insert($data);
            $db->close();
        }
    }

    //get files
    public function getFiles($limit)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderFiles->where('user_id', user()->id);
        }
        return $this->builderFiles->orderBy('id', 'DESC')->get($limit)->getResult();
    }

    //get file
    public function getFile($id)
    {
        return $this->builderFiles->where('id', cleanNumber($id))->get()->getRow();
    }

    //get more files
    public function getMoreFiles($lastId, $limit)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderFiles->where('user_id', user()->id);
        }
        return $this->builderFiles->where('id <', cleanNumber($lastId))->orderBy('id', 'DESC')->get($limit)->getResult();
    }

    //search files
    public function searchFiles($search)
    {
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            $this->builderFiles->where('user_id', user()->id);
        }
        return $this->builderFiles->like('file_name', cleanStr($search))->orderBy('id', 'DESC')->get()->getResult();
    }

    //delete file
    public function deleteFile($id)
    {
        $file = $this->getFile($id);
        if ($this->generalSettings->file_manager_show_all_files != 1) {
            if ($file->user_id != user()->id) {
                return false;
            }
        }
        if (!empty($file)) {
            if ($file->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteFile($file->file_path);
            } else {
                @unlink(FCPATH . $file->file_path);
            }
            $this->builderFiles->where('id', $file->id)->delete();
        }
    }
}
