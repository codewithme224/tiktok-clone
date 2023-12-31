<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileService
{
    public function updateImage($model, $request)
    {
        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $extension = hexdec(uniqid()). '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));

            $img->resize(
                $request->width,
                $request->height,
                $request->left,
                $request->top
            );

        $img->save(public_path() . '/files/' . $extension);

        } else if (!empty($model->image)) {
            $currentImage = public_path() . $model->image;

            if (file_exists($currentImage) && $currentImage != public_path() . '/user-placeholder.png') {
                unlink($currentImage);
            }
        }

        $model->image = '/files/' . $extension;

        return $model;
    }


    public function addVideo($model, $request)
    {
        $video = $request->file('video');
        $extension = $video->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $video->move(public_path() . '/files/', $name);
        $model->video = '/files/' . $name;

        return $model;
    }
}
