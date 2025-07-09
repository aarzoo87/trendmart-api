<?php
/**
 * 
 */
class Login_API extends Main_API
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function get_login_details()
	{
		$login_email = $this->glob['email'];
		$login_role = $this->glob['role'];
		$login_pass = $this->glob['password'];
		$return_data = array();
		$return_status = 0;
		$return_msg = $return_value = "";
		$login_email_count = $this->db_conn->field("SELECT count(*) FROM users WHERE email = '".$login_email."' AND role = '".$login_role."'");
		if($login_email_count == 0){
			$return_msg = "Invalid email or role. Please check and try again.";
		}
		if($login_email_count > 0){
			$hash_password = $this->db_conn->row("SELECT id,password FROM users WHERE email = '".$login_email."' AND role = '".$login_role."'");
			if(password_verify($login_pass, $hash_password['password'])){
				$return_status = 1;
				$return_msg = "Welcome! You have logged in successfully.";
				$return_value = encrypt_decrypt($hash_password['id'], 'e');
			}else{
				$return_msg = "The password you entered is incorrect.";
			}
		}
		$return_data['status'] = $return_status;
		$return_data['message'] = $return_msg;
		$return_data['data'] = $return_value;
		return $return_data;
	}
	public function register_user()
	{
		$email_users_count = $this->db_conn->field("SELECT count(*) FROM users WHERE email = '".$this->glob['email']."'");
		$phone_users_count = $this->db_conn->field("SELECT count(*) FROM users WHERE phone = '".$this->glob['mobile']."'");
		$return_msg = '';
		$return_status = 0;
		$return_data = array();
		if($email_users_count > 0){
			$return_msg = "User with this email is already exists.";
		}
		if($phone_users_count > 0){
			$return_msg = "User with this mobile no is already exists.";
		}
		if($email_users_count == 0 && $phone_users_count == 0){
			$mysqli = $this->db_conn->conn;
			$gender = $this->glob['gender'] == '1' ? 'Male' : ($this->glob['gender'] == '2' ? 'Female' : 'Other');
			$first_name = mysqli_real_escape_string($mysqli, $this->glob['first_name']);
			$last_name  = mysqli_real_escape_string($mysqli, $this->glob['last_name']);
			$email      = mysqli_real_escape_string($mysqli, $this->glob['email']);
			$password   = mysqli_real_escape_string($mysqli, $this->glob['password']);
			$role       = mysqli_real_escape_string($mysqli, $this->glob['role']);
			$phone      = mysqli_real_escape_string($mysqli, $this->glob['mobile']);
			$dob        = mysqli_real_escape_string($mysqli, $this->glob['dob']);
			$_gender     = mysqli_real_escape_string($mysqli, $gender);
			$_password = password_hash($password, PASSWORD_DEFAULT);
			$qry = "INSERT INTO users (first_name, last_name, email, password, role, phone, dob, gender) VALUES ('$first_name', '$last_name', '$email', '$_password', '$role', '$phone', '$dob', '$_gender')";
			$new_added_user = $this->db_conn->query($qry);
			if($new_added_user > 0){
				$return_status = 1;
				$return_msg = 'Account with '.$email.' is successfully registered.';
			}
		}
		$return_data['status'] = $return_status;
		$return_data['message'] = $return_msg;
		return $return_data;
	}
}
?>