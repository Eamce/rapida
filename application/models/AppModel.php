<?php
defined('BASEPATH') or exit('No direct script access allowed');

include 'vendor/autoload.php';

class AppModel extends CI_Model
{

	// Private $tenatImage   = 'http://172.16.43.112:7000/';
	// Private $buImage 	  = 'http://172.16.43.239:7000/';
	// Private $productImage =	'http://172.16.43.134:8000/storage/';
	// Private $gcproductImage = 'http://172.16.161.41:8001/ITEM-IMAGES/';


	// Private $tenatImage   = 'https://apanel.alturush.com/';
	private $buImage 	  = 'https://apanel.alturush.com/';
	private $productImage =	'https://storetenant.alturush.com/storage/';
	private $gcproductImage = 'https://admins.alturush.com/ITEM-IMAGES/';
	private $cssadmin = 'https://customerservice.alturush.com/';

	private function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public function appCreateAccountMod($townId, $barrioId, $username, $firstName, $lastName, $suffix, $password, $birthday, $contactNumber)
	{
		$data = array(
			'firstname' => $firstName,
			'lastname' =>  $lastName,
			'birthdate' => $birthday,
			'suffix' => $suffix,
			'status' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('toms_customer_details', $data);
		$insert_id	=  $this->db->insert_id();

		$cus_add = array(

			'customer_id' => $insert_id,
			'firstname' => $firstName,
			'lastname' => $lastName,
			'mobile_number' => $contactNumber,
			'barangay_id' => $barrioId,
			'shipping' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_addresses', $cus_add);

		$cust_num = array(
			'customer_id' => $insert_id,
			'mobile_number' => $contactNumber,
			'in_use' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_numbers', $cust_num);

		$data1 = array(
			'customer_id' => $insert_id,
			'firstname' => $firstName,
			'lastname' => $firstName,
			'username' => $username,
			'password' => $this->hash_password($password),
			'password2' => md5($password),
			'user_from' => '2',
			'mobile_number' => $contactNumber,
			'brgy_id' => $barrioId,
			'status' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('app_users', $data1);
	}

	public function signInUserMod($usr, $password)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.username', $usr);
		// $this->db->where('appsu.password2',md5($password));
		// $this->db->limit(1);
		$query = $this->db->get();
		$res1 = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		// $this->db->where('appsu.username',$usr);
		$this->db->where('appsu.password2', md5($password));
		// $this->db->limit(1);
		$query = $this->db->get();
		$res2 = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.username', $usr);
		$this->db->where('appsu.password2', md5($password));
		// $this->db->limit(1);
		$query = $this->db->get();
		$res3 = $query->row_array();
		if ($res1['status'] == '0') {
			echo "accountblocked";
		} else {
			if (empty($res1['username'])) {
				// echo 'Username not found';
				$trap = $this->forTrap($res1['id'], $res2['password']);

				if (!empty($trap)) {
					// $trap2 = $this->forTrap2($res1['id'], $res1['password'])
					echo $res3['customer_id'];
				} else {
					echo "wrongusername";
				}
				// if(!empty($res2['password2']))
				// {
				// 	echo 'wrongusername';
				// }
				// if(empty($res2))
				// {
				// 	echo "false";
				// }
			} else if (empty($res2['password2'])) {
				echo "wrongpass";
			} else if (!empty($res3)) {
				echo $res3['customer_id'];
			} else if (empty($res3)) {
				echo "wrongpass";
			}
		}
	}

	public function forTrap($id, $password)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.id', $id);
		$this->db->where('appsu.password2', md5($password));
		// $this->db->limit(1);
		$query = $this->db->get();
		$ress = $query->row_array();

		return $ress;
	}



	public function getUserDataMod($id)
	{
		$this->db->select('*', 'appsu.firstname', 'appsu.lastname');
		$this->db->from('app_users as appsu');
		$this->db->join('toms_customer_details as cus_det', 'cus_det.id = appsu.customer_id', 'left');
		$this->db->join('barangays as brgy', 'brgy.brgy_id = appsu.brgy_id', 'left');
		$this->db->where('appsu.customer_id', $id);
		$query = $this->db->get();
		$res = $query->result_array();
		// echo $res;
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_customerId' => $value['customer_id'],
				'd_firstname' => $value['firstname'],
				'd_lastname' => $value['lastname'],
				'd_contact' => $value['mobile_number'],
				'd_suffix' => $value['suffix'],
				'd_userNameUs' => $value['username'],
				'd_townId' => $value['town_id'],
				'd_brgId' => $value['brgy_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPlaceOrderDataMod($cusId)
	{

		$this->db->select('*,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
		$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.brgy_id = cust_add.barangay_id', 'left');
		$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
		$this->db->where('cust_add.customer_id', $cusId);
		$this->db->where('cust_add.shipping', '1');
		$this->db->where('tblcharges.vtype', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		if (count($res) == 0) {
			$this->db->select('*,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname');
			$this->db->from('customer_addresses as cust_add');
			$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
			$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
			$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
			$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
			$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.town_id = twn.town_id', 'left');
			$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
			$this->db->where('cust_add.customer_id', $cusId);
			// $this->db->where('cust_add.shipping', '1');
			$this->db->where('tblcharges.vtype', '1');
			$query2 = $this->db->get();
			$res2 = $query2->result_array();
			$post_data = array();
			foreach ($res2 as $value) {
				$post_data[] = array(
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
			// echo "heelo";
		} else {
			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function checkAllowedPlaceMod($townId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tblcharges');
		$this->db->where('tblcharges.town_id', $townId);
		$this->db->limit(1);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkFeeMod($townId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tbldeliveryCh');
		$this->db->where('tbldeliveryCh.town_id', $townId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		if (!empty($res)) {
			foreach ($res as $value) {
				$post_data[] = array(
					'd_charge_amt' => $value['charge_amt']
				);
			}
		} else {
			$post_data[] = array(
				'd_charge_amt' => 0
			);
		}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getOrderDataMod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->join('fd_products as fbprod', 'fbprod.product_id = appcart.product_id', 'inner');
		$this->db->where('appcart.customerId', $cusId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'd_prod' => $value['product_name'],
				'd_price' => $value['price'],
				'd_qty' => $value['quantity'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getSubtotalMod($cusId)
	{
		// error_reporting(0);
		$all_total = 0;
		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		// $this->db->where("product_id", $prod_data->productId);
		$this->db->where("cart.customerId", $cusId);
		// $this->db->group_by("tenantId");

		$result2 = $this->db->get();

		$prods = $result2->result();


		foreach ($prods as $value) {

			// endif;
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result4 = $this->db->get();

			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);


			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);

			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);
			$result7 = $this->db->get();
			$tenant = $result7->row();

			$total[] =  $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1);
		}

		for ($i = 0; $i < count($total); $i++) {
			$all_total += $total[$i];
		}
		$item = array(
			'user_details' => array(
				[
					'd_subtotal' =>	$all_total
				],
			),
		);
		echo json_encode($item);
	}


	// public function getLastOrderId_mod($cusId){
	// 		// $this->db->select('*');
	// 		// $this->db->from('toms_customer_orders as toms_order');
	// 		// $this->db->limit(1);
	// 		// $this->db->order_by('id',"desc");
	// 		// $this->db->where('toms_order.customer_id', $cusId);
	// 		// // $this->db->where('toms_order.order_from', 'mobile_app');
	// 		// $query = $this->db->get();
	//   //      	$res = $query->result_array();
	//   //      	$post_data = array();
	// 	 // 	foreach($res as $value){
	// 	 // 			$post_data[] = array(
	// 	 // 				'd_ticket_id' => $value['ticket_id'],
	// 	 // 			);	
	// 		// }
	// 		// $item = array('user_details' => $post_data);
	// 		// echo json_encode($item);

	// 		$this->db->select('*');
	// 		$this->db->from('tickets as ticket');
	// 		$this->db->limit(1);
	// 		$this->db->order_by('id',"desc");
	// 		$this->db->where('ticket.customer_id', $cusId);
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	// 	 	foreach($res as $value){
	// 	 			$post_data[] = array(
	// 	 				'd_ticket_id' => $value['ticket'],
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function getLastItems_mod($orderNo)
	{
		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_order');
		$this->db->join('fd_products as fbprod', 'fbprod.product_id = toms_order.product_id', 'inner');

		$this->db->join('locate_tenants as locateTenant', 'locateTenant.tenant_id = fbprod.tenant_id', 'inner');

		$this->db->where('toms_order.ticket_id', $orderNo);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_tenantId' => $value['tenant_id'],
				'd_tenantName' => $value['tenant'],
				'd_items' => $value['product_name'],
				'd_price' => $value['price'],
				'd_totalprice' => $value['total_price'],
				'd_qty' => $value['quantity']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getAllowedLoc_mod()
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as dlvcharg');
		$this->db->join('towns as twn', 'twn.town_id = dlvcharg.town_id', 'inner');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_towd_id' => $value['town_id'],
				'd_town' => $value['town_name'],
				'd_charge_amt' => $value['charge_amt'],
				// 'd_amount_limit' => $value['customer_to_pay']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBu_mod($cusId)
	{
		$this->db->select('*,fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->group_by('locBu.bunit_code');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBu_mod1($cusId)
	{
		$this->db->select('*,fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		// $this->db->group_by('locBu.bunit_code');
		$this->db->group_by('fdprod.tenant_id');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTenant_mod($cusId)
	{
		// $this->db->select('DISTINCT(products.tenant_id), tenant, locbu.bunit_code,business_unit');
		// $this->db->from('app_customer_temp_orders as temp_orders');
		// $this->db->join('fd_products as products', 'products.product_id = temp_orders.product_id', 'inner');
		// $this->db->join('locate_tenants as tenants', 'tenants.tenant_id = products.tenant_id', 'inner');
		// $this->db->join('locate_business_units as locbu', 'locbu.bunit_code = tenants.bunit_code', 'inner');
		// $this->db->where('customerId', $cusId);
		// $tenants = $this->db->get()->result();
		// $tenant = [];
		// foreach ($tenants as $tenant) {
		// 	$this->db->select('*');
		// 	$this->db->from('app_customer_temp_orders as temp_orders');
		// 	$this->db->join('fd_products as products', 'products.product_id = temp_orders.product_id', 'inner');
		// 	$this->db->where('tenant_id', $tenant->tenant_id);
		// 	$temp_orders = $this->db->get()->result();
		// 	$tenant_total_order = 0;

		// 	foreach ($temp_orders as $temp_order) {
		// 		$tenant_total_order += $temp_order->total_price;
		// 	}

		// 	$tenant_summary[] = [
		// 		"tenant_id" => $tenant->tenant_id,
		// 		"tenant_name" => $tenant->tenant,
		// 		"bu_id" => $tenant->bunit_code,
		// 		"bu_name" => $tenant->business_unit,
		// 		"total" => $tenant_total_order
		// 	];
		// }
		// $item = array('user_details' => $tenant_summary);
		// echo json_encode($item);


		$this->db->select('*,sum(total_price) as sumpertenats');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		// $this->db->group_by('locBu.bunit_code');
		$this->db->group_by('fdprod.tenant_id');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'bu_id' => $value['bunit_code'],
				'bu_name' => $value['business_unit'],
				'tenant_name' => $value['tenant'],
				'total' => ceil($value['sumpertenats'])
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}



	public function getTicketNoFood_mod($cusId)
	{

		$query = $this->db->query("select * from tickets as toms_tickets
										where id NOT IN (select ticket_id from toms_tag_riders)
										and customer_id = '$cusId' and cancel_status != '1'");
		// $this->db->where('toms_tickets.cancel_status', '1');
		// $query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTicketNoFood_ontrans_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('tickets as toms_tickets');
		$this->db->join('toms_tag_riders as tag_riders', 'tag_riders.ticket_id = toms_tickets.id', 'left');
		// $this->db->where('tag_riders.trans_status','1');
		$this->db->where('tag_riders.delevered_status', '0');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('toms_tickets.id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTicketNoFood_delivered_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('tickets as toms_tickets');
		$this->db->join('toms_tag_riders as tag_riders', 'tag_riders.ticket_id = toms_tickets.id', 'left');
		// $this->db->where('tag_riders.trans_status','1');
		$this->db->where('tag_riders.delevered_status', '1');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('toms_tickets.id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getTicket_cancelled_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('tickets as toms_tickets');
		// $this->db->where('tag_riders.trans_status','1');
		$this->db->where('toms_tickets.cancel_status', '1');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('toms_tickets.id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	// public function getTicketNoGood_mod($cusId){
	// 		$this->db->select('*');
	// 		$this->db->from('tickets as toms_tickets');
	// 		$this->db->where('toms_tickets.customer_id', $cusId);
	// 		$this->db->where('toms_tickets.order_type_stat','1');
	// 		$this->db->order_by('id', 'desc');
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	//        	$status = '';
	// 	 	foreach($res as $value){

	// 	 			$post_data[] = array(
	// 	 					'd_ticket_id' => $value['ticket'],
	// 	 					'd_customerId' => $value['customer_id'],
	// 	 					'd_mop' => $value['mop']
	// 	 					// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function loadProfile_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('toms_customer_details as toms_det');
		$this->db->join('app_users as appsu', 'appsu.customer_id = toms_det.id');
		$this->db->where('toms_det.id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			if ($value['picture'] == null) {
				$picture  = "https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg";
			} else {
				$picture = $value['picture'];
			}
			$post_data[] = array(
				'd_fname' => $value['firstname'],
				'd_lname' => $value['lastname'],
				'd_photo' => $picture
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function lookItems_mod($ticketNo)
	{
		$this->db->select('*,tickets.id as ticketId,toms_order.ticket_id as tik_id,toms_order.id as toms_id,toms_order.quantity as quantity,fd_prod.image as prod_image,loc_bu.business_unit as loc_bu,loc_tenants.tenant as tenant_name,toms_order.total_price as total_price,toms_order.product_id as  product_id,fd_prod.product_id as prod_id,fd_prod.product_name as prod_name');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('tickets.ticket', $ticketNo);
		// $this->db->where('tickets.customer_id',$cusId);
		// $this->db->where('toms_order.canceled_status','0');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$toms_data = $this->checkTomsData("toms_tag_riders", $value['tik_id']);

			if (empty($toms_data)) :
				$val = "false";
			else :
				$val = "true";
			endif;

			$addons = $this->countAddons($value['toms_id'], 'toms_customer_order_addons', 'addon', 'addon_id');
			$choices = $this->countAddons($value['toms_id'], 'toms_customer_order_choices', 'choice', 'choice_id');

			// $choices = $this->countChoices($temp_order_id, 'app_customer_temp_order_choices', 'choices', 'choice_id');

			$post_data[] = array(
				'ticketId' => $value['ticketId'],
				'canceled_status' => $value['canceled_status'],
				'toms_id'		  => $value['toms_id'],
				'ticket' 		  => $value['ticket'],
				'product_id'      => $value['product_id'],
				'prod_name' 	  => $value['prod_name'],
				'total_price' 	  => $value['product_price'],
				'tenant_name' 	  => $value['tenant_name'],
				'tenant_id'		  => $value['tenant_id'],
				'bu_name' 		  => $value['loc_bu'],
				'bu_id'			  => $value['bunit_code'],
				'd_qty' 		  => $value['quantity'],
				'prod_image' 	  => $this->productImage . $value['prod_image'],
				'ifexists' 	 	  => $val,
				'addon_length' => count($addons) + count($choices),
				'add_ons' => $addons,
				'choices' => $choices
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	private function countAddons(int $foreignKey, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column", 'inner');
		$this->db->where('order_id', $foreignKey);

		return $this->db->get()->result();
	}

	public function lookItems_segregatemod($ticketNo)
	{
		$this->db->select('*,sum(total_price) as sumpertenats ,tickets.id as ticketId,toms_order.ticket_id as tik_id,toms_order.id as toms_id,toms_order.quantity as quantity,fd_prod.image as prod_image,loc_bu.business_unit as loc_bu,loc_tenants.tenant as tenant_name,toms_order.total_price as total_price,toms_order.product_id as  product_id,fd_prod.product_id as prod_id,fd_prod.product_name as prod_name');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('tickets.ticket', $ticketNo);
		$this->db->group_by('loc_tenants.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_name' 	  => $value['tenant_name'],
				'tenant_id'		  => $value['tenant_id'],
				'bu_name' 		  => $value['loc_bu'],
				'bu_id'			  => $value['bunit_code'],
				'sumpertenats'	  => ceil($value['sumpertenats'])
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function lookitems_good_mod($ticketNo)
	{
		$this->db->select('*,gc_final.id as gc_final_id,gc_final.ticket_id as tik_id,gc_final.ticket_id as ticket,gc_prod_items.product_name as prod_name,gc_prod_items.image as prod_image,gc_final.quantity as quantity');
		$this->db->from('tickets as tickets');
		// $this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id','inner');
		$this->db->join('gc_final_order as gc_final', 'gc_final.ticket_id = tickets.id', 'inner');
		// $this->db->join('fd_products as fd_prod','fd_prod.product_id = toms_order.product_id','inner');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.product_id = gc_final.product_id', 'inner');
		// $this->db->join('locate_tenants as loc_tenants','loc_tenants.tenant_id = fd_prod.tenant_id');
		// $this->db->join('locate_business_units as loc_bu','loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('tickets.ticket', $ticketNo);
		// $this->db->where('tickets.customer_id',$cusId);
		// $this->db->where('toms_order.canceled_status','0');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$toms_data = $this->checkTomsData("toms_tag_riders", $value['tik_id']);

			if (empty($toms_data)) :
				$val = "false";
			else :
				$val = "true";
			endif;

			$post_data[] = array(
				'toms_id'	  => $value['gc_final_id'],
				'canceled_status' => $value['canceled_status'],
				'gc_final_id'	  => $value['gc_final_id'],
				'ticketId' 		  => $value['ticket'],
				'product_id'      => $value['product_id'],
				'prod_name' 	  => $value['prod_name'],
				'total_price' 	  => $value['price'],
				'tenant_name' 	  => "",
				'bu_name' 		  => "",
				'd_qty' 		  => $value['quantity'],
				'prod_image' 	  => $this->gcproductImage . $value['prod_image'],
				'ifexists' 	 	  => $val
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function checkTomsData($table, $ticket_id)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("ticket_id", $ticket_id);

		$result = $this->db->get();

		return $result->row();
	}

	public function loadCartDataNew_mod($cusId)
	{
		$temp_orders = [];
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = temp_orders.product_id", 'inner');
		$this->db->where('customerId', $cusId);
		$temp_orders = $this->db->get()->result();
		$main_items = [];

		foreach ($temp_orders as $temp_order) {

			$details = [];
			$temp_order_id = $temp_order->id;

			$addons = $this->getTempOrderRelations($temp_order_id, 'app_customer_temp_order_addons', 'addon', 'addon_id');
			$choices = $this->getTempOrderRelations($temp_order_id, 'app_customer_temp_order_choices', 'choices', 'choice_id');
			$flavors = $this->getTempOrderFlavorRelations($temp_order_id, 'app_customer_temp_order_flavors', 'flavors');

			$temp_order->image = $this->productImage . $temp_order->image;

			$details['main_item'] = $temp_order;

			$details['flavors'] = $flavors;

			$add_ons = [];
			$choicess = [];

			foreach ($addons as $addon) {
				$addon->image = $this->productImage . $addon->image;
				$add_ons[] = $addon;
			}

			foreach ($choices as $choice) {
				$choice->image = $this->productImage . $choice->image;
				$choicess[] = $choice;
			}

			$details['addons'] = $add_ons;
			$details['choices'] = $choicess;


			$details['main_item']->addon_length = count($add_ons) + count($choicess);
			$main_items[] = $details;
		}
		echo json_encode(['user_details' => $main_items]);
	}

	private function getTempOrderRelations(int $temp_order_id, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column", 'inner');
		$this->db->where('temp_order_id', $temp_order_id);

		return $this->db->get()->result();
	}


	private function getTempOrderFlavorRelations($temp_order_id, $table, $alias)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_flavors as fd_flavor', "fd_flavor.id = $alias.id", 'inner');
		// $this->db->join('');
		$this->db->where('temp_order_id', $temp_order_id);

		return $this->db->get()->result();
	}

	public function loadCartData_mod($cusId)
	{
		$total = array();
		$this->db->select("cart.id,quantity ,cart.productId ,buId, prod.product_id, uom_id, buId, tenantId, cart.customerId, price as real_price");
		// fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom");
		// (SUM(price) as real_price");
		// (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
		// (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		// $this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		// echo json_encode($result2);
		// exit();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");




			$result4 = $this->db->get();

			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();

			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();

			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();

			$this->db->select("*");
			$this->db->from("fd_products");
			$this->db->where("product_id", $value->product_id);

			$result9 = $this->db->get();
			$prod_image = $result9->row();

			$total[] = array(
				"d_id"    	   => $value->id,
				'cart_qty'	   => $value->quantity,
				"tenant_id"   => $value->tenantId,
				"tenant_name" => $tenant->tenant,
				"bu_id"  	   => $value->buId,
				"bu_name"     => $bu->business_unit,
				"prod_image"  => $this->productImage . $prod_image->image,
				"prod_name"   => $prod_image->product_name,
				"prod_id"	   => $value->productId,
				"prod_uom"	   => $value->uom_id,
				"total"  	   =>  $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
				// + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
			);
		}
		$item = array('user_details' => $total);
		echo json_encode($item);
	}

	public function loadCartData_sides_mod($cusId)
	{
		$this->db->select('*,fd_prod_prices.uom_id as prod_uom,fd_prod_prices.price as prod_price');
		$this->db->from('app_cart_addons_side_items as appCart_addons');
		$this->db->join('app_cart_main as app_cart_main', 'app_cart_main.id = appCart_addons.cart_id', 'left');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = appCart_addons.side_id', 'left');
		$this->db->join('fd_product_prices as fd_prod_prices', 'fd_prod_prices.product_id = appCart_addons.side_id AND fd_prod_prices.uom_id = appCart_addons.side_uom', 'left');
		$this->db->where('app_cart_main.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'side_id' => $value['side_id'],
				'side_name' => $value['product_name'],
				'side_uom' => $value['prod_uom'],
				'prod_price' => $value['prod_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function removeItemFromCart_mod($cartId)
	{
		$this->db->where('app_cart_main.id', $cartId);
		$this->db->delete('app_cart_main');

		$this->db->where('app_cart_fries.cart_id', $cartId);
		$this->db->delete('app_cart_fries');

		$this->db->where('app_cart_addons_side_items.cart_id', $cartId);
		$this->db->delete('app_cart_addons_side_items');

		$this->db->where('app_cart_drink.cart_id', $cartId);
		$this->db->delete('app_cart_drink');

		$this->db->where('app_cart_sides.cart_id', $cartId);
		$this->db->delete('app_cart_sides');
	}

	public function displayOrder_mod($cusId, $tenantId)
	{
		$this->db->select('*,appCart.id as cart_id,fd_prod_price.price as prod_price,fd_prod.product_name as product_name,appCart.quantity as appqty');
		$this->db->from('app_cart_main as appCart');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = appCart.productId', 'inner');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = appCart.productId AND IFNULL(fd_prod_price.uom_id,0) = IFNULL(appCart.uom,0)', 'inner');

		$this->db->where('appCart.tenantId', $tenantId);
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id'    => $value['cart_id'],
				'prod_price' => $value['prod_price'],
				'd_prodName' => $value['product_name'],
				'd_quantity' => $value['appqty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function check_per_store_total($cusId, $credit_limit)
	{
		$ress = array();
		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		$this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result4 = $this->db->get();
			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();


			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();
			$total_price = $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1);
			if ($total_price < $credit_limit) {
				return 'true';
			}
		}
		return 'false';
	}


	public function trapTenantLimit_mod($townId, $customerId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges');
		$this->db->where('town_id', $townId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$credit_limit = $res[0]['customer_to_pay'] - $res[0]['charge_amt'];
		$store_price = $this->check_per_store_total($customerId, $credit_limit);


		// foreach ($store_price as $value) {
		// 	# code...

		// }
		// foreach($res as $value){
		// 	// if($value['subtotalPerTenant'] < 300){
		// 			if($grandtotal < $value['customer_to_pay'] - $value['charge_amt'])
		// 			{
		// 				$limit = 'true';
		// 			}else{
		// 				$limit = 'false';
		// 			}
		$post_data[] = array(
			'limit' => $store_price,
			'town_limit' => $credit_limit
			// 'customer_to_pay' => $res[0]['customer_to_pay'],
			// 'charge_amt' => $res[0]['charge_amt']
		);
		// 	}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTenant_perbu_mod($buId)
	{
		$this->db->select('*');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->where('loc_tenants.bunit_code', $buId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_tenant_id' =>  $value['tenant_id'],
				'd_bunit_code' =>  $value['bunit_code'],
				'd_tenant' =>  $value['tenant'],
				'd_logo' =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getAmountPertenant_mod($cusId)
	{

		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		$this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result4 = $this->db->get();
			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();


			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();
			$total[] = array(
				"tenant_id"   => $value->tenantId,
				"loc_tenant_name" => $tenant->tenant,
				"bu_id"  	   => $value->buId,
				"loc_bu_name"     => $bu->business_unit,
				"total_price"  	   => $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
			);
		}
		$item = array('user_details' => $total);
		echo json_encode($item);
	}

	//node

	public function display_store_mod($unitGroupId, $globalCatID)
	{
		$this->db->select('*,loc_bu.logo as bu_logo');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code', 'left');
		$this->db->where('loc_bu.active', '1');
		$this->db->where('loc_bu.group_id', $unitGroupId);
		$this->db->where('loc_tenants.global_cat_id', $globalCatID);
		// $this->db->join('locate_tenants as loc_tenants', 'loc_tenants.bunit_code = loc_bu.bunit_code','inner');
		$this->db->group_by('loc_tenants.bunit_code');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'bunit_code' =>  $value['bunit_code'],
				'business_unit' =>  $value['business_unit'],
				'd_tenant' =>  $value['acroname'],
				'logo' =>  $this->buImage . $value['bu_logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function display_tenant_mod($buCode)
	{
		$this->db->select('*');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->where('loc_tenants.bunit_code', $buCode);
		$this->db->where('loc_tenants.active', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_id' =>  $value['tenant_id'],
				'bunit_code' =>  $value['bunit_code'],
				'd_tenant_name' => $value['tenant'],
				'logo' =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	// public function display_restaurant_mod($tenantCode){
	// 		$this->db->select('*');
	// 		$this->db->from('fd_product_prices as pro_prices');
	// 		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = pro_prices.product_id','inner');
	// 		$this->db->join('fd_uoms as fd_uom','fd_uom.id = pro_prices.uom_id','inner');
	// 		$this->db->where('fd_prod.tenant_id',$tenantCode);
	// 		$this->db->where('fd_prod.active','1');
	// 		$this->db->where('pro_prices.price!=','0.00');
	// 		// $this->db->limit(50);
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	// 	 	foreach($res as $value){
	// 	 			$post_data[] = array(
	// 	 				'unit_measure' => $value['unit_measure'],
	// 	 				'product_id' => $value['product_id'],
	// 	 				'product_uom' => $value['uom_id'],
	// 	 				'tenant_id' => $value['tenant_id'],
	// 	 				'product_name' => $value['product_name'],
	// 	 				'price' => $value['price'],
	// 	 				'image' => $this->productImage.$value['image']
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function display_restaurant_mod($categoryId)
	{

		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod_cat.category_id', $categoryId);
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function display_item_data_mod($prodId, $productUom)
	{
		if ($productUom == 'null') {
			$productUom = null;
		} else {
			$productUom = $productUom;
		}

		$this->db->select('*');
		$this->db->from('fd_product_prices as fd_prod_price');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_price.product_id', 'left');
		$this->db->join('fd_product_addons as fd_prod_addons', 'fd_prod_addons.product_id = fd_prod_price.product_id', 'left');
		$this->db->where('fd_prod_price.product_id', $prodId);
		$this->db->group_by('fd_prod_price.product_id');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$sub_data = array();
		$choices_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' =>  $value['product_id'],
				'variation' => $value['variation'],
				'addon_sides' => $value['addon_side'],
				'addon_dessert' => $value['dessert'],
				'tenant_id' => $value['tenant_id'],
				'product_name' =>  $value['product_name'],
				'description' => $value['description'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
			);

			$this->db->select('prod.product_id, prod.product_name, unit_measure, addons.uom_id , addons.addon_price');
			$this->db->from('fd_products as prod');
			$this->db->join('fd_product_addons as addons', 'prod.product_id = addons.addon_id', 'inner');
			$this->db->join('fd_uoms as uom', 'addons.uom_id = uom.id', 'left');
			$this->db->where('addons.product_id', $value['product_id']);

			$results = $this->db->get()->result();

			if (!empty($results)) :
				if (!empty($results)) :
					foreach ($results as $result) {
						$sub_data['addon_data'][] = array(
							'sub_productid'   => $result->product_id,
							'sub_productname' => $result->product_name,
							'unit'            => $result->unit_measure,
							'uom_id'			=> $result->uom_id,
							'addon_price'     => $result->addon_price,

						);
					}
				endif;
			else :
				$sub_data['addon_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('prod.product_id, prod.product_name,choice.uom_id,choice.default_choice , unit_measure, choice.addon_price');
			$this->db->from('fd_products as prod');
			$this->db->join('fd_product_choices as choice', 'prod.product_id = choice.choice_id', 'inner');
			$this->db->join('fd_uoms as uom', 'choice.uom_id = uom.id', 'left');
			$this->db->where('choice.product_id', $prodId);

			$result2s = $this->db->get()->result();

			if (!empty($result2s)) :
				foreach ($result2s as $result) {
					$choices_data['choices_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'default'			=> $result->default_choice
					);
				}
			else :
				$choices_data['choices_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('prod.product_id, prod.product_name, prices.primary_uom , prices.uom_id, unit_measure, prices.price');
			$this->db->from('fd_products as prod');
			$this->db->join('fd_product_prices as prices', 'prod.product_id = prices.product_id', 'inner');
			$this->db->join('fd_uoms as uom', 'prices.uom_id = uom.id', 'left');
			$this->db->where('prices.product_id', $prodId);

			$result3s = $this->db->get()->result();

			if (!empty($result3s)) :
				foreach ($result3s as $result) {
					$price_data['uom_data'][] = array(
						'price_productid'   => $result->product_id,
						'price_productname' => $result->product_name,
						'unit'              => $result->unit_measure,
						'uom_id'			=> $result->uom_id,
						'price'             => $result->price,
						'default'			=> $result->primary_uom
					);
				}
			else :
				$price_data['uom_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('prod.product_id, prod.product_name, flavors.flavor ,add_flavors.default_choice , add_flavors.flavor_id, add_flavors.addon_price');
			$this->db->from('fd_products as prod');
			$this->db->join('fd_product_prices as prices', 'prod.product_id = prices.product_id', 'inner');
			$this->db->join('fd_addon_flavors as add_flavors', 'add_flavors.product_id = prod.product_id', 'inner');
			$this->db->join('fd_flavors as flavors', 'flavors.id = add_flavors.flavor_id', 'inner');
			$this->db->where('prices.product_id', $prodId);

			$result4s = $this->db->get()->result();

			if (!empty($result4s)) :
				foreach ($result4s as $result) {
					$flavor_data['flavor_data'][] = array(
						'price_productid' => $result->product_id,
						'flavor_name'		=> $result->flavor,
						'flavor_id'         => $result->flavor_id,
						'price'             => $result->addon_price,
						'default'			=> $result->default_choice
					);
				}
			else :
				$flavor_data['flavor_data'][] = array();
			endif;
		}

		// foreach($res as $value){
		// 	$this->db->select('prod.product_id, prod.product_name as suggestion_name');
		// 		$this->db->from('fd_products as prod');
		// 		$this->db->join('fd_product_prices as prices', 'prod.product_id = prices.product_id', 'inner');
		// 		$this->db->join('fd_addon_suggestions as fd_sugg', 'fd_sugg.product_id = prod.product_id', 'left');
		// 		$this->db->where('prices.product_id', $prodId);

		// 		$result5s = $this->db->get()->result();

		// 		if(!empty($result5s)):
		//  		foreach ($result5s as $result) 
		//  		{
		// 	 		$suggestions_data['suggestions_data'][] = array('price_productid' => $result->product_id,

		// 	 									    				'suggestion_name' => $result->suggestion_name



		// 	 									      );
		//  		}
		//  	else:
		//  		$suggestions_data['suggestions_data'][] = array();
		//  	endif;
		// }


		// var_dump($choices_data);
		$post_data[] = $sub_data;
		$post_data[] = $choices_data;
		$post_data[] = $price_data;
		$post_data[] = $flavor_data;
		// $post_data[] = $suggestions_data;

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function addToCartNew_mod($userID, $prodId, $uomId, $uomPrice, $choiceUomId, $choiceId, $choicePrice, $flavorId, $flavorPrice, $_counter, $addonData, $selectedSideOnPrice, $selectedSideItems, $selectedSideItemsUom)
	{
		try {
			$this->db->trans_start();

			$search1 = array("[", "]");
			$replacewith1 = array("", "");
			$selectedSideItems = str_replace($search1, $replacewith1, $selectedSideItems);
			$selectedSideItemsUom = str_replace($search1, $replacewith1, $selectedSideItemsUom);
			$selectedSideOnPrice = str_replace($search1, $replacewith1, $selectedSideOnPrice);

			$this->db->select('*');
			$this->db->from('app_customer_temp_orders as appCart');
			$this->db->where('appCart.customerId', $userID);
			$this->db->where('appCart.product_id', $prodId);
			$this->db->where('appCart.uom_id', $uomId);
			$query = $this->db->get();
			$res = $query->result_array();
			if (empty($res)) {
				if ($uomId == 0) {
					$uomId = null;
				}



				$datamain = array(
					'customerId'  => $userID,
					'product_id'  => $prodId,
					'uom_id'	  => $uomId,
					'quantity'    => $_counter,
					'price'       => $uomPrice,
					'total_price' => (float) $uomPrice * (int) $_counter,
					'created_at'   => date('Y-m-d H:i:s'),
					'updated_at'  => date('Y-m-d H:i:s')
				);

				$this->db->insert('app_customer_temp_orders', $datamain);
				$insert_id = $this->db->insert_id();

				$addon_sideItems_array  = explode(',', $selectedSideItems);

				$addon_oums_array = explode(',', $selectedSideItemsUom);

				$addonn_price_array = explode(',', $selectedSideOnPrice);

				$totalAdChoice = (float) $uomPrice;

				if (count($addon_sideItems_array) > 1) {

					for ($x = 0; $x < count($addon_sideItems_array); $x++) {

						$side_id = $addon_sideItems_array[$x];
						$uom_id  =  $addon_oums_array[$x] == 0 ? null : $addon_oums_array[$x];
						$add_price = $addonn_price_array[$x];


						// if($side_id == 'null' || $uom_id == 'null' || $add_price == 'null'){
						// 	$side_id = null;
						// 	$uom_id = null;
						// 	$add_price = null;
						// }
						$addons = array(
							'temp_order_id' => $insert_id,
							'addon_id' => $side_id,
							'uom_id' =>  $uom_id,
							'addon_sides' => '1',
							'addon_price' => $add_price,
							'created_at'   => date('Y-m-d H:i:s'),
							'updated_at'  => date('Y-m-d H:i:s')
						);

						$this->db->insert('app_customer_temp_order_addons', $addons);

						$totalAdChoice += $add_price;
					}
				}
				if ($choiceId != 0) {
					$choice = array(
						'temp_order_id' => $insert_id,
						'choice_id' => $choiceId,
						'uom_id' =>  $choiceUomId,
						'addon_price' => $choicePrice,
						'created_at'   => date('Y-m-d H:i:s'),
						'updated_at'  => date('Y-m-d H:i:s')
					);
					$this->db->insert('app_customer_temp_order_choices', $choice);

					$totalAdChoice += $choicePrice;
				}

				if ($flavorId != 0) {
					$flavor = array(
						'temp_order_id' => $insert_id,
						'flavor_id' => $flavorId,
						'addon_price' =>  $flavorPrice,
						'created_at'   => date('Y-m-d H:i:s'),
						'updated_at'  => date('Y-m-d H:i:s')
					);
					$this->db->insert('app_customer_temp_order_flavors', $flavor);

					$totalAdChoice += $flavorPrice;
				}

				$x = $totalAdChoice * (int) $_counter;

				$this->db->set('total_price', $x);
				$this->db->where('id', $insert_id);
				$this->db->update('app_customer_temp_orders');
				// dump($x);
				$this->db->trans_complete();
			}
		} catch (\Exception $th) {
			$this->db->trans_rollback();
		}
	}

	public function add_to_cart_mod($customerId, $buCode, $tenantCode, $prodId, $productUom, $flavorId, $drinkId, $drinkUom, $friesId, $friesUom, $sideId, $sideUom, $selectedSideItems, $selectedSideItemsUom, $selectedDessertItems, $selectedDessertItemsUom, $_counter)
	{


		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$addon_sideItems = str_replace($search1, $replacewith1, $selectedSideItems);
		$addon_dessertItems = str_replace($search1, $replacewith1, $selectedDessertItems);

		// $selectedDessertItemsUom = '[null,1]';
		$search2 = array("[", "]");
		$replacewith2 = array("", "");
		$addon_sideItems_uom = str_replace($search2, $replacewith2, $selectedSideItemsUom);
		$addon_dessertItems_uom = str_replace($search2, $replacewith2, $selectedDessertItemsUom);

		if ($productUom == 'null') {
			$productUom = null;
		}
		if ($flavorId == 'null') {
			$flavorId = null;
		}
		// if($drinkUom == 'null'){
		// 	$drinkUom = null;
		// }
		if ($friesUom == 'null') {
			$friesUom = null;
		}
		if ($sideUom == 'null') {
			$sideUom = null;
		}
		$this->db->select('*');
		$this->db->from('app_cart_main as appCart');
		$this->db->where('appCart.customerId', $customerId);
		$this->db->where('appCart.productId', $prodId);
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {

			$datamain = array(
				'buId' => $buCode,
				'tenantId' => $tenantCode,
				'customerId' => $customerId,
				'productId' => $prodId,
				'uom' => $productUom,
				'flavor' => $flavorId,
				'quantity' => $_counter,
				'create_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_cart_main', $datamain);
			$insert_id = $this->db->insert_id();

			if ($drinkId != 'null') {
				$data1 = array(
					'cart_id' => $insert_id,
					'drink_id' 	=> $drinkId,
					'drink_uom' => $drinkUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_drink', $data1);
			} else if ($friesId != 'null') {
				$data2 = array(
					'cart_id' => $insert_id,
					'fries_id' 	=> $friesId,
					'fries_uom' => $friesUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_fries', $data2);
			} else if ($sideId != 'null') {
				$data3 = array(
					'cart_id' => $insert_id,
					'side_id' 	=> $sideId,
					'side_uom' => $sideUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_sides', $data3);
			}

			if ($addon_sideItems != null) {
				$addon_sideItems_array  = explode(',', $addon_sideItems);
				$addon_sideItems_count  = count($addon_sideItems_array);

				$addon_sideItems_uom_array = explode(',', $addon_sideItems_uom);

				for ($x = 0; $x < $addon_sideItems_count; $x++) {
					$side_id = $addon_sideItems_array[$x];
					$side_uom_id = $addon_sideItems_uom_array[$x];
					if ($side_uom_id == 'null') {
						$side_uom_id = null;
					}
					$data4 = array(
						'cart_id' => $insert_id,
						'side_id' => $side_id,
						'side_uom' => $side_uom_id,
						'type' => "side_addon"
					);
					$this->db->insert('app_cart_addons_side_items', $data4);
				}
			}

			if ($addon_dessertItems != null) {
				$addon_dessertitems_array  = explode(',', $addon_dessertItems);
				$addon_dessertitems_count  = count($addon_dessertitems_array);

				$addon_dessertitems_uom_array  = explode(',', $addon_dessertItems_uom);

				for ($x = 0; $x < $addon_dessertitems_count; $x++) {
					$dessert_id =  $addon_dessertitems_array[$x];
					$dessert_uom_id = $addon_dessertitems_uom_array[$x];
					if ($dessert_uom_id == 'null' or $dessert_uom_id == 0) {
						$dessert_uom_id = null;
					}
					$data5 = array(
						'cart_id' => $insert_id,
						'side_id' => $dessert_id,
						'side_uom' => $dessert_uom_id,
						'type' => "dessert_addon"
					);
					$this->db->insert('app_cart_addons_side_items', $data5);
				}
			}
		} else {
			$this->db->set('quantity', $_counter);
			$this->db->where('customerId', $customerId);
			$this->db->where('productId', $prodId);
			$this->db->update('app_cart_main');
		}
	}

	public function selectSuffix_mod()
	{
		$this->db->select('*');
		$this->db->from('name_suffix as nameSuffix');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'suffix' => $value['suffix']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTowns_mod()
	{
		$this->db->select('*');
		$this->db->from('towns as towns');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' =>  $value['town_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getbarrio_mod($town_id)
	{
		$this->db->select('*');
		$this->db->from('barangays as barangays');
		$this->db->where('barangays.town_id', $town_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'brgy_id' => $value['brgy_id'],
				'town_id' => $value['town_id'],
				'brgy_name' => $value['brgy_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updateCartQty_mod($id, $qty)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->where('appcart.id', $id);
		$query = $this->db->get();
		$res = $query->result();
		$total_price = $res[0]->price;
		// dd($total_price);
		foreach ($query->result() as $temp_order) {
			// $total_price += (float) $temp_order->price;

			$this->db->select("SUM(addon_price) as total_addons");
			$this->db->from("app_customer_temp_order_addons");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasAddons = $this->db->get()->result();

			if (empty($hasAddons) === false) {
				$total_price += (float) $hasAddons[0]->total_addons;
			}
		}

		$res = $query->row();

		$this->db->set('quantity', $qty);
		$this->db->set('total_price', $qty * $total_price);
		$this->db->where('id', $id);
		$this->db->update('app_customer_temp_orders');
	}

	public function getCounter_mod($cusid)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->where('appcart.customerId', $cusid);
		$query = $this->db->get();
		// echo $query->num_rows();

		$post_data = array();
		$post_data[] = array(

			'num' => $query->num_rows()

		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function listenCartSubtotal_mod($cusid)
	{
		// $this->db->select('*, sum(appcart.price*appcart.quantity) as subtotal');
		// $this->db->from('app_cart as appcart');
		// $this->db->where('appcart.customerId',$cusid);
		// $query = $this->db->get();
		// $res = $query->result_array();
		// $post_data = array();
		// $subtotal = 0;

		// foreach($res as $value){
		// 	$subtotal += $value['quantity'] * $value['price'];
		// 	$post_data[] = array(
		// 			'subtotal' => $value['subtotal']
		// 		);
		// }

		//  	$item = array('user_details' => $post_data);
		// echo json_encode($item);

		$this->db->select('appcart.quantity as cart_qty,IFNULL(SUM(main_prod_price.price),0) + IFNULL(SUM(fd_fries_price.price),0) + IFNULL(SUM(fd_drink_price.price),0) + IFNULL(SUM(fd_side_price.price),0) + IFNULL(SUM(fd_flavors.addon_price),0) as total');
		$this->db->from('app_cart_main as appcart');
		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		// $this->db->join('locate_business_units as loc_bu','loc_bu.bunit_code = appcart.buId','inner');
		// $this->db->join('locate_tenants as loc_tenants','loc_tenants.tenant_id = appcart.tenantId','inner');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND main_prod_price.uom_id = appcart.uom', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND fd_drink_price.uom_id = drink_id.drink_uom', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND fd_fries_price.uom_id = fries_id.fries_uom', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND fd_side_price.uom_id = side_id.side_uom', 'left');
		$this->db->where('appcart.customerId', $cusid);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'subtotal' => $value['total'] * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getMainOrders($customer_id, $deliveryDateData, $deliveryTimeData, $modeOfOrder, $insert_id)
	{
		// dd($deliveryDateData, $deliveryTimeData);
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->where('temp_orders.customerId', $customer_id);
		$query = $this->db->get();

		$main_orders = $query->result_array();

		foreach ($main_orders as $main_order) {
			$order_id = $this->insertMainOrders($insert_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $modeOfOrder);

			$this->insertChoices($main_order['id'], $order_id);
			$this->insertAddons($main_order['id'], $order_id);
			$this->insertFlavors($main_order['id'], $order_id);
		}

		$this->clearCustomerCart($main_orders);
	}

	public function clearCustomerCart($main_orders)
	{
		foreach ($main_orders as $main_order) {

			$temp_order_id = $main_order['id'];

			$this->clearChoices($temp_order_id);
			$this->clearAddons($temp_order_id);
			$this->clearFlavors($temp_order_id);
			$this->clearMainOrders($temp_order_id);
		}
	}

	public function clearCustomerCartPerItem($cartID)
	{
		// foreach ($main_orders as $main_order) {

		$temp_order_id = $cartID;

		$this->clearChoices($temp_order_id);
		$this->clearAddons($temp_order_id);
		$this->clearFlavors($temp_order_id);
		$this->clearMainOrders($temp_order_id);
		// }
	}

	private function clearChoices($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_choices', array('temp_order_id' => $temp_order_id));
	}

	private function clearAddons($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_addons', array('temp_order_id' => $temp_order_id));
	}

	private function clearFlavors($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_flavors', array('temp_order_id' => $temp_order_id));
	}

	private function clearMainOrders($temp_order_id)
	{
		$this->db->delete('app_customer_temp_orders', array('id' => $temp_order_id));
	}

	private function insertMainOrders($ticket_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $modeOfOrder)
	{
		// dd(date('Y-m-d H:i:s', strtotime($deliveryDateData . " " . $deliveryTimeData)));
		$order = array(
			'ticket_id' => $ticket_id,
			'product_id' => $main_order['product_id'],
			'uom_id' => $main_order['uom_id'],
			'quantity' => $main_order['quantity'],
			'product_price' => $main_order['price'],
			'measurement' => $main_order['measurement'],
			'total_price' => $main_order['total_price'],
			'mop' => $modeOfOrder,
			'pickup_at' =>  date('Y-m-d H:i:s', strtotime($deliveryDateData . " " . $deliveryTimeData)),
			'icoos' =>  $main_order['icoos'],
			'user_id' => $customer_id,
			'submitted_at' => date('Y-m-d H:i:s'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('toms_customer_orders', $order);

		return $this->db->insert_id();
	}

	private function insertChoices($temp_order_id, $order_id)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$choices = $query->result_array();

			foreach ($choices as $key => $choice) {
				$choice_data = array(
					'order_id' => $order_id,
					'choice_id' => $choice['choice_id'],
					'uom_id' => $choice['uom_id'],
					'choice_sides' => 1,
					'addon_price' => $choice['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_choices', $choice_data);
			}
		}
	}

	private function insertAddons($temp_order_id, $order_id)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_order_addons');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$addons = $query->result_array();

			foreach ($addons as $key => $addon) {
				$addon_data = array(
					'order_id' => $order_id,
					'addon_id' => $addon['addon_id'],
					'uom_id' => $addon['uom_id'],
					'addon_sides' => 1,
					'addon_price' => $addon['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_addons', $addon_data);
			}
		}
	}

	private function insertFlavors($temp_order_id, $order_id)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_order_flavors');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$flavors = $query->result_array();

			foreach ($flavors as $flavor) {
				$flavor_data = array(
					'order_id' => $order_id,
					'flavor_id' => $flavor['flavor_id'],
					'addon_price' => $flavor['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_flavors', $flavor_data);
			}
		}
	}

	private function getTenantIDs($ticket_id)
	{
		$this->db->select('DISTINCT(tenant_id) as tenant_id');
		$this->db->from('toms_customer_orders');
		$this->db->join('fd_products', 'fd_products.product_id = toms_customer_orders.product_id', 'inner');
		$this->db->where('ticket_id', $ticket_id);

		$query = $this->db->get();

		$result = $query->result_array();

		return $result;
	}


	public function placeOrder_delivery_mod($cusId, $deliveryDateData, $deliveryTimeData, $selectedDiscountType, $deliveryCharge, $changeFor)
	{
		// $modeOfOrder = 0;

		dd(123);

		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod($cusId, true) + $deliveryCharge;
		// dd($totalPayablePrice);
		$this->getMainOrders($cusId, $deliveryDateData, $deliveryTimeData, $modeOfOrder, $insert_id, $changeFor);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}


		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$selectedDiscountType = str_replace($search1, $replacewith1, $selectedDiscountType);

		if (!empty($selectedDiscountType)) {
			$selectedDiscountType_array  = explode(',', $selectedDiscountType);
			$addon_sideItems_count  = count($selectedDiscountType_array);

			for ($x = 0; $x < $addon_sideItems_count; $x++) {
				$cust_disc = array(
					'ticket_id' => $insert_id,
					'customer_discount_storage_id' => $selectedDiscountType_array[$x],
					'status' => '0',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('customer_discounts', $cust_disc);
			}
		}

		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $changeFor,
			'delivery_charge' => $deliveryCharge,
			'change' => $changeFor - $totalPayablePrice,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		foreach ($tenant_ids as $tenant_id) {

			// dump($tenant_id);

			$tId = $tenant_id['tenant_id'];

			$fd_vtype = array(
				'ticket_id' => $insert_id,
				'transpo_id' => '1',
				'tenant_id' => $tId,
				'delivery_charge' => $deliveryCharge,
				'status' => '1',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('fd_vtype_suggestions', $fd_vtype);

			$this->pusher()->trigger("order-submitted.{$tId}", 'App\Events\OrderSubmitted', array('message' => ''));
		}



		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();
	}

	public function placeOrder_mod($cusId, $deliveryDateData, $deliveryTimeData, $subtotal, $changeFor,   $selectedDiscountType)
	{

		// $modeOfOrder = '1';
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod($cusId, true);
		// dd($totalPayablePrice);
		$this->getMainOrders($cusId, $deliveryDateData, $deliveryTimeData, $modeOfOrder, $insert_id);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}


		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$addon_sideItems = str_replace($search1, $replacewith1, $selectedDiscountType);

		$addon_sideItems_array  = explode(',', $addon_sideItems);
		$addon_sideItems_count  = count($addon_sideItems_array);


		for ($x = 0; $x < $addon_sideItems_count; $x++) {
			$cust_disc = array(
				'ticket_id' => $insert_id,
				'customer_discount_storage_id' => $addon_sideItems_array[$x],
				'status' => '0',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('customer_discounts', $cust_disc);
		}



		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $totalPayablePrice,
			'delivery_charge' => 0,
			'change' => $changeFor,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		foreach ($tenant_ids as $tenant_id) {

			// dump($tenant_id);

			$tId = $tenant_id['tenant_id'];

			$fd_vtype = array(
				'ticket_id' => $insert_id,
				'transpo_id' => '1',
				'tenant_id' => $tId,
				'delivery_charge' => $changeFor,
				'status' => '1',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('fd_vtype_suggestions', $fd_vtype);

			$this->pusher()->trigger("order-submitted.{$tId}", 'App\Events\OrderSubmitted', array('message' => ''));
		}



		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();



		// $item = array('user_details' => $post_data);
		// echo json_encode($item);



	}

	// public function FunctionName(Type $var = null)
	// {
	// 	# code...
	// }

	public function savePickup_mod($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $getTenantData, $subtotal, $tender)
	{
		// $jsonStr = json_decode($deliveryDateData,true);
		// var_dump($this->app_cart(12));

		$deliveryDateData = str_replace(array("[", "]"), array("", ""), $deliveryDateData);
		$deliveryDateData  = explode(',', $deliveryDateData);

		$deliveryTimeData = str_replace(array("[", "]"), array("", ""), $deliveryTimeData);
		$deliveryTimeData  = explode(',', $deliveryTimeData);

		$getTenantData = str_replace(array("[", "]"), array("", ""), $getTenantData);
		$getTenantData  = explode(',', $getTenantData);

		$count = 1;
		$tody_order = $this->app_cart_today_order($customerId, 5);
		if (!empty($tody_order)) {
			$count = $tody_order + 1;
		}

		$data = array(
			'ticket' => date('ymd') . '-2-00' . $count,
			'customer_id' => $customerId,
			'type' => '2',
			'mop' => 'Pick-up',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('tickets', $data);
		$insert_id = $this->db->insert_id();

		$this->db->select('*,appcart.productId as appcartproductId,side_id.side_id as side_id,side_id.side_uom as side_uom,fries_id.fries_id as fries_id,fries_id.fries_uom as fries_uom,appcart.uom as productUom,appcart.flavor as flavor_id,appcart.productId as productId,drink_id.drink_id as drink_id,drink_id.drink_uom as drink_uom,appcart.quantity as cart_qty,appcart.id as d_id,fd_flavors.addon_price as flavor_price,loc_tenants.tenant_id as tenantId,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');

		$this->db->from('app_cart_main as appcart');

		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = appcart.buId', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = appcart.tenantId', 'left');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND IFNULL(main_prod_price.uom_id,0) = IFNULL(appcart.uom,0)', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND IFNULL(fd_drink_price.uom_id,0) = IFNULL(drink_id.drink_uom,0)', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND IFNULL(fd_fries_price.uom_id,0) = IFNULL(fries_id.fries_uom,0)', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND IFNULL(fd_side_price.uom_id,0) = IFNULL(side_id.side_uom,0)', 'left');

		$this->db->where('appcart.customerId', $customerId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$totalitemprice = 0;

		foreach ($res as $value) {
			$totalitemprice = $value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'];

			$key = array_search((int) $value['tenantId'], $getTenantData);

			$date = $deliveryDateData[$key];
			$time = $deliveryTimeData[$key];

			$dateTimePickup = date("Y-m-d h:i:s", strtotime("$date $time"));

			// $post_data[] = array(
			// 	'appcartproductId' => $value['appcartproductId'],
			// 	'd_productId' =>$value['productId'],
			// 	'd_productUom' => $value['productUom'],
			// 	'd_flavor_id' => $value['flavor_id'],
			// 	'flavor_price' => $value['flavor_price'],
			// 	'd_drink_id' =>$value['drink_id'],
			// 'd_drink_uom' =>$value['drink_uom'],
			// 'd_fries_id' => $value['fries_id'],
			// 'd_fries_uom' => $value['fries_uom'],
			// 'd_side_id' => $value['side_id'],
			// 'd_side_uom'=> $value['side_uom'],
			// 	'd_id' => $value['d_id'],
			// 	'prod_name' => $value['prod_name'],
			// 	'cart_qty' => $value['cart_qty'],
			// 	'loc_bu_name' => $value['loc_bu_name'],
			// 	'loc_tenant_name' => $value['loc_tenant_name'],
			// 	'flavor_price' => $value['flavor_price'],
			// 'prod_price' => $value['prod_price'],
			// 	'drink_name' => $value['drink_name'],
			// 	'drink_price' => $value['drink_price'],
			// 	'fries_name' => $value['fries_name'],
			// 	'fries_price' => $value['fries_price'],
			// 	'side_name' => $value['side_name'],
			// 	'side_price' => $value['side_price'],
			// 	'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'],);


			$data1 = array(
				'ticket_id' => $insert_id,
				'product_id' =>  $value['productId'],
				'uom_id' => $value['productUom'],
				'quantity' =>  $value['cart_qty'],
				'product_price' => $value['prod_price'],
				'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'],
				'mop' => '1',
				'pickup_at' => $dateTimePickup,
				'icoos' => 0,
				'submitted_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('toms_customer_orders', $data1);

			if ($value['flavor_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'flavor_id' => $value['flavor_id'],
					'addon_price' => $value['flavor_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_flavors', $data11);
			}
			if ($value['drink_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => $value['drink_id'],
					'choice_fries' => null,
					'choice_sides' => null,
					'choice_sizes' => null,
					'addon_price' => $value['drink_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
			if ($value['fries_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => null,
					'choice_fries' => $value['fries_id'],
					'choice_sides' => null,
					'choice_sizes' => null,
					'addon_price' => $value['fries_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
			if ($value['side_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => null,
					'choice_fries' => null,
					'choice_sides' => $value['side_id'],
					'choice_sizes' => null,
					'addon_price' => $value['side_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
		}

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('customer_delivery_infos', $infos);
		}

		//save customer_bills
		$bills = array(
			'ticket_id' => $insert_id,
			'amount' => $subtotal,
			'change' => $tender,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_bills', $bills);

		$this->db->where('customerId', $customerId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($customerId);
	}

	public function app_cart($customerId)
	{
		$this->db->select('*');
		$this->db->from('app_cart as appcart');
		$this->db->where('appcart.customerId', $customerId);
		return $this->db->get()->result_array();
	}

	public function app_cart_today_order($customerId, $modeOfOrder)
	{
		$this->db->select('count(appcart.ticket)');
		$this->db->from('tickets as appcart');
		$this->db->where('appcart.customer_id', $customerId);
		$this->db->where('appcart.type', '2');
		$this->db->where('SUBSTR(appcart.ticket,1,8)', date('ymd') . '-2');
		$this->db->group_by('appcart.ticket');

		$tody_order = $this->db->count_all_results();

		$count = 1;

		if (!empty($tody_order)) {
			$count = $tody_order + 1;
		}

		$data = array(
			'ticket' => date('ymd') . '-2-00' . $count,
			'customer_id' => $customerId,
			'type' => '2',
			'order_type_stat' => $modeOfOrder,
			'mop' => $modeOfOrder == '1' ? "Pick-up" : "Delivery",
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('tickets', $data);

		return $this->db->insert_id();
	}

	// public function app_cart_today_order($customerId){
	// 	$this->db->select('count(appcart.ticket)');
	// 	$this->db->from('tickets as appcart');
	// 	$this->db->where('appcart.customer_id',$customerId);
	// 	$this->db->where('SUBSTR(appcart.ticket,1,8)',date('ymd').'-2');
	// 	$this->db->group_by('appcart.ticket');

	// 	echo $this->db->count_all_results();
	// }

	public function loadSubTotal_mod($cusId)
	{
		// sum(appcart.quantity * appcart.price) as subtotal
		// $this->db->select('*, product_prices.price as product_price , product_name.product_name as prod_name, product_name');
		// $this->db->select('*,appcart.quantity as cart_qty,appcart.id as d_id,main_prod.image as prod_image,fd_flavors.addon_price as flavor_price,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');
		$this->db->select('appcart.quantity as cart_qty,IFNULL(SUM(main_prod_price.price),0) + IFNULL(SUM(fd_fries_price.price),0) + IFNULL(SUM(fd_drink_price.price),0) + IFNULL(SUM(fd_side_price.price),0) + IFNULL(SUM(fd_flavors.addon_price),0) as total');
		$this->db->from('app_cart_main as appcart');
		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		// $this->db->join('locate_business_units as loc_bu','loc_bu.bunit_code = appcart.buId','inner');
		// $this->db->join('locate_tenants as loc_tenants','loc_tenants.tenant_id = appcart.tenantId','inner');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND main_prod_price.uom_id = appcart.uom', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND fd_drink_price.uom_id = drink_id.drink_uom', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND fd_fries_price.uom_id = fries_id.fries_uom', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND fd_side_price.uom_id = side_id.side_uom', 'left');
		$this->db->where('appcart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'subtotal' => $value['total'] * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadSubTotalnew_mod($userId, $from_controller = false)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders');
		$this->db->where('customerId', $userId);
		$query = $this->db->get();
		$res = $query->result_array();
		$grand_total = 0;

		foreach ($res as $temp_order) {
			$grand_total += $temp_order['total_price'];
		}

		if ($from_controller == false) {
			$item = [];
			$item['grand_total'] = $grand_total;

			echo json_encode(['user_details' => [$item]]);
			exit();
		}

		return (float) $grand_total;
	}



	private function totalAddons($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_addons');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();

			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}


	private function totalChoices($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();
			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}

	private function totalFlavors($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_flavors');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();
			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}

	// public function loadRiderDetails_mod($ticket_id){
	// 		$this->db->select('*');
	// 		$this->db->from('tickets as ticket_id ');
	// 		$this->db->join('toms_tag_riders as tagrider', 'tagrider.ticket_id = ticket_id.id','inner');
	// 		$this->db->join('toms_riders_data as riderdata', 'riderdata.id = tagrider.rider_id','inner');
	// 		$this->db->where('ticket_id.ticket',$ticket_id);
	// 		$query = $this->db->get();
	// 		$res = $query->result_array();
	//     	$post_data = array();
	// 	 	foreach($res as $value){
	// 		 			$post_data[] = array(
	// 		 				'firstname' => $value['r_firstname'],
	// 		 				'lastname' => $value['r_lastname'],
	// 		 				'photo' => $this->buImage.$value['r_picture'],
	// 		 				'mobile' => $value['r_mobile'],
	// 		 				'rm_brand' => $value['rm_brand'],
	// 		 				'rm_model' => $value['rm_model'],
	// 		 				'rm_picture' => $this->buImage.$value['rm_picture']

	// 		 		);
	// 			}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function getTrueTime_mod()
	{
		$t = time();
		$post_data[] = array(
			'date_today' => date("Y-m-d", $t),
			'hour_today' => date("H", $t),
			'minute_today' =>  date("i", $t)

		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadFlavor_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_addon_flavors as fd_addon_flavors ');
		$this->db->join('fd_flavors as fd_flavors', 'fd_flavors.id = fd_addon_flavors.flavor_id', 'inner');
		$this->db->where('fd_addon_flavors.product_id', $productId);
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'flavor_id' => $value['flavor_id'],
				'add_on_flavors' => $value['flavor'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadDrinks_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices ');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_drinks', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'drink_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadFries_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_fries', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'fries_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}



	public function loadSide_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices ');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_sides', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'side_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function checkAddon_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->where('fd_product_addons.product_id', $productId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'addon_sides' => $value['addon_sides'],
				'addon_dessert' => $value['addon_dessert'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadAddonSide_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
		$this->db->where('fd_product_addons.product_id', $productId);
		$this->db->where('fd_product_addons.addon_sides', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'addon_id' => $value['addon_id'],
				'uom_id' => $value['uom_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}
	public function loadAddonDessert_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
		$this->db->where('fd_product_addons.product_id', $productId);
		$this->db->where('fd_product_addons.addon_dessert', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'addon_id' => $value['addon_id'],
				'uom_id' => $value['uom_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}



	public function cancelOrderSingleFood_mod($tomsId, $ticketId)
	{

		$this->db->set('canceled_status', '1');
		$this->db->where('id', $tomsId);
		$this->db->update('toms_customer_orders');

		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_order');
		$this->db->where('toms_order.ticket_id', $ticketId);
		$this->db->where('toms_order.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->num_rows();
		if ($res == 0) {
			$this->db->set('cancel_status', '1');
			$this->db->where('id', $ticketId);
			$this->db->update('tickets');
		}
	}

	public function cancelOrderSingleGood_mod($tomsId, $ticketId)
	{
		$this->db->set('canceled_status', '1');
		$this->db->where('id', $tomsId);
		$this->db->update('gc_final_order');

		$this->db->set('pending_status', '0');
		$this->db->where('id', $tomsId);
		$this->db->update('gc_final_order');


		$this->db->select('*');
		$this->db->from('gc_final_order as gcfinal');
		$this->db->where('gcfinal.ticket_id', $ticketId);
		$this->db->where('gcfinal.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->num_rows();
		if ($res == 0) {
			$this->db->set('cancel_status', '1');
			$this->db->where('id', $ticketId);
			$this->db->update('tickets');

			$this->db->set('cancelled_status', '1');
			$this->db->set('cancelled_at', date('Y-m-d H:i:s'));
			$this->db->where('ticket_id', $ticketId);
			$this->db->update('gc_order_statuses');


			//          $this->db->select('*');
			// 			$this->db->from('gc_order_statuses as gc_stat');
			// 			$this->db->where('gc_stat.ticket_id',$ticketId);
			// 			$query = $this->db->get();
			// 			$res = $query->num_rows();	
			// 			$this->pusher()->trigger("private-grocery-order-submitted", 'App\Events\GroceryOrderSubmitted', array('message' => ''));
		}
	}

	public function loadLocation_mod($locid)
	{
		$this->db->select('*');
		$this->db->from('towns');
		$this->db->where('town_id', $locid);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' => $value['town_name'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	function delete_on_checkout_mod($cusId)
	{

		$this->db->select('*');
		$this->db->from('app_cart_main as appcart');
		$this->db->where('customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			// $post_data[] = array(
			// 	'cart_id' => $value['id']
			// );

			$this->db->where('app_cart_main.id', $value['id']);
			$this->db->delete('app_cart_main');

			$this->db->where('app_cart_fries.cart_id', $value['id']);
			$this->db->delete('app_cart_fries');

			$this->db->where('app_cart_addons_side_items.cart_id', $value['id']);
			$this->db->delete('app_cart_addons_side_items');

			$this->db->where('app_cart_drink.cart_id', $value['id']);
			$this->db->delete('app_cart_drink');

			$this->db->where('app_cart_sides.cart_id', $value['id']);
			$this->db->delete('app_cart_sides');
		}
	}

	function displayAddOns_mod($cartId)
	{
		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = drink_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = fries_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res1 = $query->result_array();
		$post_data1 = array();
		foreach ($res1 as $value) {
			$post_data1[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = side_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res2 = $query->result_array();
		$post_data2 = array();
		foreach ($res2 as $value) {
			$post_data2[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = side_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res3 = $query->result_array();
		$post_data3 = array();
		foreach ($res3 as $value) {
			$post_data3[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$item = array($post_data);
		$a = json_encode($item);

		$item1 = array($post_data1);
		$b = json_encode($item1);

		$item2 = array($post_data2);
		$c = json_encode($item2);

		$item3 = array($post_data3);
		$d = json_encode($item3);


		echo $merger = json_encode(array_merge(json_decode($a, true), json_decode($b, true), json_decode($c, true), json_decode($d, true)));
	}


	public function upLoadImage_sr_mod($customerId)
	{
		$this->db->select("*");
		$this->db->from("app_users");
		$this->db->where("customer_id", $customerId);
		$query = $this->db->get();
		$res3 = $query->result_array();
		$post_data = array();
		foreach ($res3 as $value) {
			$post_data[] = array(
				'user_id' => $value['id'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function showFlavor_mod($cartId)
	{
		$this->db->select("*");
		$this->db->from('app_cart_main as appcartmain');
		$this->db->join("fd_flavors as fd_flavors", "fd_flavors.id = appcartmain.flavor", "left");
		$this->db->join("fd_addon_flavors as fd_addon_flavors", "fd_addon_flavors.flavor_id = appcartmain.flavor AND appcartmain.productId=fd_addon_flavors.product_id", "left");
		$this->db->where("appcartmain.id", $cartId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'flavor_d' => $value['flavor'],
				'flavor_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTotal_mod($ticket_id)
	{
		$this->db->select('*,sum(toms_order.total_price) as total_price');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = ticket.id');
		// $this->db->join('customer_delivery_infos as dev_info','dev_info.ticket_id = ticket.id');
		// $this->db->join('barangays as brgy','brgy.brgy_id = dev_info.barangay_id');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = ticket.id');
		$this->db->where('ticket.ticket', $ticket_id);
		// $this->db->where('toms_order.canceled_status','0');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			// if($value['total_price']==NULL){
			// 	$newval = 0;
			// }else{
			// 	$newval = number_format($value['total_price'],2);
			// }
			$post_data[] = array(
				'total_price' => ceil($value['total_price'] + $value['delivery_charge'])
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function checkifongoing_mod($ticket_id)
	{



		$this->db->select('*');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_tag_riders as tagriders', 'tagriders.ticket_id = ticket.id');
		$this->db->where("ticket.ticket", $ticket_id);
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {
			echo "false";
		}
		if (!empty($res)) {
			echo "true";
		}
	}

	public function viewCategories_mod($categoryID)
	{
		$this->db->select('*');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_categories as fd_cat', 'fd_cat.category_id = fd_prod_cat.category_id');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->where("fd_prod_cat.category_id", $categoryID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'total_price' => $value['product_name'],
			);
		}
		$item = array('user_details' => $value);
		echo json_encode($item);
	}

	public function checkifemptystore_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_products as fd_prod');
		$this->db->where("fd_prod.tenant_id", $tenant_id);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function getCategories_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_categories as fd_ccat');
		$this->db->where("fd_ccat.tenant_id", $tenant_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'tenant_id' => $value['tenant_id'],
				'category_id' => $value['category_id'],
				'category' => $value['category'],
				'image' =>  $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsBycategories_mod($category_id)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod_cat.category_id', $category_id);
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$now = strtotime(date('H:i:s'));
		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'isAvailnow' => $avail
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsByCategoriesAll_mod($tenant_id)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->where('fd_prod.tenant_id', $tenant_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		$now = strtotime(date('H:i:s'));
		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'isAvailnow' => $avail
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}




	public function getGcItems_mod($offset, $categoryNo, $itemSearch)
	{
		$this->db->select('*, gc_prod.product_id as prod_id')
			->from('gc_product_items as gc_prod')
			->limit(10)
			->offset($offset);

		if (!empty($itemSearch)) {
			$this->db->like('gc_prod.product_name', $itemSearch);
		}

		$products = $this->db->where('gc_prod.status', 'active')
			->where('gc_prod.category_no', $categoryNo)
			->where('image!=', 'null')
			->get()
			->result();

		$user_details = array_map(function (object $product) {

			$with = $this->productLeastUOMPrice($product);

			if (!empty($with)) {
				$product->prod_id = $product->product_id;
				$product->image = $this->gcproductImage . $product->image;
				$product->uom = $with->UOM;
				$product->price = number_format($with->price_with_vat, 2);
				$product->uom_id = $with->uom_id;
			}

			return $product;
		}, $products);

		echo json_encode(compact('user_details'));
	}

	private function productLeastUOMPrice(object $product): object
	{
		$uomPrice = $this->db->select('*')
			->from('gc_product_uoms as uom')
			->join('gc_product_prices as price', 'uom.itemcode = price.itemcode AND uom.UOM = price.UOM')
			->where('uom.itemcode', $product->itemcode)
			->order_by('price_with_vat', 'asc')
			->limit(1)
			->get()
			->result();

		if (!empty($uomPrice)) {
			return $uomPrice[0];
		}
	}

	public function gc_cart_mod($cusId)
	{
		$this->db->select('*,cart_gc.item_code as itemCode , cart_gc.quantity as cartQty');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');

		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id' => $value['id'],
				'cart_qty' => $value['cartQty'],
				'product_id' => $value['itemCode'],
				'product_name' => $value['product_name'],
				'product_image' => $this->gcproductImage . $value['image'],
				'product_uom' => $value['UOM'],
				'bu' => $value['business_unit'],
				'price_price' => $value['price_with_vat'],
				'total_price' => number_format($value['price_with_vat'] * $value['cartQty'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function addToCartGc_mod($userID, $buCode, $prodId, $itemCode, $uomSymbol, $uom, $_counter)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as appCart');
		// $this->db->where('appCart.customer_id',$userID);
		$this->db->where('appCart.item_code', $itemCode);
		// $this->db->where('appCart.uom_symbol',$uomSymbol);
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {
			$data = array(
				'customer_id' => $userID,
				'buId' => $buCode,
				'product_id' => $prodId,
				'item_code' => $itemCode,
				'uom' => $uom,
				'uom_symbol' => $uomSymbol,
				'quantity' => $_counter,
				'date_created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_cart_gc', $data);
		}
	}

	public function updateGcCartQty_mod($id, $qty)
	{
		$this->db->set('quantity', $qty);
		$this->db->where('id', $id);
		$this->db->update('app_cart_gc');
	}

	public function loadGcSubTotal_mod($userID)
	{
		$this->db->select('*,SUM(price_with_vat * cart_gc.quantity) as price_vat');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->where('cart_gc.customer_id', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => number_format($value['price_vat'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getGcCounter_mod($cusid)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as appgccart');
		$this->db->where('appgccart.customer_id', $cusid);
		$query = $this->db->get();
		// echo $query->num_rows();

		$post_data = array();
		$post_data[] = array(
			'num' => $query->num_rows()
		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getGcCategories_mod()
	{
		$this->db->select('*');
		$this->db->from('gc_product_items as gc_cate');
		$this->db->group_by('gc_cate.category_no');
		// $this->db->limit(10);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'category_name' => $value['category_name'],
				'category_no' => $value['category_no'],
				'itemcode' => $value['itemcode'],
				'image' => $this->gcproductImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsByGcCategories_mod($category_id, $offset)
	{
		$this->db->select('*');
		$this->db->from('gc_product_items as gc_cate');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = gc_cate.itemcode');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_cate.itemcode AND gc_product_uom.UOM = gc_prod_price.UOM');
		$this->db->limit(10);
		$this->db->offset($offset);
		$this->db->where('gc_cate.category_no', $category_id);
		$this->db->where('gc_cate.image!=', 'null');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_name' => $value['product_name'],
				'itemcode' => $value['itemcode'],
				'image'	=> $this->gcproductImage . $value['image'],
				'price'	=> number_format($value['price_with_vat'], 2),
				'uom' => $value['UOM'],
				'uom_id' => $value['uom_id'],
				'product_id' => $value['product_id'],
				'category_name' => $value['category_name'],
				'category_no' => $value['category_no'],
				'itemcode' => $value['itemcode'],
				'image' => $this->gcproductImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function removeGcItemFromCart_mod($cartId)
	{
		$this->db->where('app_cart_gc.id', $cartId);
		$this->db->delete('app_cart_gc');
	}

	//testing this if usefull
	public function getGcPickUpItems_mod($cusId)
	{
		$this->db->select('*,cart_gc.product_id as itemCode , cart_gc.quantity as cartQty');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.product_id');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.product_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id' => $value['id'],
				'cart_qty' => $value['cartQty'],
				'product_id' => $value['itemCode'],
				'product_name' => $value['product_name'],
				'product_image' => $this->gcproductImage . $value['image'],
				'product_uom' => $value['UOM'],
				'bu' => $value['business_unit'],
				'price_price' => $value['price_with_vat'],
				'total_price' => number_format($value['price_with_vat'] * $value['cartQty'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBill_mod($cusId)
	{
		$this->db->select('*,SUM(price_with_vat * cart_gc.quantity) as price_vat');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => $value['price_vat']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_getbillperbu_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.product_id AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => $value['price_vat']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcgroupbyBu($cusId)
	{
		// $this->db->distinct();
		$this->db->select('cart_gc.customer_id,cart_gc.buId,loc_bu.business_unit, SUM(gc_prod_price.price_with_vat * cart_gc.quantity) as gcsum');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->group_by('cart_gc.buId');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'buId' => $value['buId'],
				'buName' => $value['business_unit'],
				'total' => $value['gcsum'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getConFee_mod()
	{
		$this->db->select('*');
		$this->db->from('gc_setup_business_rules as gc_setupRules');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'minimum_order_amount' => $value['minimum_order_amount'],
				'pickup_charge' => $value['pickup_charge']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_submitOrder_mod($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $buData, $totalData, $convenienceData, $placeRemarks, $pickUpOrDelivery)
	{
		// dd($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $buData, $totalData, $convenienceData, $placeRemarks);
		$deliveryDateData_arr = str_replace(array("[", "]"), array("", ""), $deliveryDateData);
		$dates  = explode(',', $deliveryDateData_arr);

		$deliveryTime = str_replace(array("[", "]"), array("", ""), $deliveryTimeData);
		$times  = explode(',', $deliveryTime);

		$bus = str_replace(array("[", "]"), array("", ""), $buData);
		$stores  = explode(',', $bus);

		$totalData = str_replace(array("[", "]"), array("", ""), $totalData);
		$totalPerStores  = explode(',', $totalData);

		$convenienceData = str_replace(array("[", "]"), array("", ""), $convenienceData);
		$pickingChargePerStores  = explode(',', $convenienceData);

		$placeRemarks = str_replace(array("[", "]"), array("", ""), $placeRemarks);
		$placeRemarks = explode(',', $placeRemarks);

		$insert_id = null;

		foreach ($stores as $key => $buId) {


			$this->db->select('*,cart_gc.product_id as gcarProdId  , cart_gc.quantity as cartQty');
			$this->db->from('app_cart_gc as cart_gc');
			// $this->db->join('gc_product_prices as gc_prod_price','gc_prod_price.itemcode = cart_gc.product_id AND gc_prod_price.UOM = cart_gc.uom_symbol');
			$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
			// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id ');
			$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
			$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
			$this->db->where('cart_gc.customer_id', $customerId);
			$this->db->where('cart_gc.buId', $buId);

			$query2 = $this->db->get();
			$res2 = $query2->result_array();

			$insert_id = $this->app_cart_today_order($customerId, $pickUpOrDelivery);
			foreach ($res2 as $value) {
				$data = array(
					'ticket_id' => $insert_id,
					'bu_id' => $value['buId'],
					'product_id' => $value['gcarProdId'],
					'uom_id' => $value['uom'],
					'quantity' => $value['cartQty'],
					'price' => $value['price_with_vat'],
					'total_price' => $value['price_with_vat'] * $value['cartQty'],
					'icoos' => '1',
					'pending_status' => '1',
					'canceled_status' => '0',
					'user_id' => $customerId,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('gc_final_order', $data);

				$this->pusher()->trigger("private-grocery-order-submitted.{$buId}", 'App\Events\GroceryOrderSubmitted', array('message' => ''));
			}


			$date = $dates[$key];
			$time = $times[$key];
			$store = $stores[$key];
			$total = $totalPerStores[$key];
			$pickingChargePerStore = $pickingChargePerStores[$key];
			$placeRemark = $placeRemarks[$key];

			$dat = [
				'ticket_id' => $insert_id,
				'bu_id' => $buId,
				'mode_of_order' => 1,
				'order_pickup' => date('Y-m-d H:i:s', strtotime("$date $time")),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$this->db->insert('gc_order_statuses', $dat);

			$dat1 = [
				'ticket_id' => $insert_id,
				'bu_id' => $buId,
				'remarks' => $placeRemark,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$this->db->insert('gc_special_instructions', $dat1);

			$dat2 = [
				'ticket_id' => $insert_id,
				'amount' => $total,
				'picking_charge' => $pickingChargePerStore,
				'change' => '0.00',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];
			$this->db->insert('customer_bills', $dat2);
		}

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('customer_delivery_infos', $infos);
		}

		$this->db->where('app_cart_gc.customer_id', $customerId);
		$this->db->delete('app_cart_gc');
	}


	public function gc_searchProd_mod($search_prod)
	{
		$this->db->select('*');
		$this->db->from('gc_product_items as gc_prod');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = gc_prod.itemcode');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_prod.itemcode AND gc_product_uom.UOM = gc_prod_price.UOM');

		// $this->db->offset($offset);
		$this->db->where('gc_prod.status', 'active');
		$this->db->where('image!=', 'null');
		$this->db->like('product_name', $search_prod, 'both');
		$this->db->order_by('gc_prod_price.price_with_vat', 'asc');
		// $this->db->where('gc_prod.itemcode','131798');	
		// $this->db->order_by('gc_prod_price.price_id','asc');
		$this->db->limit(100);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_name' => $value['product_name'],
				'itemcode' => $value['itemcode'],
				'image'	=> $this->gcproductImage . $value['image'],
				'price'	=> number_format($value['price_with_vat'], 2),
				'uom' => $value['UOM'],
				'uom_id' => $value['uom_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_select_uom_mod($itemCode)
	{
		$this->db->select('*');
		$this->db->from('gc_product_uoms as gc_prod_uom');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_prod_uom.itemcode AND gc_prod_uom.uom_id = gc_prod_price.price_id');
		$this->db->where('gc_prod_uom.itemcode', $itemCode);
		$this->db->where('gc_prod_price.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'UOM' => $value['UOM'],
				'price_with_vat' => $value['price_with_vat']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function showDiscount_mod()
	{
		$this->db->select('*');
		$this->db->from('discount_lists as ds');
		$this->db->where('ds.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'id' =>  $value['id'],
				'discount_name' =>  $value['discount_name'],
				'discount_percent' => $value['discount_percent'] / 100 * 100 . "%"
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function uploadId_mod($userID, $discountId, $name, $idNumber, $imageName)
	{
		$data = array(
			'customer_id' => $userID,
			'discount_id' => $discountId,
			'name'   	 => $name,
			'id_number'	 => $idNumber,
			'image_path' => 'storage/uploads/discount_ids/' . $imageName . '.jpeg',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_discount_storages', $data);
	}

	// public function uploadId1_mod($userID,$discountId,$name,$idNumber,$imageName,$imageBookletName){
	// 	$data = array(
	//    		'customer_id'=> $userID,
	//    		'discount_id'=> $discountId,
	//    		'name'   	 => $name,
	//    		'id_number'	 => $idNumber,
	//    		'image_path' => 'storage/uploads/discount_ids/'.$imageName.'.jpeg',
	//    		'image_booklet_path' => 'storage/uploads/discount_ids/'.$imageBookletName.'.jpeg',
	//         'created_at' => date('Y-m-d H:i:s'),
	//         'updated_at' => date('Y-m-d H:i:s')
	// 	);
	// 	$this->db->insert('customer_discount_storages', $data);
	// }

	public function loadIdList_mod($userID)
	{
		$this->db->select('*,cs_ds.id as cs_id');
		$this->db->from('customer_discount_storages as cs_ds');
		$this->db->join('discount_lists as ds', 'ds.id = cs_ds.discount_id', 'inner');
		$this->db->where('cs_ds.customer_id', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'id' => $value['cs_id'],
				'name' =>  $value['name'],
				'discount_name' =>  $value['discount_name'],
				'dicount_id' => $value['id'],
				'discount_no' => $value['id_number'],
				'discount_percent' => $value['discount_percent'] / 100 * 100 . "%"
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function delete_id_mod($id)
	{
		$this->db->where('customer_discount_storages.id', $id);
		$this->db->delete('customer_discount_storages');
	}

	public function checkidcheckout_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_discount_storages as cs_ds');
		$this->db->where('cs_ds.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkIfHasAddresses_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_addresses as cs_ds');
		$this->db->where('cs_ds.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function changeAccountStat_mod($username)
	{
		$this->db->set('status', '0');
		$this->db->where('username', $username);
		$this->db->update('app_users');
	}

	public function getUserDetails_mod($usernameLogIn)
	{

		$this->db->select('*');
		$this->db->from('app_users as app_us');
		$this->db->where('app_us.username', $usernameLogIn);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'mobile_number' => strval($value['mobile_number'])
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getusernameusingnumber_mod($mobileNumber)
	{
		$this->db->select('*');
		$this->db->from('app_users as app_us');
		$this->db->where('app_us.mobile_number', $mobileNumber);
		$query = $this->db->get();
		$res = $query->row_array();
		return $res['customer_id'];
	}

	public function saveOTPNumber_mod($userID, $my_number, $otp_num)
	{

		$this->db->set('status', '1');
		$this->db->where('contact_num', $my_number);
		$this->db->update('user_verification_codes');

		$data = array(
			'user_id' => $userID,
			'contact_num' => $my_number,
			'otp_code' => $otp_num,
			'status' => '0',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('user_verification_codes', $data);
	}

	public function checkOtpCode_mod($otpCode, $mobileNumber)
	{
		$this->db->select('*');
		$this->db->from('user_verification_codes as usrvrcode');
		$this->db->where('usrvrcode.contact_num', $mobileNumber);
		$this->db->where('usrvrcode.otp_code', $otpCode);
		$this->db->where('usrvrcode.status', '0');
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			$this->changeOtpStatus_mod($otpCode, $mobileNumber);
			echo "true";
		} else {
			echo "false";
		}
	}

	public function changeOtpStatus_mod($otpCode, $mobileNumber)
	{
		$this->db->set('status', '1');
		$this->db->where('contact_num', $mobileNumber);
		$this->db->where('otp_code', $otpCode);
		$this->db->update('user_verification_codes');
	}

	public function changePassword_mod($password2, $realMobileNumber)
	{
		$this->db->set('password2', md5($password2));
		$this->db->set('status', '1');
		$this->db->where('mobile_number', $realMobileNumber);
		$this->db->update('app_users');
	}

	public function checkUsernameIfExist_mod($username)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.username', $username);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkPhoneIfExist_mod($phonenumber)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.mobile_number', '0' . $phonenumber);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}


	public function displayCartAddOns_mod($cartId)
	{
		echo "string";
		// 		$this->db->select('*');
		// 		$this->db->from('app_cart_main as app_c_main');
		// 		$this->db->join('app_cart_drink as appdrink', 'appdrink.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_fries as appfries', 'appfries.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_sides as appside', 'appside.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_addons_side_items as appaddon', 'appaddon.cart_id = app_c_main.id','left');

		// 		$this->db->where('app_c_main.id',$cartId);
		// 		$query = $this->db->get();
		// 		$res = $query->result_array();
		// 		// echo json_encode($res);
		// 		// exit();
		// 		$post_data = array();
		// 		  foreach($res as $value){
		// 			$post_data[] = array(
		// 				'mobile_number' => strval($value['mobile_number'])  
		// 			);
		// 		  }
		// 		$item = array('user_details' => $post_data);
		// 		echo json_encode($item);
	}


	public function getProvince_ctrl()
	{
		$this->db->select('*');
		$this->db->from('province');
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'prov_id' => $value['prov_id'],
				'prov_name' => $value['prov_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}
	// public function getTown_mod($provId)
	// {
	// 	$this->db->select(' *, tblcharges.town_id as town_id');
	// 	$this->db->from('tbl_delivery_charges as tblcharges');
	// 	$this->db->join('towns as twn', 'twn.town_id = tblcharges.town_id', 'inner');
	// 	$this->db->where('twn.prov_id', $provId);
	// 	$this->db->where('twn.status', '1');
	// 		$this->db->group_by('tblcharges.town_id');
	// 	//$this->db->group_by('twn.town_id');	
	// 	$query = $this->db->get();
	// 	$res = $query->result_array();
	// 	$post_data = array();
	// 	foreach ($res as $value) {
	// 		$post_data[] = array(
	// 			'town_id' => $value['town_id'],
	// 			'town_name' => $value['town_name'],
	// 			'bunit_group_id' => $value['bunit_group_id']
	// 		);
	// 	}
	// 	$item = array('user_details' => $post_data);
	// 	echo json_encode($item);
	// }

	public function getTown_mod($provId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tblcharges');
		$this->db->join('towns as twn', 'twn.town_id = tblcharges.town_id', 'inner');
		$this->db->where('twn.prov_id', $provId);
		$this->db->where('tblcharges.status', '1');
		//$this->db->group_by('tblcharges.town_id');
		$this->db->group_by('tblcharges.town_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' => $value['town_name'],
				'bunit_group_id' => $value['bunit_group_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBarangay_mod($townID)
	{
		$this->db->select('*');
		$this->db->from('barangays');
		$this->db->where('town_id', $townID);
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'brgy_id' => $value['brgy_id'],
				'brgy_name' => $value['brgy_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function selectBuildingType_mod()
	{
		$this->db->select('*');
		$this->db->from('building_type');
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'buildingID' => $value['buildingID'],
				'buildingName' => $value['buildingName']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function submitNewAddress_mod($userID, $firstName, $lastName, $mobileNum, $houseUnit, $streetPurok, $landMark, $barangayID, $buildingID)
	{
		$data = array(
			'customer_id' => $userID,
			'firstname' => $firstName,
			'lastname' => $lastName,
			'mobile_number' => $mobileNum,
			'barangay_id' => $barangayID,
			'complete_address' => $houseUnit,
			'land_mark' => $landMark,
			'address_type' => $buildingID,
			'street_purok' => $streetPurok,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_addresses', $data);
	}

	public function loadAddress_mod($cusId)
	{
		$this->db->select('*,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
		$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.brgy_id = cust_add.barangay_id', 'left');
		$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
		$this->db->where('cust_add.customer_id', $cusId);
		$this->db->where('tblcharges.vtype', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		if (count($res) == 0) {
			$this->db->select('*,cust_add.id as csid,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname');
			$this->db->from('customer_addresses as cust_add');
			$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
			$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
			$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
			$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
			$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.town_id = twn.town_id', 'left');
			$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
			$this->db->where('cust_add.customer_id', $cusId);
			$this->db->where('tblcharges.vtype', '1');
			$query2 = $this->db->get();
			$res2 = $query2->result_array();
			$post_data = array();
			foreach ($res2 as $value) {
				$post_data[] = array(
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {
			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function deleteAddress_mod($id)
	{
		$this->db->where('customer_addresses.id', $id);
		$this->db->delete('customer_addresses');
	}

	public function showRiderDetails_mod($ticketId)
	{
		$this->db->select('*,rider_data.id as rider_id');
		$this->db->from('tickets as tik');
		$this->db->join('toms_tag_riders as tag_rider', 'tag_rider.ticket_id = tik.id', 'inner');
		$this->db->join('toms_riders_data as rider_data', 'rider_data.id = tag_rider.rider_id', 'inner');
		$this->db->where('tik.ticket', $ticketId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'r_picture' =>  $this->cssadmin . $value['r_picture'],
				'r_firstname' =>  $value['r_firstname'],
				'r_lastname' =>  $value['r_lastname'],
				'rm_picture' =>  $this->cssadmin . $value['rm_picture'],
				'rm_brand' =>  $value['rm_brand'],
				'rm_color' => $value['rm_color'],
				'rm_plate_no' => $value['rm_plate_num'],
				'rm_mobile_no' => $value['r_mobile'],
				'rm_id' => $value['rider_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updateDefaultShipping_mod($id, $customerId)
	{
		$this->db->set('shipping', '0');
		$this->db->where('customer_id', $customerId);
		$this->db->update('customer_addresses');

		$this->db->set('shipping', '1');
		$this->db->where('id', $id);
		$this->db->update('customer_addresses');
	}

	public function pusher()
	{
		$app_id = '1106021';
		$app_key = '41ffaa2dad5288031ed1';
		$app_secret = 'ffe4eb654395eb6325c0';
		$app_cluster = 'ap1';
		$pusher = new Pusher\Pusher($app_key, $app_secret, $app_id, array('cluster' => $app_cluster));
		return $pusher;
	}

	public function viewTenantCategories_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_categories as fd_cat');
		$this->db->where('fd_cat.tenant_id', $tenant_id);
		$this->db->where('fd_cat.active', '1');
		$this->db->group_by('fd_cat.category');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'category_id' => $value['category_id'],
				'category' => $value['category'],
				'image' => $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function viewAddon_mod($cusId)
	{

		$this->db->select('*,appcart.productId as appcartproductId,side_id.side_id as side_id,side_id.side_uom as side_uom,fries_id.fries_id as fries_id,fries_id.fries_uom as fries_uom,appcart.uom as productUom,appcart.tenantId as tenant_id,appcart.flavor as flavor_id,appcart.productId as productId,drink_id.drink_id as drink_id,drink_id.drink_uom as drink_uom,appcart.quantity as cart_qty,appcart.id as d_id,fd_flavors.addon_price as flavor_price,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');
		$this->db->from('app_cart_main as appcart');

		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');

		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = appcart.buId', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = appcart.tenantId', 'left');

		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND IFNULL(main_prod_price.uom_id,0) = IFNULL(appcart.uom,0)', 'left');

		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND IFNULL(fd_drink_price.uom_id,0) = IFNULL(drink_id.drink_uom,0)', 'left');

		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND IFNULL(fd_fries_price.uom_id,0) = IFNULL(fries_id.fries_uom,0)', 'left');

		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND IFNULL(fd_side_price.uom_id,0) = IFNULL(side_id.side_uom,0)', 'left');

		$this->db->where('appcart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$totalitemprice = 0;
		$totalPayablePrice = 0;

		$tenant_ids = [];
		$post_data = array();
		foreach ($res as $value) {
			$totalitemprice = $value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'];
			$totalPayablePrice = ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'];
			$tenant_ids[] = $value['tenant_id'];
			$post_data[] = array(
				'appcartproductId' => $value['appcartproductId'],
				'd_productId' => $value['productId'],
				'd_productUom' => $value['productUom'],
				'd_flavor_id' => $value['flavor_id'],
				'flavor_price' => $value['flavor_price'],
				'd_drink_id' => $value['drink_id'],
				'd_drink_uom' => $value['drink_uom'],
				'd_fries_id' => $value['fries_id'],
				'd_fries_uom' => $value['fries_uom'],
				'd_side_id' => $value['side_id'],
				'd_side_uom' => $value['side_uom'],
				'd_id' => $value['d_id'],
				'prod_name' => $value['prod_name'],
				'cart_qty' => $value['cart_qty'],
				'loc_bu_name' => $value['loc_bu_name'],
				'loc_tenant_name' => $value['loc_tenant_name'],
				'flavor_price' => $value['flavor_price'],
				'prod_price' => $value['prod_price'],
				'drink_name' => $value['drink_name'],
				'drink_price' => $value['drink_price'],
				'fries_name' => $value['fries_name'],
				'fries_price' => $value['fries_price'],
				'side_name' => $value['side_name'],
				'side_price' => $value['side_price'],
				'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	function checkIfBf_mod($userID)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id');
		$this->db->where('temp_orders.customerId', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$now = strtotime(date('H:i:s'));
		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = $avail;
		}

		$xb = [];

		$xb[] = ['isavail' => !in_array(false, $post_data)];

		$item = array('user_details' => $xb);
		echo json_encode($item);
	}

	public function getTotalFee_mod($ticket_id)
	{
		$this->db->select('*');
		$this->db->from('tickets as tik');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = tik.id', 'inner');
		$this->db->where('tik.ticket', $ticket_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				"amount" => $value['amount'],
				"delivery_charge" => $value['delivery_charge']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getglobalcat_mod()
	{
		$this->db->select('*');
		$this->db->from('global_categories as global_cat');
		$this->db->where('global_cat.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'id'			=> $value['id'],
				'category' 		=> $value['category'],
				'cat_picture'	=> $this->buImage . $value['cat_picture']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function search_item_mod($query, $unitGroupId)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->like('fd_prod.product_name', $query, 'both');
		$this->db->where('loc_bu.group_id', $unitGroupId);
		$this->db->limit(30);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'tenant_name' => $value['tenant'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'prod_bu' => $value['business_unit'],
				'bu_id' => $value['bunit_code']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function searchGc_item_mod($query, $unitGroupId)
	{
		$this->db->select('*, gc_prod.product_id as prod_id');
		$this->db->from('gc_product_items as gc_prod');
		$this->db->limit(30);
		// ->offset($offset);
		$this->db->like('gc_prod.product_name', $query);


		$products = $this->db->where('gc_prod.status', 'active')
			->where('image!=', 'null')
			->get()
			->result();
		$user_details = array_map(function (object $product) {
			$with = $this->productLeastUOMPrice($product);
			if (!empty($with)) {
				$product->prod_id = $product->product_id;
				$product->image = $this->gcproductImage . $product->image;
				$product->uom = $with->UOM;
				$product->price = number_format($with->price_with_vat, 2);
				$product->uom_id = $with->uom_id;
			}
			return $product;
		}, $products);
		echo json_encode(compact('user_details'));
	}

	public function updatePassword_mod($cusId, $currentpass, $newpass)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.customer_id', $cusId);
		$this->db->where('appsu.password2', md5($currentpass));
		$query = $this->db->get();
		$res = $query->row_array();
		if (empty($res)) {
			echo "wrongPass";
		} else {
			$this->db->set('password2', md5($newpass));
			$this->db->where('customer_id', $cusId);
			$this->db->update('app_users');
		}
	}

	public function chat_mod($from, $to)
	{

		$this->db->select();
		$this->db->from('messages as mes');

		$this->db->where('contact_type_from', 'CUSTOMER');
		$this->db->where('mes.from_id', $from);
		$this->db->where('mes.contact_type_to', 'RIDER');
		$this->db->where('mes.to_id', $to);

		$this->db->or_where('contact_type_from', 'RIDER');
		$this->db->where('mes.from_id', $to);
		$this->db->where('mes.contact_type_to', 'CUSTOMER');
		$this->db->where('mes.to_id', $from);


		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		foreach ($res as $value) {
			$f = 'false';
			if ($value['from_id'] == $from && $value['contact_type_from'] == 'CUSTOMER') {
				$f = 'true';
			}

			$post_data[] = array(
				'body'	=> $value['body'],
				'isSender'  => $f,
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function send_chat_mod($from, $to, $message)
	{
		$data = array(
			'contact_type_from' => 'CUSTOMER',
			'from_id' => $from,
			'contact_type_to' => 'RIDER',
			'to_id' => $to,
			'body' => $message,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('messages', $data);
	}
}
