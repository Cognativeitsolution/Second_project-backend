<?php

namespace App\Helpers;
use Image;

class Helper{
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    public static function upload_image($originalImage){

        $thumbnailImage = Image::make($originalImage);
        $thumbnailPath = public_path().'/thumbnail/';
        $originalPath = public_path().'/images/';
        $randomString = hexdec(uniqid());
        $thumbnailImage->save($originalPath.$randomString.$originalImage->getClientOriginalName());
        $thumbnailImage->resize(120,120);
        $thumbnailImage->save($thumbnailPath.$randomString.$originalImage->getClientOriginalName()); 
        $imageName = $randomString.$originalImage->getClientOriginalName();

        return $imageName;

    }

    public static function upload_banner_image($originalImage){

        $thumbnailImage = Image::make($originalImage);
        $thumbnailPath = public_path().'/thumbnail/';
        $originalPath = public_path().'/images/';
        $randomString = hexdec(uniqid());
        $thumbnailImage->save($originalPath.$randomString.$originalImage->getClientOriginalName());
        $thumbnailImage->resize(170,120);
        $thumbnailImage->save($thumbnailPath.$randomString.$originalImage->getClientOriginalName());
        $imageName = $randomString.$originalImage->getClientOriginalName();

        return $imageName;

    }

    public static function site_id(){
        return 999 ;
    }

}


