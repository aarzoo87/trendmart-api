<?php
/**
 * 
 */
class Seller_API extends Main_API
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function get_category_details()
	{
		$category_data = $formatted_category_data = [];
		$parent_categories = $this->db_conn->query("SELECT id, name FROM category WHERE parent_id = 0");

		while ($parent = $parent_categories->fetch_assoc()) {
		    $category_id = $parent['id'];
		    $category_data[$category_id] = [
		        'name' => $parent['name'],
		        'subcategories' => []
		    ];

		    $sub_category_details = $this->db_conn->query("SELECT id, name FROM category WHERE parent_id = " . $category_id);
		    while ($row = $sub_category_details->fetch_assoc()) {
		        $category_data[$category_id]['subcategories'][$row['id']] = $row['name'];
		    }
		}
		foreach ($category_data as $category_id => $category) {
			$group = [
				"group" => $category['name'],
				"items" => []
			];
			foreach ($category_data[$category_id]['subcategories'] as $key => $value) {
				$group['items'][] = [
					'label' => $value,
					'value' => (string)$key
				];
			}
			$formatted_category_data[] = $group;
		}
		$return_data['status'] = 1;
		$return_data['data'] = $formatted_category_data;
		return $return_data;
	}
	public function add_product()
	{
		$mysqli = $this->db_conn->conn;
		$product_name = mysqli_real_escape_string($mysqli, $this->glob['product_name']);
		$product_brand = mysqli_real_escape_string($mysqli, $this->glob['product_brand']);
		$descriotion = mysqli_real_escape_string($mysqli, $this->glob['product_desc']);
		$product_category = $this->glob['product_category'];
		$product_price = $this->glob['product_price'];
		$dis_price = $this->glob['product_discount_price'];
		$product_stock = $this->glob['product_stock'];
		$product_id = isset($this->glob['product_id']) ? encrypt_decrypt($this->glob['product_id'],'d') : 0;
		if($product_id == 0){
			$qry = "INSERT INTO product (product_name, category_id, brand_name, product_price, discount_price, product_stock, description) VALUES ('$product_name', '$product_category', '$product_brand', '$product_price', '$dis_price', '$product_stock', '$descriotion')";
			$product_id = $this->db_conn->query_get_id($qry);
		}else{
			$qry = "UPDATE product SET 
			            product_name = '$product_name', 
			            category_id = '$product_category', 
			            brand_name = '$product_brand', 
			            product_price = '$product_price', 
			            discount_price = '$dis_price', 
			            product_stock = '$product_stock', 
			            description = '$descriotion',
			            updated_at = date('Y-m-d H:i:s')
			        WHERE id = '$product_id'";
			$this->db_conn->query($qry);
		}
		$return_data = array();
		$return_status = 0;
		$return_msg = '';
		if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
	        $upload_dir = dirname(__DIR__) . '/uploads/';
	        if (!is_dir($upload_dir)) {
	            mkdir($upload_dir, 0755, true);
	        }
	        $fileTmpPath = $_FILES['product_image']['tmp_name'];
	        $fileName = $_FILES['product_image']['name'];
	        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
	        $newFileName = 'product_'. $product_id . '.' . $fileExt;
	        $destPath = $upload_dir . $newFileName;

	        if (move_uploaded_file($fileTmpPath, $destPath)) {
	        	$this->db_conn->query("UPDATE product SET product_image = '".$newFileName."' WHERE id = ".$product_id."");
	        	$return_status = 1;
	        	$return_msg = 'Product Added Sucessfully.';
	        } else {
	        	$return_status = 0;
	        	$return_msg = 'Product Added Sucessfully but Product Image not saved.';
	        }
	    } else {
	        $return_status = 0;
	        $return_msg = 'No Product Image found.';
	    }
	    $return_data['status'] = $return_status;
	    $return_data['message'] = $return_msg;
	    return $return_data;
	}
	public function get_product_details()
	{
		global $env_server_url;
		$product_data = array();
		$qry = "SELECT product.*, category.name as category_name FROM product LEFT JOIN category on (product.category_id = category.id)";
		$products_list = $this->db_conn->query($qry);
		while ($row = $products_list->fetch_assoc()) {
			$product_data[] = array(
				'id'     => encrypt_decrypt($row['id'],'e'),
				'product_name'     => $row['product_name'],
				'brand_name'       => $row['brand_name'],
				'category_id'      => $row['category_id'],
				'category_name'    => $row['category_name'],
				'product_price'    => $row['product_price'],
				'discount_price'   => $row['discount_price'],
				'product_stock'    => $row['product_stock'],
				'description'    => $row['description'],
				'product_image'    => $env_server_url . "/uploads/" . $row['product_image'],
			);
		}
		$return_data['status'] = 1;
		$return_data['data'] = $product_data;
		return $return_data;
	}
	public function delete_product()
	{
		$product_id = encrypt_decrypt($this->glob['product_id'],'d');
		if($product_id > 0){
			$this->db_conn->query("DELETE FROM product WHERE id = ".$product_id."");
			$return_data['status'] = 1;
			$return_data['message'] = 'Product Deleted Successfully.';
			return $return_data;
		}
	}
	public function get_single_product_detail()
	{
		global $env_server_url;
		$product_id = encrypt_decrypt($this->glob['product_id'],'d');
		if($product_id > 0){
			$row = $this->db_conn->row("SELECT product.*, category.name as category_name FROM product LEFT JOIN category on (product.category_id = category.id) WHERE product.id = ".$product_id."");
			$product_data = $return_data = array();
			$product_data['id'] = encrypt_decrypt($row['id'],'e');
			$product_data['product_name'] = $row['product_name'];
			$product_data['brand_name'] = $row['brand_name'];
			$product_data['category_id'] = $row['category_id'];
			$product_data['category_name'] = $row['category_name'];
			$product_data['product_price'] = $row['product_price'];
			$product_data['discount_price'] = $row['discount_price'];
			$product_data['product_stock'] = $row['product_stock'];
			$product_data['description'] = $row['description'];
			$product_data['product_image'] = $env_server_url . "/uploads/" . $row['product_image'];

			$return_data['status'] = 1;
			$return_data['message'] = 'Product Deleted Successfully.';
			$return_data['data'] = $product_data;
			return $return_data;
		}
	}
	public function add_to_cart()
	{
		$product_id = isset($this->glob['product_id']) ? encrypt_decrypt($this->glob['product_id'],'d') : 0;
		$customer_user_id = isset($this->glob['customer_id']) ? encrypt_decrypt($this->glob['customer_id'], 'd') : 0;
		$return_data = array();
		$return_status = 0;
		$return_msg = "";
		if($product_id > 0 && $customer_user_id > 0){
			$qty = isset($this->glob['product_qty']) ? $this->glob['product_qty'] : 1;
			$seleced_product = isset($this->glob['selected']) ? $this->glob['selected'] : true;
			$is_Selected = $seleced_product ? 1 : 0;
			$existing_cart_id = $this->db_conn->field("SELECT id FROM cart WHERE product_id = ".$product_id." AND customer_id = ".$customer_user_id."");
			if ($existing_cart_id > 0) {
				$this->db_conn->query("UPDATE cart SET qty = ".$qty." WHERE id = ".$existing_cart_id."");
				$return_status = 1;
				$return_msg = "Product updated in the cart successfully.";
			}else{
				$cart_qry = "INSERT INTO cart (product_id, customer_id, qty, isSelected) VALUES ('$product_id', '$customer_user_id', '$qty', '$is_Selected')";
				$cart_id = $this->db_conn->query_get_id($cart_qry);
				if($cart_id > 0){
					$return_status = 1;
					$return_msg = "Product added to the cart successfully.";
				}else{
					$return_msg = "Failed to add product to cart.";
				}
			}
		}else{
			$return_msg = "Customer Not Found.";
		}
		$return_data['status'] = $return_status;
		$return_data['message'] = $return_msg;
		return $return_data;
	}
	public function get_cart_product_details()
	{
		global $env_server_url;
		$customer_user_id = isset($this->glob['customer_id']) ? encrypt_decrypt($this->glob['customer_id'], 'd') : 0;
		$product_details = $this->db_conn->query("SELECT product.product_name,product.brand_name,product.product_price,product.discount_price,product.product_stock,product.description,product.product_image,cart.product_id,cart.qty,cart.size,cart.color,category.name as category_name FROM cart LEFT JOIN product ON (cart.product_id = product.id) LEFT JOIN category ON (product.category_id = category.id) WHERE cart.customer_id = ".$customer_user_id."");
		while ($row = $product_details->fetch_assoc()) {
			$product_data[] = array(
				'id'     => encrypt_decrypt($row['product_id'],'e'),
				'product_name'     => $row['product_name'],
				'brand_name'       => $row['brand_name'],
				'category_name'    => $row['category_name'],
				'product_price'    => $row['product_price'],
				'discount_price'   => $row['discount_price'],
				'product_stock'    => $row['product_stock'],
				'description'    => $row['description'],
				'product_qty'    => $row['qty'],
				'product_size'    => $row['size'],
				'product_color'    => $row['color'],
				'product_image'    => $env_server_url . "/uploads/" . $row['product_image'],
			);
		}
		$return_data['status'] = 1;
		$return_data['data']['products'] = $product_data;
		$return_data['data']['total_items'] = count($product_data);
		return $return_data;
	}
}
?>