<?php

namespace Empathy\ELib\File;
use Empathy\MVC\Config;



// overrides for testing purposes
if (defined('MVC_TEST_MODE')) {

    function is_uploaded_file($filename) {
        return file_exists($filename);
    }
    function move_uploaded_file($filename, $destination) {
        return copy($filename, $destination);
    }
}


class Upload
{
  
    public $error;
    public $target;
    public $target_dir;
    public $file;
  
    public function __construct($upload=true)
    {
        $this->file = '';
        $this->error = '';
        $this->target = '';
        $this->target_dir = Config::get('DOC_ROOT').'/public_html/episodes/';
        if ($upload) {
            $this->upload();
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getError()
    {
        return $this->error;
    }

    public function upload()
    {


        if (!isset($_FILES['file']['name']) || ($_FILES['file']['name'] == ''))
        {
            $this->error .= "Problem uploading file. Empty file?";		       
        }
        else
        {
            $name_array = explode('.', $_FILES['file']['name']);
            $size = sizeof($name_array);
            $ext = $name_array[$size-1];       
    	       
            if(!preg_match('/mp3/i', $ext))
            {
                $this->error .= "Invalid file format.";
            }
            else
            {
                $name = '';
                if(sizeof($name_array) > 2)
                {
                    for($i = 0; $i < $size-1; $i++)
                    {
                        $name .= $name_array[$i];
                        if($i+1 != $size-1)
                        {
                            $name .= '.';
                        }
                    }
                }
                else
                {
                    $name = $name_array[0];
                }


                $this->target = $this->target_dir.$name.".".$ext;	    
                // deal with duplicates
                $i = 1;
                while(file_exists($this->target))
                {
                    $this->target = $this->target_dir.$name."_".$i++.".".$ext;
                }	    
                $this->file = substr($this->target, strlen($this->target_dir));	    

                //echo $this->target; exit();

                if(!@move_uploaded_file($_FILES['file']['tmp_name'], $this->target))
                {
                    $this->error .= "Internal error";
                }

                //echo $this->error;	
                //exit();

            }    
        }
    }      


    public function remove($files)
    {     
        $success_arr = array();
        $all_files = array();
       
        foreach($files as $file)
        {
            if($file != '')
            {
                $all_files = array_merge($all_files, glob($this->target_dir.'*'.$file));
            }
        }   

        // print_r($all_files);
        // exit();


        foreach($all_files as $file)
        {
            array_push($success_arr, @unlink($file));	
        }    
        if(in_array(false, $success_arr))
        {
            $success = false;
        }    
        else
        {
            $success = sizeof($success_arr);
        }
        return $success;
    }  




}
