<?php
defined('BASEPATH') or exit('No direct script access allowed');

define('SECRET_KEY', 'SoAxVBnw8PYHzHHTFBQdG0MFCLNdmGFf');
define('SECRET_IV', 'T1g994xo2UAqG81M');
define('ENCRYPT_METHOD', 'AES-256-CBC');

class AppController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
		$this->load->model('AppModel');
	}

	public function decrypt($string)
	{
		return openssl_decrypt($string, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
	}

	public function appCreateAccountCtrl()
	{
		// if(isset($_POST['townId']) && isset($_POST['barrioId']) && isset($_POST['username']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['suffix']) && isset($_POST['password']) && isset($_POST['birthday']) && isset($_POST['contactNumber'])){

		$this->AppModel->appCreateAccountMod($this->decrypt($_POST['townId']), $this->decrypt($_POST['barrioId']), $this->decrypt($_POST['username']), $this->decrypt($_POST['firstName']), $this->decrypt($_POST['lastName']), $this->decrypt($_POST['suffix']), $this->decrypt($_POST['password']), $this->decrypt($_POST['birthday']), $this->decrypt($_POST['contactNumber']));
		// }
	}

	public function checkLoginCtrl()
	{
		// if (isset($_POST['_usernameLogIn']) && isset($_POST['_passwordLogIn'])) {
		// 	$this->AppModel->signInUserMod($this->decrypt($_POST['_usernameLogIn']), $this->decrypt($_POST['_passwordLogIn']));
		$this->AppModel->signInUserMod('jen', '12345');
		// }
	}

	public function getUserDataCtrl()
	{
		//if (isset($_POST['id'])) {
		// $this->AppModel->getUserDataMod($_POST['id']);
		$this->AppModel->getUserDataMod('368');
		//}

	}

	public function getPlaceOrderDataCtrl()
	{
		// if (isset($_POST['cusId'])) {
		// 	$this->AppModel->getPlaceOrderDataMod($this->decrypt($_POST['cusId']));
		// }
		$this->AppModel->getPlaceOrderDataMod('368');
	}

	public function checkAllowedPlace_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->checkAllowedPlaceMod($_POST['townId']);
		}
	}

	public function checkFee_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->checkFeeMod($_POST['townId']);
			// $this->AppModel->checkFeeMod('6');
		}
	}

	public function getOrderDataCtrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getOrderDataMod($_POST['cusId']);
			// $this->AppModel->getOrderDataMod('2282');
		}
	}

	public function getSubtotalCtrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getSubtotalMod($_POST['customerId']);
			// $this->AppModel->getSubtotalMod('1628');
		}
	}

	public function placeOrder_ctrl()
	{
		$this->AppModel->placeOrder_delivery_mod(
			// $_POST['cusId'],
			// $_POST['deliveryDate'],
			// $_POST['deliveryTime'],
			// $_POST['selectedDiscountType'],
			// $_POST['deliveryCharge'],
			// $_POST['changeFor']
			'368',
			'38',
			'38448',
			'4',
			'5',
			'6'
			// '7',
			// '8',
			// '9',
			// '10',
			// '11',
			// '1',
			// '1'
		);
	}


	public function getLastItems_ctrl()
	{
		if (isset($_POST['orderNo'])) {
			$this->AppModel->getLastItems_mod($_POST['orderNo']);
		}
	}

	public function getAllowedLoc_ctrl()
	{
		if (isset($_POST['d'])) {
			$this->AppModel->getAllowedLoc_mod();
		}
	}

	public function getBu_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBu_mod($_POST['cusId']);
		}
	}

	public function getBu_ctrl1()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBu_mod1($_POST['cusId']);
		}
		// $this->AppModel->getBu_mod1('2423');
	}

	public function getTenant_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getTenant_mod($_POST['cusId']);
			// 	// $this->AppModel->getTenant_mod('163');
		}
		// $this->AppModel->getTenant_mod('2423');
	}


	public function getTicketNoFood_ctrl()
	{
		// if (isset($_POST['cusId'])) {
		// 	$this->AppModel->getTicketNoFood_mod($_POST['cusId']);
		// }
		$this->AppModel->getTicketNoFood_mod('2423');
	}

	public function getTicketNoFood_ontrans_ctrl()
	{
		// if(isset($_POST['cusId'])){
		// 	$this->AppModel->getTicketNoFood_ontrans_mod($_POST['cusId']);
		// }
		$this->AppModel->getTicketNoFood_ontrans_mod('2423');
	}


	public function getTicketNoFood_delivered_ctrl()
	{
		// if(isset($_POST['cusId'])){
		// 	$this->AppModel->getTicketNoFood_delivered_mod($_POST['cusId']);
		// }
		$this->AppModel->getTicketNoFood_delivered_mod('2423');
	}

	public function getTicket_cancelled_ctrl()
	{
		// if(isset($_POST['cusId'])){
		// 	$this->AppModel->getTicketNoFood_delivered_mod($_POST['cusId']);
		// }
		$this->AppModel->getTicket_cancelled_mod('2423');
	}




	// public function getTicketNoGood_ctrl(){
	// 		$this->AppModel->getTicketNoGood_mod($_POST['cusId']);
	// 		// $this->AppModel->getTicketNoGood_mod('465');
	// }

	public function loadProfile_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadProfile_mod($_POST['cusId']);
		}
		// $this->AppModel->loadProfile_mod('2423');
	}

	public function lookItems_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookItems_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_mod('210705-2-001');
	}

	public function lookItems_segregate_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookItems_segregatemod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_segregatemod('210104-2-001');
	}

	public function lookitems_good_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookitems_good_mod($this->decrypt($_POST['ticketNo']));
			// $this->AppModel->lookitems_good_mod('210420-2-001');
		}
	}

	public function loadCartData_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadCartData_mod($_POST['cusId']);
			// $this->AppModel->loadCartData_mod('1628');
		}
	}


	public function loadCartDataNew_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadCartDataNew_mod($_POST['cusId']);
			// $this->AppModel->loadCartDataNew_mod(3822);
		}
	}


	public function loadCartData_sides_ctrl()
	{
		$this->AppModel->loadCartData_sides_mod('163'); //tiwason pd
	}

	public function clearCustomerCartPerItem_ctrl()
	{
		if (isset($_POST['cartId'])) {
			$this->AppModel->clearCustomerCartPerItem($_POST['cartId']);
			// $this->AppModel->removeItemFromCart_mod('179','163');
		}
	}

	public function displayOrder_ctrl()
	{
		if (isset($_POST['cusId']) && isset($_POST['tenantId'])) {
			$this->AppModel->displayOrder_mod($_POST['cusId'], $_POST['tenantId']);
			// $this->AppModel->displayOrder_mod('163','9');
		}
	}

	public function trapTenantLimit_ctrl()
	{
		if (isset($_POST['cusId']) && isset($_POST['townId'])) {
			$this->AppModel->trapTenantLimit_mod($_POST['townId'], $_POST['cusId']);
			// $this->AppModel->trapTenantLimit_mod('1','1628');
		}
	}


	public function getAmountPertenant_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getAmountPertenant_mod($_POST['cusId']);
			// $this->AppModel->getAmountPertenant_mod('163');
		}
	}

	public function getTenant_perbu_ctrl()
	{
		if (isset($_POST['buId'])) {
			$this->AppModel->getTenant_perbu_mod($_POST['buId']);
		}
	}

	//node

	public function display_store_ctrl()
	{
		//	if (isset($_POST['unitGroupId'])) {
		$this->AppModel->display_store_mod($_POST['unitGroupId'], $_POST['globalCatID']);
		//	}
		// $this->AppModel->display_store_mod('1','1');
	}

	public function display_tenant_ctrl()
	{
		$this->AppModel->display_tenant_mod($_POST['buCode']);
	}

	public function display_restaurant_ctrl()
	{
		if (isset($_POST['categoryId'])) {
			$this->AppModel->display_restaurant_mod($_POST['categoryId']);
			// $this->AppModel->display_restaurant_mod('27');
		}
	}

	public function display_item_data_ctrl()
	{
		if (isset($_POST['prodId']) && isset($_POST['productUom'])) {
			$this->AppModel->display_item_data_mod($_POST['prodId'], $_POST['productUom']);
		}
		// $this->AppModel->display_item_data_mod('97', null);
	}

	public function add_to_cart_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->add_to_cart_mod(
				$_POST['customerId'],
				$_POST['buCode'],
				$_POST['tenantCode'],
				$_POST['prodId'],
				$_POST['productUom'],
				$_POST['flavorId'],
				$_POST['drinkId'],
				$_POST['drinkUom'],
				$_POST['friesId'],
				$_POST['friesUom'],
				$_POST['sideId'],
				$_POST['sideUom'],
				$_POST['selectedSideItems'],
				$_POST['selectedSideItemsUom'],
				$_POST['selectedDessertItems'],
				$_POST['selectedDessertItemsUom'],
				$_POST['_counter']
			);
		}
		// $this->AppModel->add_to_cart_mod(1,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
	}

	public function addToCartNew_ctrl()
	{
		$this->AppModel->addToCartNew_mod(
			$_POST['userID'],
			$_POST['prodId'],
			$_POST['uomId'],
			$_POST['uomPrice'],
			$_POST['choiceUomId'],
			$_POST['choiceId'],
			$_POST['choicePrice'],
			$_POST['flavorId'],
			$_POST['flavorPrice'],
			$_POST['_counter'],
			$_POST['addonData'],
			$_POST['selectedSideOnPrice'],
			$_POST['selectedSideItems'],
			$_POST['selectedSideItemsUom']

			// '2423',
			// '105',
			// null,
			// '30',
			// '30',
			// '30',
			// '30',
			// '30',
			// '30',
			// '3',
			// '[1,2,3]',
			// '[1,2,3]',
			// '[1,2,3]',
			// '[1,2,3]'
		);
	}

	public function selectSuffix_ctrl()
	{
		$this->AppModel->selectSuffix_mod();
	}

	public function getTowns_ctrl()
	{
		$this->AppModel->getTowns_mod();
	}

	public function getbarrio_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->getbarrio_mod($_POST['townId']);
		}
	}


	//

	public function updateCartQty_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateCartQty_mod($this->input->post('id'), $this->input->post('qty'));
			// $this->AppModel->updateCartQty_mod(36, 12);
		}
	}

	public function getCounter_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getCounter_mod($this->input->post('customerId'));
		}
	}

	public function savePickup_ctrl()
	{
		// if(isset($_POST['customerId']) && isset($_POST['groupValue']) && isset($_POST['deliveryDateData']) && isset($_POST['deliveryTimeData']) && isset($_POST['getTenantData']) && isset($_POST['subtotal']) && isset($_POST['tender']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['contactNo'])){

		$this->AppModel->placeOrder_mod(
			$this->decrypt($_POST['customerId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['subtotal']),
			$this->decrypt($_POST['tender']),
			$this->decrypt($_POST['selectedDiscountType'])
			// '2282',
			// '1',
			// '1',
			// '1',
			// '1',
			// '1',
			// '1'
		);
	}

	public function loadSubTotal_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadSubTotal_mod($_POST['userID']);
		}
	}

	public function loadSubTotalnew_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadSubTotalnew_mod($_POST['customerId']);
		}
		// $this->AppModel->loadSubTotalnew_mod('2423');
	}

	public function xsample()
	{
		$this->AppModel->getMainOrders('2282');
	}

	// public function loadRiderDetails_ctrl(){
	// 	$this->AppModel->loadRiderDetails_mod($this->input->post('ticketNo'));
	// 	// $this->AppModel->loadRiderDetails_mod('201125-2-003');
	// }


	public function getTrueTime_ctrl()
	{
		$this->AppModel->getTrueTime_mod();
	}

	public function listenCartSubtotal_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->listenCartSubtotal_mod($this->input->post('customerId'));
		}
	}

	public function loadFlavor_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadFlavor_mod($this->input->post('prodId'));
		}
	}

	public function loadDrinks_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadDrinks_mod($this->input->post('prodId'));
		}
		// $this->AppModel->loadDrinks_mod('111');
	}

	public function loadFries_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadFries_mod($this->input->post('prodId'));
		}
	}

	public function loadSide_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadSide_mod($this->input->post('prodId'));
		}
	}

	public function checkAddon_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->checkAddon_mod($this->input->post('prodId'));
		}
	}

	public function loadAddonSide_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadAddonSide_mod($this->input->post('prodId'));
		}
	}

	public function loadAddonDessert_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadAddonDessert_mod($this->input->post('prodId'));
		}
	}

	public function cancelOrderSingleFood_ctrl()
	{
		if (isset($_POST['tomsId']) && isset($_POST['ticketId'])) {
			$this->AppModel->cancelOrderSingleFood_mod($this->input->post('tomsId'), $this->input->post('ticketId'));
		}

		// $this->AppModel->cancelOrderSingleFood_mod('6693','4185');
	}

	public function cancelOrderSingleGood_ctrl()
	{
		if (isset($_POST['tomsId']) && isset($_POST['ticketId'])) {
			$this->AppModel->cancelOrderSingleGood_mod($this->input->post('tomsId'), $this->input->post('ticketId'));
		}
	}

	public function loadLocation_ctrl()
	{
		$this->AppModel->loadLocation_mod('1');
	}

	public function displayAddOns_ctrl()
	{
		if (isset($_POST['prodId'])) {
			// $this->AppModel->displayAddOns_mod('90');
			$this->AppModel->displayAddOns_mod($this->input->post('cartId'));
		}
	}

	public function showFlavor_ctrl()
	{
		$this->AppModel->showFlavor_mod('103');
	}

	public function showDrinks_ctrl()
	{
		$this->AppModel->showDrinks_ctrl('103');
	}

	public function getTotal_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getTotal_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function checkifongoing_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->checkifongoing_mod($_POST['ticketNo']);
			// $this->AppModel->checkifongoing_mod('210415-2-003');
		}
	}

	public function viewCategories_ctrl()
	{
		$this->AppModel->viewCategories_mod('1');
	}

	public function checkifemptystore_ctrl()
	{

		if (isset($_POST['tenantCode'])) {

			// $this->AppModel->checkifemptystore_mod('14');
			$this->AppModel->checkifemptystore_mod($_POST['tenantCode']);
		}
	}

	public function getCategories_ctrl()
	{
		if (isset($_POST['tenantCode'])) {
			$this->AppModel->getCategories_mod($_POST['tenantCode']);
		}
	}


	public function getItemsBycategories_ctrl()
	{
		if (isset($_POST['categoryId'])) {
			$this->AppModel->getItemsBycategories_mod($_POST['categoryId']);
			// $this->AppModel->getItemsBycategories_mod('27');
		}
	}

	public function getItemsByCategoriesAll_ctrl()
	{
		if (isset($_POST['tenantCode'])) {
			$this->AppModel->getItemsByCategoriesAll_mod($_POST['tenantCode']);
			// $this->AppModel->getItemsBycategories_mod('32');
		}
	}

	public function getGcItems_ctrl()
	{
		if (isset($_POST['offset']) &&  isset($_POST['categoryNo']) && isset($_POST['itemSearch'])) {
			$this->AppModel->getGcItems_mod($_POST['offset'], $_POST['categoryNo'], $_POST['itemSearch']);
			// $this->AppModel->getGcItems_mod('10', '130', '');
		}
	}


	public function addToCartGc_ctrl()
	{
		if (isset($_POST['userID']) && isset($_POST['userID']) && isset($_POST['buCode']) && isset($_POST['prodId']) && isset($_POST['itemCode']) && isset($_POST['uomSymbol']) && isset($_POST['uom'])  && isset($_POST['_counter'])) {
			$this->AppModel->addToCartGc_mod($_POST['userID'], $_POST['buCode'], $_POST['prodId'], $_POST['itemCode'], $_POST['uomSymbol'], $_POST['uom'], $_POST['_counter']);
			// $this->AppModel->addToCartGc_mod('12121','12','12','12','1');
		}
	}

	public function gc_cart_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->gc_cart_mod($_POST['userID']);
			// $this->AppModel->gc_cart_mod('1628');
		}
	}

	public function updateGcCartQty_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateGcCartQty_mod($this->input->post('id'), $this->input->post('qty'));
		}
	}


	public function loadGcSubTotal_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadGcSubTotal_mod($_POST['customerId']);
			// $this->AppModel->loadGcSubTotal_mod('2423');
		}
	}

	public function getGcCounter_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getGcCounter_mod($_POST['customerId']);
			// $this->AppModel->getGcCounter_mod('378');
		}
	}


	public function getGcCategories_ctrl()
	{
		$this->AppModel->getGcCategories_mod();
	}

	public function getItemsByGcCategories_ctrl()
	{
		if (isset($_POST['categoryId']) && isset($_POST['offset'])) {
			$this->AppModel->getItemsByGcCategories_mod($_POST['categoryId'], $_POST['offset']);
		}
	}

	public function removeGcItemFromCart_r()
	{
		if (isset($_POST['cartId'])) {
			$this->AppModel->removeGcItemFromCart_mod($_POST['cartId']);
		}
	}


	public function getBill_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getBill_mod($_POST['customerId']);
			// $this->AppModel->getBill_mod('1628');
		}
	}

	public function gc_getbillperbu_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getBill_mod($_POST['customerId']);
			// $this->AppModel->gc_getbillperbu_mod('1628');
		}
	}

	public function gcgroupbyBu()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gcgroupbyBu($this->decrypt($_POST['customerId']));
			// $this->AppModel->gcgroupbyBu('1628');
		}
	}


	public function getConFee_ctrl()
	{
		$this->AppModel->getConFee_mod();
	}

	public function gc_submitOrder_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gc_submitOrder_mod(
				$_POST['customerId'],
				$_POST['groupValue'],
				$_POST['deliveryDateData'],
				$_POST['deliveryTimeData'],
				$_POST['buData'],
				$_POST['totalData'],
				$_POST['convenienceData'],
				$_POST['placeRemarks'],
				$_POST['pickUpOrDelivery']
			);
		}
	}

	public function gc_searchProd_ctrl()
	{
		if (isset($_POST['search_prod'])) {
			$this->AppModel->gc_searchProd_mod($_POST['search_prod']);
			// $this->AppModel->gc_searchProd_mod('ORANGE');
		}
	}

	public function gc_select_uom_ctrl()
	{
		if (isset($_POST['itemCode'])) {
			// $this->AppModel->gc_select_uom_mod('100462');
			$this->AppModel->gc_select_uom_mod($_POST['itemCode']);
		}
	}

	public function showDiscount_ctrl()
	{
		$this->AppModel->showDiscount_mod();
	}

	public function uploadId_ctrl()
	{
		if (isset($_POST['userID']) && isset($_POST['discountId']) && isset($_POST['name']) && isset($_POST['idNumber']) && isset($_POST['imageName'])) {
			$this->AppModel->uploadId_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['discountId']), $this->decrypt($_POST['name']), $this->decrypt($_POST['idNumber']), $this->decrypt($_POST['imageName']));
		}
	}


	// public function uploadId1_ctrl(){
	// 	if(isset($_POST['userID']) && isset($_POST['discountId']) && isset($_POST['name']) && isset($_POST['idNumber']) && isset($_POST['imageName']) && isset($_POST['imageBookletName'])){
	// 		$this->AppModel->uploadId1_mod($this->decrypt($_POST['userID']),$this->decrypt($_POST['discountId']),$this->decrypt($_POST['name']),$this->decrypt($_POST['idNumber']),$this->decrypt($_POST['imageName']),$this->decrypt($_POST['imageBookletName']));
	// 	}
	// }

	public function upLoadImage_ctrl()
	{
		$imageName = $_POST['_imageName'];
		$image = $_POST['_image'];
		$this->base64_to_jpeg($image, $imageName);
	}

	public function base64_to_jpeg($base64_string, $output_file)
	{
		file_put_contents('storage/uploads/discount_ids/' . $output_file . '.jpeg', base64_decode($base64_string));
		return $output_file;
	}



	public function loadIdList_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadIdList_mod($_POST['userID']);
		}
	}

	public function delete_id_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->delete_id_mod($_POST['id']);
		}
	}

	public function checkidcheckout_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkidcheckout_mod($_POST['userID']);
			// $this->AppModel->checkidcheckout_mod("465");
		}
	}

	public function checkIfHasAddresses_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkIfHasAddresses_mod($this->decrypt($_POST['userID']));
		}
	}


	public function changeAccountStat_ctrl()
	{
		if (isset($_POST['usernameLogIn'])) {
			$this->AppModel->changeAccountStat_mod($_POST['usernameLogIn']);
		}
	}

	public function getUserDetails_ctrl()
	{
		if (isset($_POST['usernameLogIn'])) {
			$this->AppModel->getUserDetails_mod($_POST['usernameLogIn']);
			// $this->AppModel->getUserDetails_mod('pj');
		}
	}

	public function getusernameusingnumber_ctrl($mobileNumber)
	{
		$userid = $this->AppModel->getusernameusingnumber_mod($mobileNumber);
		return $userid;
	}


	public function saveOTPNumber_ctrl()
	{
		if (isset($_POST['mobileNumber'])) {
			$data = array();
			$data_result = array();
			$otp_num = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
			$apicode = 'PR-ALTUR166130_RHH2A';
			$passwd = '9)h!tc%#y$';
			$my_number = $_POST['mobileNumber'];

			//Save data to user_verification_codes table...
			$userID = $this->getusernameusingnumber_ctrl($my_number);
			$this->AppModel->saveOTPNumber_mod($userID, $my_number, $otp_num);

			$message =  "ALTURUSH DELIVERY TO RECOVER YOUR ACCOUNT," . $otp_num . ".";
			$this->itexmo($my_number, $message, $apicode, $passwd);
		}
	}

	public function itexmo($number, $message, $apicode, $passwd)
	{
		$ch = curl_init();
		$itexmo = array('1' => $number, '2' => $message, '3' => $apicode, 'passwd' => $passwd);
		curl_setopt($ch, CURLOPT_URL, "https://www.itexmo.com/php_api/api.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			http_build_query($itexmo)
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($ch);
		curl_close($ch);
	}


	public function checkOtpCode_ctrl()
	{
		if (isset($_POST['otpCode']) && isset($_POST['mobileNumber'])) {
			$this->AppModel->checkOtpCode_mod($this->decrypt($_POST['otpCode']), $this->decrypt($_POST['mobileNumber']));
		}
		// $this->AppModel->checkOtpCode_mod('316626','09107961118');
	}

	public function changePassword_ctrl()
	{
		if (isset($_POST['newPassWord']) && isset($_POST['realMobileNumber'])) {
			// $this->AppModel->changePassword_mod($this->decrypt('1212121212'),'09107961118');
			$this->AppModel->changePassword_mod($this->decrypt($_POST['newPassWord']), $this->decrypt($_POST['realMobileNumber']));
		}
	}

	public function checkUsernameIfExist_ctrl()
	{
		if (isset($_POST['username'])) {
			$this->AppModel->checkUsernameIfExist_mod($this->decrypt($_POST['username']));
		}
	}

	public function checkPhoneIfExist_ctrl()
	{
		if (isset($_POST['phoneNumber'])) {
			$this->AppModel->checkPhoneIfExist_mod($this->decrypt($_POST['phoneNumber']));
		}
		// $this->AppModel->checkPhoneIfExist_mod('9107961118');
	}

	public function displayCartAddOns_ctrl()
	{
		$this->AppModel->displayCartAddOns_mod('51');
	}

	public function getProvince_ctrl()
	{
		$this->AppModel->getProvince_ctrl();
	}

	public function getTown_ctrl()
	{
		//if (isset($_POST['provinceId'])) {
		$this->AppModel->getTown_mod($this->decrypt($_POST['provinceId']));
		//}
		// $this->AppModel->getTown_mod('1');		
	}


	public function getBarangay_ctrl()
	{
		if (isset($_POST['townID'])) {
			$this->AppModel->getBarangay_mod($this->decrypt($_POST['townID']));
		}
	}

	public function selectBuildingType_ctrl()
	{
		$this->AppModel->selectBuildingType_mod();
	}

	public function submitNewAddress_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->submitNewAddress_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['firstName']), $this->decrypt($_POST['lastName']), $this->decrypt($_POST['mobileNum']), $this->decrypt($_POST['houseUnit']), $this->decrypt($_POST['streetPurok']), $this->decrypt($_POST['landMark']), $this->decrypt($_POST['barangayID']), $this->decrypt($_POST['buildingID']));
		}
	}

	public function loadAddress_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadAddress_mod($this->decrypt($_POST['userID']));
		}
		// $this->AppModel->loadAddress_mod('2279');
	}

	public function deleteAddress_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->deleteAddress_mod($this->decrypt($_POST['id']));
		}
	}

	public function showRiderDetails_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->showRiderDetails_mod($this->decrypt($_POST['ticketNo']));
		}
	}

	public function updateDefaultShipping_ctrl()
	{
		if (isset($_POST['id']) && isset($_POST['customerId'])) {
			$this->AppModel->updateDefaultShipping_mod($this->decrypt($_POST['id']), $this->decrypt($_POST['customerId']));
		}
	}

	public function viewTenantCategories_ctrl()
	{
		if (isset($_POST['tenantId'])) {
			$this->AppModel->viewTenantCategories_mod($this->decrypt($_POST['tenantId']));
		}
		// $this->AppModel->viewTenantCategories_mod(2);
	}


	public function checkIfBf_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkIfBf_mod($this->decrypt($_POST['userID']));
		}
	}

	public function viewAddon_ctrl()
	{
		echo $this->decrypt('123');
		// $this->AppModel->viewAddon_mod('2282');
	}

	public function getTotalFee_ctrl()
	{
		if (isset($_POST['ticketID'])) {
			$this->AppModel->getTotalFee_mod($_POST['ticketID']);
		}
	}

	public function getglobalcat_ctrl()
	{
		$this->AppModel->getglobalcat_mod();
	}

	public function search_item_ctrl()
	{
		if (isset($_POST['search'])) {
			$this->AppModel->search_item_mod($_POST['search'], $_POST['unitGroupId']);
		}
		// $this->AppModel->search_item_mod("hotdog","1");
	}

	public function searchGc_item_ctrl()
	{
		if (isset($_POST['search'])) {
			$this->AppModel->searchGc_item_mod($_POST['search'], "1");
		}
	}

	public function updatePassword_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->updatePassword_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['currentPass']), $this->decrypt($_POST['oldPassword']));
		}
		// $this->AppModel->updatePassword_mod('2423','12345','123451');
	}

	public function chat_ctrl()
	{
		$this->AppModel->chat_mod($_POST['userID'], $_POST['riderId']);
		// $this->AppModel->chat_mod('344','35');
	}

	public function send_chat_ctrl()
	{
		$this->AppModel->send_chat_mod($_POST['userID'], $_POST['riderId'], $_POST['chat']);
	}

	public function uploadProfilePic_ctrl()
	{
		file_put_contents('torage/uploads/profilePhotos/' . $_POST['userID'] . '.jpeg', base64_decode($_POST['base64Image']));
		// return $output_file;
	}
}
