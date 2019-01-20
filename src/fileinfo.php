<?php
namespace dsda\fileinfo;

class fileinfo {
	
	public $result = false;
	private $input_files = [];
		
	function __construct(array $files=[], array $data=[]) {
		
		//$this->catcher = new \dsda\catcher\catcher();
		
		// in - array of files
		//      video, audio
		//      video
		//      image file
		
		// data - post data with trim, blur, task_type, tags, source, description, location
		
		// out - true if file correct and task accepted
		//       error message if file not correct or something wrong
		

		// check exist files
		foreach($files as $key=>$file) {
			if (!file_exists($file)) {
				throw new \Exception("FilesManipulator: Input file - ".$file." not found!", 0);
			}
			if (!is_readable($file)) {
				throw new \Exception("FilesManipulator: Input file - ".$file." not readable!", 0);
			}
		}
		
		// check media files
		foreach($files as $key=>$file) {
			$media_type = $this->get_media_type($file);
			if ($media_type==false) {
				throw new \Exception("FilesManipulator: file - ".$file." is unknown type!", 0);
			}
			$this->input_files[] = ['path'=>$file, 'type'=>$media_type];
			unset($media_type);
		}
		
		// generate filename ($this->output_file)
		$this->set_output_name();
		
		// add post to db
		//XXX: тут закончил
		$config = new \dsda\config\config();
		$db = new \dsda\dbconnector\dbconnector($config);
		
		$post_id = $db->query("INSERT INTO `post` VALUES (NULL, )");
		
		$this->result = $post_id;
		
		// create task for files with post_id
	}
	
	
	private function set_output_name(){
		if ($this->input_files[0]['type']=='image') {
			$ext = 'jpg';
		} else {
			$ext = 'mp4';
		}
		$this->output_file = explode(" ",microtime(false))[1].str_replace("0.","",explode(" ",microtime(false))[0]).'.'.$ext;
	}
	
	private function get_media_type($file_path){
		$container = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
		$name = strtolower(pathinfo($file_path, PATHINFO_FILENAME));
		
		$types = [];
		$types['video'] = ['mov','mp4','avi','wmv','mpeg','m4v','flv', 'webm', 'mkv', 'vob', 'mts', 'm2ts', 'ps', 'ts', 'm2p', 'mpg', 'm2v', '3gp'];
		$types['audio'] = ['aac', 'aiff', 'm4a', 'm2a', 'mp3', 'ogg', 'wav', 'wma', 'webm'];
		$types['image'] = ['jpg', 'jpeg', 'tiff', 'gif', 'bmp', 'tga', 'png', 'webp'];
		
		foreach($types as $type_key=>$type_extensions) {
			if (in_array($container, $type_extensions)) {
				return $type_key;
			}
		}
		
		return false;
		
	}
	
}
