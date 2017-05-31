<?php

App::uses('AppController', 'Controller');

class ImagesController extends AppController {

	var $modelName = 'Image';
	var $name = 'Images';

	public function beforeFilter() {
		$this->autoRender = false;
        $this->dir = WWW_ROOT.'theme'.DS.$this->theme.DS.'images'.DS;
		// goi den before filter cha
		parent::beforeFilter();
	}

	public function list_images()
	{
		$dir = $this->dir;
        $handle = opendir($dir);
        $arr_list_image = array();
        if($handle){
            while(($file = readdir($handle))!==false){
                if($file!='.' && $file!='..'){
                    $imagefile = $file;
                    if(is_file($dir.DS.$file)){
                        $arr_list_image[] = array(
                            'name'=>$imagefile
                        ,'type'=>'f'
                        ,'size'=>filesize($dir.DS.$file)
                        );
                    }
                }
            }
        }
        echo json_encode($arr_list_image);
    }

    public function delete_image(){
        $name = $_REQUEST['name'];
        $path = $this->dir;
        if(file_exists($path.$name))
            unlink ($path.$name);
    }

    public function upload_image()
    {
        $file_size = 200;
        $path = $this->dir;
        $arr_list_image = array();
        if(isset($_FILES['file'])){
            $file = $_FILES['file'];
            $filename = $file['name'];
            $uploadSuccess =  move_uploaded_file($file['tmp_name'],$this->dir.$filename);
            if( $uploadSuccess ) {
                $arr_list_image = array(
                    'name'=>$filename
                    ,'type'=>'f'
                    ,'size'=>$file_size
                );
            }
        }
        echo json_encode($arr_list_image);
    }

    public function thumb_image(){
		$dir = $this->dir;
        $image_file = $_GET['path'];
        if($image_file){
            if(file_exists($dir.$image_file) && is_file($dir.$image_file)){
                $file = $dir.$image_file;
                list($width, $height, $type) = @getimagesize($file);
                $thumb_width = 150;
                $percent = $thumb_width / $width;
                $thumb_height = floor($height * $percent);
                switch($type){
                    case 1;//Gif
                        $im_source = @imagecreatefromgif($file);
                        break;
                    case 2;//Jpg
                        $im_source = @imagecreatefromjpeg($file);
                        break;
                    case 3;//Png
                        $im_source = @imagecreatefrompng($file);
                        break;
                    default ;
                        $im_source = @imagecreatefromjpeg($file);
                        break;
                }
                $tmp_image = imagecreatetruecolor( $thumb_width, $thumb_height );
                imagecopyresized($tmp_image, $im_source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );

                /* Output the image with headers */
                header("Content-Type: image/webp");
                imagejpeg($tmp_image);
                imagedestroy($tmp_image);
                imagedestroy($im_source);
            }echo null;
        }echo null;
    }
}