<?php
class common_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->utc_time 	= time();
    }
    
    /*************** GET REQUEST WITH AJAX CALL OR NOT FROM DATABASE ******** 15-2-2012 KD********/
    function isAjax()
    {
            if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                    return true;	
            return false;
    }
    
    /*************** GET REQUEST WITH AJAX CALL OR NOT FROM DATABASE ******** 15-2-2012 KD********/
    function checkUserSession()
    {
        $sess_user_id = $this->session->userdata("NC_ADMIN_ID");
		
		if($sess_user_id == ''){
            $direct_url	= $this->uri->uri_string();
            $this->session->set_userdata('NC_DIRECT_URL',$direct_url);
            //redirect(LOGINURL);
            return false;
		}
        return true;
    }
    
    /*
    | -------------------------------------------------------------------
    |  COMMAN FUNCTION TO ADD NEW DAATA 
    | -------------------------------------------------------------------
    */
   
    function login_check($username='',$password='')
    {
        if($username == '' || $password == ''){
            return false;
        }
		
		if(filter_var($username, FILTER_VALIDATE_EMAIL)){
			$where 	= array(
					'ADMIN_EMAIL' 		=> $username,
					'ADMIN_PASSWORD' 	=> sha1($password),
					'ADMIN_STATUS'   	=> '1',
				);
		}else{
			return false;
		}
        
        $user_detail = $this->get_single_data('*', TBL_ADMIN, $where);
		
        if(count($user_detail) > 0){
            // UPDATE LAST LOGIN TIME
            $data 	= array(
						'ADMIN_LAST_LOGIN_DATE'  => time(),
					);
            $where 	= array('ADMIN_ID' => $user_detail['ADMIN_ID']);
            $admin_login_time_update = $this->update_data($data, TBL_ADMIN, $where);
			
			$user_detail = $this->get_single_data('*', TBL_ADMIN, $where);
			
            return $user_detail;
        }
        return false;
    }

    /*************** GET ALL RECORD COUNT FROM DATABASE ******** 21-09-2012 KD********/
    function get_setting_value($key)
    {
		$where = array(
			'S_KEY' => $key
			);
			$kd         = $this->get_single_data('*',TBL_ADMINSETTINGS,$where);
		if(isset($kd['S_VALUE']) && $kd['S_VALUE'] != "")
		{
			return $kd['S_VALUE'];
		}
		return "";
    }

    /*************** GET ALL RECORD COUNT FROM DATABASE ******** 21-09-2012 KD********/
    function set_setting_value($key,$value)
    {
		$data = array('S_VALUE' => $value);
		$where = array(
			'S_KEY' => $key
		);
		$kd         = $this->update_data($data,TBL_ADMINSETTINGS,$where);
		return true;
    }
	
    /*************** GET SINGLE RECORD FROM DATABASE ******** 15-2-2012 KD********/
    function get_single_data($fields,$table_name,$where=array(),$join=array())
    {        
        //$fields =    'title, content, date';
        $this->db->select($fields);
        if(count($join) > 0)
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value);
            }
        }
        if(count($where) > 0)
        {
            $this->db->where($where);
        }
        $kd = $this->db->get($table_name);
        if ($kd->num_rows() > 0)
        {
           //$row = $kd->row_array();
           $row = $kd->first_row('array'); // IF WE PASS ARRAY PARAMETER THEN ITS PASS IN ARRAY FORMATE OTHERWISE  ITS PASS BYDEFAULT IN OBJECT TYPE
           return $row;
        } 
        return array();
    }
    

    /*************** GET SINGLE RECORD FROM DATABASE ******** 15-2-2012 KD********/

    function get_single_data_query($query)
    {        
        $kd = $this->db->query($query);
        if ($kd->num_rows() > 0)
        {
           //$row = $kd->row_array();
           $row = $kd->first_row('array'); // IF WE PASS ARRAY PARAMETER THEN ITS PASS IN ARRAY FORMATE OTHERWISE  ITS PASS BYDEFAULT IN OBJECT TYPE
           return $row;
        } 
        return array();
    }

    /*************** GET ALL RECORD FROM DATABASE ******** 15-2-2012 KD********/

    function get_all_data($fields,$table_name,$where=array(),$join=array(),$order_by = array(),$limit='')
    {
        $this->db->select($fields);
        if(is_array($join) && count($join) > 0)
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value);
            }
        }
        if(is_array($where) && count($where) > 0)
        {
            $this->db->where($where);
        }
        if(is_array($order_by) && count($order_by) > 0)
        {
            foreach($order_by as $key => $value)
            {
                $this->db->order_by($key, $value);
            }
        }
        if($limit != '')
        {
            $this->db->limit($limit);
        }
        $kd = $this->db->get($table_name);
        $kd_result = array();
        foreach ($kd->result_array() as $row)
        {
            $kd_result[] = $row;
        }
        return $kd_result;
    }

    /*************** GET ALL RECORD FROM DATABASE ******** 15-2-2012 KD********/
    function get_all_data_query($query)
    {
        $kd = $this->db->query($query);
        $kd_result = array();
        foreach ($kd->result_array() as $row)
        {
            $kd_result[] = $row;
        }
		return $kd_result;
    }

    /*************** GET ALL RECORD COUNT FROM DATABASE ******** 21-09-2012 KD********/
    function get_all_count($fields,$table_name,$where=array(),$join=array(),$order_by = array())
    {
        $this->db->select($fields);
        if(is_array($join) && count($join) > 0)
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value);
            }
        }
        if(is_array($where) && count($where) > 0)
        {
            $this->db->where($where);
        }
        if(is_array($order_by) && count($order_by) > 0)
        {
            foreach($order_by as $key => $value)
            {
                $this->db->order_by($key, $value);
            }
        }
        $kd_result = $this->db->count_all_results($table_name);
        return $kd_result;
    }

    /*************** GET ALL RECORD COUNT FROM DATABASE ******** 21-09-2012 KD********/
    function get_all_count_query($query)
    {
        $kd 	= $this->db->query($query);
        return $kd->num_rows();
    }

   /*************** INSERT RECORD INTO DATABASE ******** 16-2-2012 KD********/
   
    function add_data($data,$table_name) 
    {
        $retId = $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }

    /*************** UPDATE RECORD INTO DATABASE ******** 16-2-2012 KD********/
    function update_data($data,$table_name,$where) 
    {
        $update_data = $this->db->update($table_name, $data, $where);
        return $this->db->affected_rows();
        //return $update_data;
    }

    /* For truncate string and add the ellipses to string */
    function truncate($string, $del,$dot=false)
    {
        $len = strlen($string);
        if ($len > $del)
        {
            $new = substr($string,0,$del);
            if($dot == true)
            {
                $new    .= "...";
            }
            return $new;
        }
        else
        {
            return $string;
        }
    }

    /*********** Generates a File Upload Code 23 May 2012 KD*****************/
    function UploadFile($files,$path,$type='')
    {	
	    if($type == '1')
	    {
		    $extensions	=	array('jpeg','JPEG','gif','GIF','png','PNG','jpg','JPG');
	    }
	    else if($type == '2')
	    {
		    $extensions	=	array('wmv','WMV','wav','WAV','m4r','M4R','mpeg','MPEG','mpg','MPG','mpe','MPE','mov','MOV','avi','AVI','mp4','MP4','m4v','M4V','3gp','3GP','flv','FLV','pem','PEM');
	    }
	    else
	    {
		    $extensions	=	array('jpeg','JPEG','gif','GIF','png','PNG','jpg','JPG','pdf','PDF','ZIP','zip','rar','RAR','html','HTML','TXT','txt','doc','docx','DOC','DOCX','ppt','PPT','pptx','PPTX','xlsx','XLSX','xls','XLS','exe','EXE','mp3','MP3','wav','WAV','m4r','M4R','mpeg','MPEG','mpg','MPG','mpe','MPE','mov','MOV','avi','AVI','wmv','WMV','3gp','3GP','flv','FLV','pem','PEM');
	    }
	    $destination 		=	$path.$files["name"];
	    // GET FILE PARTS
	    $fileParts		=	pathinfo($files['name']);
	    $file_name		=	$files['name'];
	    $file_name_only		=	$fileParts['filename'];
	    $file_name_only		= 	preg_replace('/[^a-zA-Z0-9]/','-',$file_name_only);
	    $file_extention		=	$fileParts['extension'];
	    $Count			=	0;
	    $destination 		=   	$path.$file_name_only.".$file_extention";
	    $file_name		=	$file_name_only.".$file_extention";;

	    // THIS SHOULD KEEP CHECKING UNTIL THE FILE DOESN'T EXISTS
	    while( file_exists($destination))
	    {
		    $Count 		+= 1;
		    $destination 	=  $path. $file_name_only."-".$Count.".$file_extention";
		    $file_name 	=  $file_name_only."-".$Count.".$file_extention";
	    }
	    if(!empty($files))
	    {
		    if(in_array($file_extention,$extensions))
		    {
			    if(move_uploaded_file($files["tmp_name"],$destination))
			    {
				    return $file_name;
			    }
			    else
			    {
				    return false;
			    }
		    }
		    else
		    {
			    return false;
		    }	
	    } 
    }

    /*************** GENERATES A RESIZE IMAGE CODE ******** 01-03-2012 KD********/
    function resizeImage($originalImage,$toWidth,$toHeight,$filename, $path, $extention)
    {
		$new_width = $toWidth;
        $new_height = $toHeight;
        // Get the original geometry and calculate scales
        list($width, $height) = getimagesize($originalImage);
        $xscale=$width/$toWidth;
        $yscale=$height/$toHeight;
        if(($width != $toWidth) && ($height != $toHeight))
        {
            // Recalculate new size with default ratio
            if ($yscale<$xscale)
            {
                $new_width = round($width * (0.8/$yscale));
                $new_height = round($height * (1/$yscale));
            }
            else
            {
                $new_width = round($width * (1/$xscale));
                $new_height = round($height * (1/$xscale));
            }
        }
        else
        {
			$new_width 	= $width;
            $new_height = $height;
        }
        // Resize the original image
        $imageResized = imagecreatetruecolor($new_width, $new_height);
        $extention = strtolower($extention);
        if($extention == 'image/jpeg' || $extention == 'image/jpg') 
        { 
            $imageTmp     = imagecreatefromjpeg ($originalImage);
            imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($imageResized,$path.IMG_THUMB_PRE.$filename,100); 
        }
        else if($extention == 'image/png')
        {
            $imageTmp     = imagecreatefrompng ($originalImage);
            imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagepng($imageResized,$path.IMG_THUMB_PRE.$filename);
        }
        else if($extention == 'image/gif')
        {
            $imageTmp     = imagecreatefromgif ($originalImage);
            imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagegif($imageResized,$path.IMG_THUMB_PRE.$filename,100); 
        }
        return $imageResized; 
    }
    function deleteImageInfolder($k_image,$path)
    {	
        if(file_exists($path))
        {
            @unlink($path.$k_image);
        }
        if(file_exists($path))
        {
            @unlink($path.IMG_THUMB_PRE.$k_image);
        }
    }

    /******************* DLETE IMAGES AND DIRECTORY ******** 03/03/2012 KD*************/
    function delete_folder($directory,$empty = false) 
    {
        if(substr($directory,-1) == "/") 
        {
            $directory = substr($directory,0,-1);
        }
        if(!file_exists($directory) || !is_dir($directory)) 
        {
            return false;
        } 
        elseif(!is_readable($directory)) 
        {
            return false;
        } 
        else 
        {
           $directoryHandle = opendir($directory);
            while ($contents = readdir($directoryHandle)) 
            {
                if($contents != '.' && $contents != '..') 
                {
                    $path = $directory . "/" . $contents;
					if(is_dir($path)) 
                    {
                        $this->deleteAll($path);
                    } 
                    else 
                    {
                        unlink($path);
                    }
                }
            }
            closedir($directoryHandle);
            if($empty == false) 
            {
                if(!rmdir($directory)) 
                {
                    return false;
                }
            }
            return true;
        }
    }

    /******************* Hour to second ******** 10/04/2012 KD*************/
    function hr_to_sec($hr_time) 
    {
        if($hr_time == '')
        {
            return false;
        }
        $hr_time_arr	= explode(':',$hr_time);
        $hr_time_hr     = (isset($hr_time_arr['0']))?$hr_time_arr['0']:0;
        $hr_time_mnt    = (isset($hr_time_arr['1']))?$hr_time_arr['1']:0;
        $hr_time_sec    = (isset($hr_time_arr['1']))?$hr_time_arr['1']:0;
		$total_time_sec	= ($hr_time_hr*3600) + ($hr_time_mnt*60) + ($hr_time_sec);
        return $total_time_sec;
    }



    /******************* Second to hour ******** 10/04/2012 KD*************/
    function sec_to_hr($sec_time,$format='H:i:s') 
    {
        if($sec_time == '')
        {
            return false;
        }
        $hr  = floor($sec_time / 3600);
		$mnt = floor(($sec_time % 3600) / 60);
        $sec = ($sec_time % 3600) % 60;
        if($format == 'H:i:s')
        {
            $total_hr = $hr.':'.$mnt.':'.$mnt;
        }
        else if($format == 'H:i')
        {
            $total_hr = $hr.':'.$mnt;
        }
        else if($format == 'i:s')
        {
            $total_hr = $mnt.':'.$sec;
        }
        else if($format == 'H:s')
        {
            $total_hr = $hr.':'.$sec;
        }
        else if($format == 'H')
        {
            $total_hr = $hr;
        }
        else if($format == 's')
        {
            $total_hr = $sec;
        }
        else if($format == 'i')
        {
            $total_hr = $mnt;
        }
        else
        {
            $total_hr = $hr.':'.$mnt.':'.$sec;
        }
        return $total_hr;
    }

    /******************* Day Name To week day no ******** 17/04/2012 KD*************/
    function dayname_to_weekdayno($data='Monday')
    {
        $numDaysToMon = '';
        switch($data)
        {
            case 'Monday': $numDaysToMon = 1; break;
            case 'Tuesday': $numDaysToMon = 2; break;
            case 'Wednesday': $numDaysToMon = 3; break;
            case 'Thursday': $numDaysToMon = 4; break;
            case 'Friday': $numDaysToMon = 5; break;
            case 'Saturday': $numDaysToMon = 6; break;
            case 'Sunday': $numDaysToMon = 7; break;   
        }
        return $numDaysToMon;
    }

    /******************* week day no To Day Name ******** 17/04/2012 KD*************/
    function weekdayno_to_dayname($data='1')
    {
        $numDaysToMon = '';
        switch($data)
        {
            case '1': $numDaysToMon = 'Monday'; break;
            case '2': $numDaysToMon = 'Tuesday'; break;
            case '3': $numDaysToMon = 'Wednesday'; break;
            case '4': $numDaysToMon = 'Thursday'; break;
            case '5': $numDaysToMon = 'Friday'; break;
            case '6': $numDaysToMon = 'Saturday'; break;
            case '7': $numDaysToMon = 'Sunday'; break;   
        }
        return $numDaysToMon;
    }

    /*********** Generates a Photo From Url Code *****************/
    function GetImageFromUrl($link)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch,CURLOPT_URL,$link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
    }



    /*********** CREATE TUMB COMPANY 06-08-2012 KD **********************/
	function createCropImage($name, $newname, $new_w, $new_h, $border=true, $base64=false,$left_co=0,$top_co=0,$width_co=0,$height_co=0)
	{
		if(file_exists($newname)){
		@unlink($newname);
		}
		if(!file_exists($name)){
			return false;
		}
		$arr = explode(".",$name);
		$ext = $arr[count($arr)-1];
	
		if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
		$transparency = false;
			$img = @imagecreatefromjpeg($name);
		} elseif($ext=="png" || $ext=="PNG"){
		$transparency = true;
			$img = @imagecreatefrompng($name);
		} elseif($ext=="gif" || $ext=="GIF") {
		$transparency = true;
			$img = @imagecreatefromgif($name);
		}
		if(!$img || !isset($img)){
			return false;
		}
		$old_x = imageSX($img);
		$old_y = imageSY($img);
	  /*  if($old_x < $new_w && $old_y < $new_h) {
			$thumb_w = $old_x;
			$thumb_h = $old_y;
		} elseif ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = floor(($old_y*($new_h/$old_x)));
		} elseif ($old_x < $old_y) {
			$thumb_w = floor($old_x*($new_w/$old_y));
			$thumb_h = $new_h;
		} elseif ($old_x == $old_y) { */
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		//}
		$thumb_w = ($thumb_w<1) ? 1 : $thumb_w;
		$thumb_h = ($thumb_h<1) ? 1 : $thumb_h;
		$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	   
		if($transparency) {
			if($ext=="png"  || $ext=="PNG") {
				imagealphablending($new_img, false);
				$colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
				imagefill($new_img, 0, 0, $colorTransparent);
				imagesavealpha($new_img, true);
			} elseif($ext=="gif"  || $ext=="GIF") {
				$trnprt_indx = imagecolortransparent($img);
				if ($trnprt_indx >= 0) {
					//its transparent
					$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_img, 0, 0, $trnprt_indx);
					imagecolortransparent($new_img, $trnprt_indx);
				}
			}
		} else {
			Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		}
		imagecopyresampled($new_img, $img, 0,0,$left_co,$top_co, $thumb_w, $thumb_h, $width_co, $height_co); 
		if($border) {
			$black = imagecolorallocate($new_img, 0, 0, 0);
			imagerectangle($new_img,0,0, $thumb_w, $thumb_h, $black);
		}
		if($base64) {
			ob_start();
			imagepng($new_img);
			$img = ob_get_contents();
			ob_end_clean();
			$return = base64_encode($img);
		} else {
			if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
				imagejpeg($new_img, $newname);
				$return = true;
			} elseif($ext=="png" || $ext=="PNG"){
				imagepng($new_img, $newname);
				$return = true;
			} elseif($ext=="gif" || $ext=="GIF") {
				imagegif($new_img, $newname);
				$return = true;
			}
		}
		imagedestroy($new_img);
		imagedestroy($img);
		return $return;
	}

    /*********** CREATE TUMB RESPECT THUMB 06-08-2012 KD **********************/
    function createRespectThumbImage($name, $newname, $new_w, $new_h, $border=true, $base64=false)
    {
		if(file_exists($newname)){
			@unlink($newname);
		}
		if(!file_exists($name)){
			return false;
		}
		$arr = explode(".",$name);
		$ext = $arr[count($arr)-1];
		if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
			$transparency = false;
			$img = @imagecreatefromjpeg($name);
		} elseif($ext=="png" || $ext=="PNG"){
			$transparency = true;
			$img = @imagecreatefrompng($name);
		} elseif($ext=="gif" || $ext=="GIF") {
			$transparency = true;
			$img = @imagecreatefromgif($name);
		}
		if(!$img){
			return false;
		}
		$old_x = imageSX($img);
		$old_y = imageSY($img);
		if($old_x < $new_w && $old_y < $new_h) {
			$thumb_w = $old_x;
			$thumb_h = $old_y;
		} elseif ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = floor(($old_y*($new_h/$old_x)));
		} elseif ($old_x < $old_y) {
			$thumb_w = floor($old_x*($new_w/$old_y));
			$thumb_h = $new_h;
		} elseif ($old_x == $old_y) { 
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
		$thumb_w = ($thumb_w<1) ? 1 : $thumb_w;
		$thumb_h = ($thumb_h<1) ? 1 : $thumb_h;
		$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
		if($transparency) {
			if($ext=="png"  || $ext=="PNG") {
				imagealphablending($new_img, false);
				$colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
				imagefill($new_img, 0, 0, $colorTransparent);
				imagesavealpha($new_img, true);
			} elseif($ext=="gif"  || $ext=="GIF") {
				$trnprt_indx = imagecolortransparent($img);
				if ($trnprt_indx >= 0) {
					//its transparent
					$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_img, 0, 0, $trnprt_indx);
					imagecolortransparent($new_img, $trnprt_indx);
				}
			}
		} else {
			Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		}
		imagecopyresampled($new_img, $img, 0,0,0,0, $thumb_w, $thumb_h, $old_x, $old_y); 
		if($border) {
			$black = imagecolorallocate($new_img, 0, 0, 0);
			imagerectangle($new_img,0,0, $thumb_w, $thumb_h, $black);
		}
		if($base64) {
			ob_start();
			imagepng($new_img);
			$img = ob_get_contents();
			ob_end_clean();
			$return = base64_encode($img);
		} else {
			if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
				imagejpeg($new_img, $newname);
				$return = true;
			} elseif($ext=="png" || $ext=="PNG"){
				imagepng($new_img, $newname);
				$return = true;
			} elseif($ext=="gif" || $ext=="GIF") {
				imagegif($new_img, $newname);
				$return = true;
			}
		}
		imagedestroy($new_img);
		imagedestroy($img);
		return $return;
	}

	/*********** CREATE TUMB RESPECT THUMB 25-09-2012 KD **********************/
	function createThumbImage($name, $newname, $new_w, $new_h, $border=true, $base64=false)
    {
		if(file_exists($newname)){
			@unlink($newname);
		}
		if(!file_exists($name)){
			return false;
		}
		$arr = explode(".",$name);
		$ext = $arr[count($arr)-1];
		if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
			$transparency = false;
			$img = imagecreatefromjpeg($name);
		} elseif($ext=="png" || $ext=="PNG"){
			$transparency = true;
			$img = @imagecreatefrompng($name);
		} elseif($ext=="gif" || $ext=="GIF") {
			$transparency = true;
			$img = @imagecreatefromgif($name);
		}
		if(!isset($img) || !$img){
			return false;
		}
		$old_x = imageSX($img);
		$old_y = imageSY($img);
		
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		
		$thumb_w = ($thumb_w<1) ? 1 : $thumb_w;
		$thumb_h = ($thumb_h<1) ? 1 : $thumb_h;
		$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
		   
		if($transparency) {
			if($ext=="png" || $ext=="PNG") {
			imagealphablending($new_img, false);
			$colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
			imagefill($new_img, 0, 0, $colorTransparent);
			imagesavealpha($new_img, true);
			} elseif($ext=="gif"  || $ext=="GIF") {
			$trnprt_indx = imagecolortransparent($img);
			if ($trnprt_indx >= 0) {
				//its transparent
				$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
				$trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($new_img, 0, 0, $trnprt_indx);
				imagecolortransparent($new_img, $trnprt_indx);
			}
			}
		} else {
			Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		}
		imagecopyresampled($new_img, $img, 0,0,0,0, $thumb_w, $thumb_h, $old_x, $old_y); 
		if($border) {
			$black = imagecolorallocate($new_img, 0, 0, 0);
			imagerectangle($new_img,0,0, $thumb_w, $thumb_h, $black);
		}
		if($base64) {
			ob_start();
			imagepng($new_img);
			$img = ob_get_contents();
			ob_end_clean();
			$return = base64_encode($img);
		} else {
			if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG"){
			imagejpeg($new_img, $newname);
			$return = true;
			} elseif($ext=="png" || $ext=="PNG"){
			imagepng($new_img, $newname);
			$return = true;
			} elseif($ext=="gif" || $ext=="GIF") {
			imagegif($new_img, $newname);
			$return = true;
			}
		}
		imagedestroy($new_img);
		imagedestroy($img);
		return $return;
    }

    function get_week_start_date($wk_num, $yr, $first = 0) 
    { 
		$wk_ts  = strtotime('+' . $wk_num . ' weeks', strtotime($yr . '0101')); 
		$mon_ts = strtotime('-' . date('w', $wk_ts) + $first . ' days', $wk_ts); 
		return $mon_ts;
    }



    function get_last_week_time_array($weekCount='52')  // KD MAX 52 WEEK // max 52;
    {   
		$past_year =  date('Y',time())-1;
		$year_weak = array();
		for($week_number=0;$week_number<56;$week_number++)
		{
			$year_weak[] = $this->get_week_start_date($week_number, date('Y',time())-1);
		}
		for($week_number=0;$week_number<56;$week_number++)
		{
			$weektime = $this->get_week_start_date($week_number, date('Y',time()));
			if($weektime <= $this->utc_time )
			{
				$year_weak[] = $weektime;
			}
		}
		$year_weak = array_unique($year_weak,SORT_STRING);
		asort($year_weak);
		$k = array();
		foreach($year_weak as $key => $value)
		{
			$k[] = $value;
		}
		for($i=count($k);$i > count($k)-$weekCount;$i--)
		{
			$j[] =   $k[$i-1];
		}
		return $j;
	}

    function lastday_month($month = '', $year = '')
	{
		if (empty($month)) {
			$month = date('m');
		}
		if (empty($year)) {
		   $year = date('Y');
		}
		$result = strtotime("{$year}-{$month}-01");
		$result = strtotime('-1 second', strtotime('+1 month', $result));
		return $result;
	}

    function get_last_month_time_array($total_month_point)  // KD MAX 52 WEEK // max 52;
    {
		$k=0;
		$year = date('Y',$this->utc_time);
		$current_month = date('m',$this->utc_time) + 1;
		//  $total_month_point = 31;
		$kd = 0;
		$month_array = array();
		for($i=0;$i<3;$i++)
		{
			if($i == '0')
			{
				for($j=$current_month;$j>0 && $kd<$total_month_point;$j--)
				{
					$kd = $kd + 1;
					$month_array[] = $this->lastday_month($j,$year);
				}
			}
			else
			{
				for($j=12;$j>0 && $kd<$total_month_point;$j--)
				{
					$kd = $kd + 1;
					$month_array[] = $this->lastday_month($j,$year);
				}
			}
			$year = $year - 1;
		}
		return $month_array;
    }
    
	 ///////////
    /**
     * Sending Push Notification
     */
    public function send_notification_android($registatoin_ids, $message ,$gcm_key) {
/*	if(IS_ANDROID_PUSH_ON == 'false')
	{
	    return true;
	} */
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

		$fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );

		$headers = array(
            //'Authorization: key=AIzaSyDzJwaNvStxxQmMG1c6oHrOthQPJ4pifOw',
			'Authorization: key='.$gcm_key,
            'Content-Type: application/json'
        );
		
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        return $result;
    }

    function send_notification_ios($userdeviceToken,$body = array(),$pem_file_path,$app_is_live='1')
    {
  /*      if(IS_IOS_PUSH_ON != 'true')
        {
            return true;
        } */
        // Construct the notification payload
        if(!isset($body['aps']['sound']))
        {
            $body['aps']['sound'] = "default";
	    }

        // End of Configurable Items
        $payload     = json_encode($body);
        $ctx     = stream_context_create();
        $filename     = $pem_file_path;
        if(!file_exists($filename))
        {
			return true;
        }
    /*    stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);

        // assume the private key passphase was removed.
    //    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp)
        {
            return "Failed to connect $err $errstr";
        }
        else
        { */
		if(is_array($userdeviceToken) && count($userdeviceToken) > 0)
        {
			foreach($userdeviceToken as $key => $token_rec_id)
            {
                stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
                if($app_is_live == '1'){
					$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
                }
                else{
					$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
                }

                if (!$fp)
                {
			    //    return "Failed to connect $err $errstr";
                    continue;
                }
                else
                {
					
            //        $token_rec_id = '';
             //       $token_rec_id =  $value['IOS_TOKEN_UDID_ID'];
			        if($token_rec_id != '')
                    {
                        $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
                        fwrite($fp, $msg);
                    }
                }
                fclose($fp);
            }
        }
        else
        {
			stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);

            // assume the private key passphase was removed.
        //    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        //    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

            if($app_is_live == '1'){
				$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            }
            else{
				$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            }

            if (!$fp)
            {
                return false;
            //    return "Failed to connect $err $errstr";
            }
            else
            {
                $token_rec_id =  $userdeviceToken;
                if($token_rec_id != '')
                {
                    $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
                    fwrite($fp, $msg);
                }
            }
            fclose($fp);
        }
        return true;
        // END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
    }
    
    function codeToMessage($code)
    {
        switch ($code) {
            case UPLONC_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini (Allow max file upload size :  ".ini_get('upload_max_filesize').")";
                break;
            case UPLONC_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLONC_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLONC_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLONC_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLONC_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLONC_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    function mb_basename($file)
    {
        return end(explode('/',$file));
    }
    
	
	// Function for generate random string for access token generate
	function str_rand_access_token($length = 32, $seeds = 'allalphanum')
	{
		// Possible seeds
		$seedings['alpha'] 					= 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] 				= '0123456789';
		$seedings['alphanum'] 				= 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['allalphanum'] 			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['upperalphanum'] 			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$seedings['alphanumspec'] 			= 'abcdefghijklmnopqrstuvwqyz0123456789!@#$%^*-_=+';
		$seedings['alphacapitalnumspec'] 	= 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@!*-_';
		$seedings['hexidec'] 				= '0123456789abcdef';
		$seedings['customupperalphanum'] 	= 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; //Confusing chars like 0,O,1,I not included
		
		// Choose seed
		if (isset($seedings[$seeds])){
			$seeds 			= $seedings[$seeds];
		}
		
		// Seed generator
		list($usec, $sec) 	= explode(' ', microtime());
		$seed 				= (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		// Generate
		$str 			= '';
		$seeds_count 	= strlen($seeds);
		
		for ($i = 0; $length > $i; $i++){
			$str 		.= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $str;
	}
    
	function getCountryList()
	{
		$where			= array('CNT_STATUS'	=> 1);
		$order_by		= array('CNT_NAME'	=> "ASC");
		$countryData	= $this->get_all_data("*", TBL_ADMINCOUNTRIES, $where, array(), $order_by);
		return $countryData;
	}
	
	function getLanguageList()
	{
		$where			= array('LANG_STATUS'	=> 1);
		$langData		= $this->get_all_data("*", TBL_ADMINLANGUAGES, $where);
		return $langData;
	}
	
	function getAllLanguageList()
	{
		$where			= array('LANG_STATUS !='	=> 9);
		$langData		= $this->get_all_data("*", TBL_ADMINLANGUAGES, $where);
		return $langData;
	}
	
	function getTimezoneList()
	{
		$where			= array('TZ_STATUS'	=> 1);
		$timezoneData	= $this->get_all_data("*", TBL_ADMINTIMEZONES, $where);
		return $timezoneData;
	}
	
	function getCurrencyList()
	{
		$where			= array('CUR_STATUS'	=> 1);
		$order_by		= array('CUR_NAME'	=> "ASC");
		$currencyData	= $this->get_all_data("*", TBL_ADMINCURRENCIES, $where, array(), $order_by);
		return $currencyData;
	}
	
	function getCurrencyById($id)
	{
		$where			= array('CUR_ID'	=> $id);
		$currencyData	= $this->get_single_data("*", TBL_ADMINCURRENCIES, $where);
		return $currencyData;
	}
	
	function escape_data($data) 
    {
		return $this->db->escape($data);
    }
	
	function query($query) 
    {
		$this->db->query($query);
    }
	
	function affetcteRows() 
    {
		return $this->db->affected_rows();
    }

	function getPermissions($role){
		$where				= array(
								'ROLE_ID'	=> $role,
							);
		$permissionData		= $this->get_single_data("*", TBL_ADMINROLES, $where);
		if (is_array($permissionData) && count($permissionData) > 0 && !empty($permissionData['ROLE_PERMISSIONS'])){
			return $permissionData['ROLE_PERMISSIONS'];
		}else{
			return false;
		}
	}
	
	public function getChildIds($id)
	{
		$getChildIds	= "SELECT GROUP_CONCAT(U_ID) as childs
								FROM ".TBL_ADMIN."
								WHERE FIND_IN_SET('".$id."', U_PARENT_ID)
								AND U_STATUS!=9";
		$usersArr		= $this->get_single_data_query($getChildIds);
		return rtrim($usersArr['childs'],",");
	}

	public function getParentIds($id)
	{
		$getParentIds	= "SELECT  GROUP_CONCAT(
									@id :=(
											SELECT  U_PARENT_ID
											FROM    ".TBL_ADMIN."
											WHERE   U_ID = @id
										)
									) AS parents
								FROM    (
										SELECT  @id := '".$id."'
									) vars
								STRAIGHT_JOIN
									".TBL_ADMIN."
								WHERE   U_PARENT_ID IS NOT NULL";
		
		$usersArr		= $this->get_single_data_query($getParentIds);
		return rtrim($usersArr['parents'],",");
	}
}

?>