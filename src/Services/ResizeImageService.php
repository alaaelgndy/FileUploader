<?php

namespace Elgndy\FileUploader\Services;

use Intervention\Image\Facades\Image as InterventionImage;

class ResizeImageService
{
    /**
     * @var    Array $results
     * @return Array of images every image has obj contains infos about it =>
     * {mime , dirname , basename , extension , filename}
     */
    protected $results = [];

    /**
     * @param Object Intervention\Image\Facades\Image $image_package
     */
    protected $image_package;

    public function __construct(InterventionImage $i)
    {
        $this->image_package = $i;
    }

    /**
     * @param  string $local_image_name
     * @param  string $folder_path
     * @param  Array $sizes
     * @return Array Images after resize
     */
    public function resize(string $local_image_name, string $local_path, array $sizes): array
    {
        foreach ($sizes as $size) {
            $resolver = $this->resolveSize($size);
            $this->results[] = $this->image_package::make($local_path . '/' . $local_image_name)
                ->resize($resolver['width'], $resolver['height'])
                ->save($local_path . '/' . $size . '_' . $local_image_name);
        }
        return $this->results;
    }

    /**
     * @param  String $size
     * @return Array [width , height]
     */
    private function resolveSize(array $size): array
    {
        $array = explode(",", $size);
        return ['width' => intval($array[0]), 'height' => intval($array[1])];
    }
}
