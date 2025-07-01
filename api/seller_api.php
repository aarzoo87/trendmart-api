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
		$qry = "INSERT INTO product (product_name, category_id, brand_name, product_price, discount_price, product_stock, description) VALUES ('$product_name', '$product_category', '$product_brand', '$product_price', '$dis_price', '$product_stock', '$descriotion')";
		$product_id = $this->db_conn->query_get_id($qry);
		$return_data = array();
		$return_status = 0;
		$return_msg = '';
		if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
	        $upload_dir = dirname(__DIR__) . '/uploads/';
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
				'id'     => $row['id'],
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
		$product_id = $this->glob['product_id'];
		if($product_id > 0){
			$this->db_conn->query("DELETE FROM product WHERE id = ".$product_id."");
			$return_data['status'] = 1;
			$return_data['message'] = 'Product Deleted Successfully.';
			return $return_data;
		}
	}
}
?>