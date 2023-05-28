<?php
session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_type'] == 1)
				return 1;
			else
				return 2;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_files(){
		extract($_POST);
		if(empty($id)){
		if($_FILES['upload']['tmp_name'] != ''){
					$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['upload']['name'];
					$move = move_uploaded_file($_FILES['upload']['tmp_name'],'assets/uploads/'. $fname);
		
					if($move){
						$file = $_FILES['upload']['name'];
						$file = explode('.',$file);
						$chk = $this->db->query("SELECT * FROM files where SUBSTRING_INDEX(name,' ||',1) = '".$file[0]."' and folder_id = '".$folder_id."' and file_type='".$file[1]."' ");
						if($chk->num_rows > 0){
							$file[0] = $file[0] .' ||'.($chk->num_rows);
						}
						$data = " name = '".$file[0]."' ";
						$data .= ", folder_id = '".$folder_id."' ";
						$data .= ", description = '".$description."' ";
						$data .= ", user_id = '".$_SESSION['login_id']."' ";
						$data .= ", file_type = '".$file[1]."' ";
						$data .= ", file_path = '".$fname."' ";
						if(isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
						else
						$data .= ", is_public = 0 ";

						$save = $this->db->query("INSERT INTO files set ".$data);
						if($save)
						return json_encode(array('status'=>1));
		
					}
		
				}
			}else{
						$data = " description = '".$description."' ";
						if(isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
						else
						$data .= ", is_public = 0 ";
						$save = $this->db->query("UPDATE files set ".$data. " where id=".$id);
						if($save)
						return json_encode(array('status'=>1));
			}

	}
	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}

	function save_category(){
		extract($_POST);
		$data = " category = '$category' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO category_list set ".$data);
			if($save)
				return 1;
		}else{
			$save = $this->db->query("UPDATE category_list set ".$data." where id =".$id);
			if($save)
				return 2;
		}
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM category_list where id=".$id);
		if($delete)
			return 1;
	}
	function save_voting(){
		extract($_POST);
		$data = " title = '$title' ";
		$data .= " , description = '$description' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO voting_list set ".$data);
			if($save)
				return 1;
		}else{
			$save = $this->db->query("UPDATE voting_list set ".$data." where id =".$id);
			if($save)
				return 2;
		}
	}

	function get_voting(){
		extract($_POST);
		$get = $this->db->query("SELECT * FROM voting_list where id=".$id);
		if($get->num_rows > 0){
			return json_encode($get->fetch_assoc());
		}
	}
	function delete_voting(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM voting_list where id=".$id);
		if($delete)
			return 1;
	}

	function update_voting(){
		extract($_POST);
		$this->db->query("UPDATE voting_list set is_default = 0 where id !=".$id);
		$update = $this->db->query("UPDATE voting_list set is_default = 1 where id= ".$id);
		if($update)
			return 1;
	}
	function save_opt(){
		extract($_POST);
		$data = " category_id = '".$category_id."' ";
		$data .= ", opt_txt = '".$opt_txt."' ";
		$data .= ", voting_id = '".$voting_id."' ";

		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/img/'. $fname);
			$data .= ", image_path = '".$fname."' ";
			if(!empty($id)){

			$path = $this->db->query("SELECT * FROM voting_opt where id=".$id)->fetch_array()['image_path'];
			if(!empty($path))
			unlink('assets/img/'.$path);
			}

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO voting_opt set ".$data);
			if($save)
				return 1;
		}else{
			$save = $this->db->query("UPDATE voting_opt set ".$data." where id=".$id);
			if($save)
				return 2;
		}
	}
	function delete_candidate(){
		extract($_POST);
		$path = $this->db->query("SELECT * FROM voting_opt where id=".$id)->fetch_array()['image_path'];
		$delete = $this->db->query("DELETE FROM voting_opt where id=".$id);
		if($delete){
			unlink('assets/img/'.$path);
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " category_id = $category_id ";
		$data .= ", voting_id = $voting_id ";
		$data .= ", max_selection = $max_selection ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO voting_cat_settings set ".$data);
		}else{
			$save = $this->db->query("UPDATE voting_cat_settings set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function submit_vote(){
		extract($_POST);
		$set = 0;
		foreach($opt_id as $k => $val){
			foreach ($val as $key => $v) {

			$data = " voting_id = $voting_id ";
			$data .= ", category_id = $k ";
			$data .= ", user_id = '".$_SESSION['login_id']."' ";
			$data .= ", voting_opt_id = $v ";
			// echo $data.'<br>';
			$save[] = $this->db->query("INSERT INTO votes set ".$data);
			$set++;
			}
		}
		if(isset($save) && count($save) == $set)
			return 1;
	}
}