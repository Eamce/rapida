<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['createUser_r'] = 'AppController/appCreateAccountCtrl';
$route['checkLogin_r'] = 'AppController/checkLoginCtrl';
$route['getUserData_r'] = 'AppController/getUserDataCtrl';
$route['getPlaceOrderData_r'] = 'AppController/getPlaceOrderDataCtrl';
$route['getOrderData_r'] = 'AppController/getOrderDataCtrl';
$route['getSubtotal_r'] = 'AppController/getSubtotalCtrl'; //to delete
$route['placeOrder_r'] = 'AppController/placeOrder_ctrl';
// $route['getLastOrderId_r'] = 'AppController/getLastOrderId_ctrl'; 
$route['getLastItems_r'] = 'AppController/getLastItems_ctrl';
$route['getAllowedLoc_r'] = 'AppController/getAllowedLoc_ctrl';
$route['getBu_r'] = 'AppController/getBu_ctrl';
$route['getBu_r1'] = 'AppController/getBu_ctrl1';
$route['getTenant_r'] = 'AppController/getTenant_ctrl';
$route['getTicketNoFood_r'] = 'AppController/getTicketNoFood_ctrl';
$route['getTicketNoFood_ontrans_r'] = 'AppController/getTicketNoFood_ontrans_ctrl';
$route['getTicketNoFood_delivered_r'] = 'AppController/getTicketNoFood_delivered_ctrl';
$route['getTicket_cancelled_r'] =  'AppController/getTicket_cancelled_ctrl';
// $route['getTicketNoGood_r'] = 'AppController/getTicketNoGood_ctrl';
$route['loadProfile_r'] = 'AppController/loadProfile_ctrl';
$route['lookItems_r'] = 'AppController/lookItems_ctrl';
$route['lookItems_segregate_r'] = 'AppController/lookItems_segregate_ctrl';
$route['lookitems_good_r'] = 'AppController/lookitems_good_ctrl';
$route['loadCartData_r'] = 'AppController/loadCartData_ctrl';
$route['loadCartDataNew_r'] = 'AppController/loadCartDataNew_ctrl';
$route['loadCartData_sides_r'] = 'AppController/loadCartData_sides_ctrl'; // to be done later
$route['removeItemFromCart_r'] = 'AppController/clearCustomerCartPerItem_ctrl';
$route['displayOrder_r'] = 'AppController/displayOrder_ctrl';
$route['trapTenantLimit_r'] = 'AppController/trapTenantLimit_ctrl';
$route['getTenant_perbu_r'] = 'AppController/getTenant_perbu_ctrl';
$route['getAmountPertenant_r'] = 'AppController/getAmountPertenant_ctrl';
$route['checkAllowedPlace_r'] = 'AppController/checkAllowedPlace_ctrl';
$route['checkFee_r'] = 'AppController/checkFee_ctrl';
$route['updateCartQty_r'] =  'AppController/updateCartQty_ctrl';
$route['getCounter_r'] = 'AppController/getCounter_ctrl';
//node 
$route['display_store_r'] = 'AppController/display_store_ctrl';
$route['display_tenant_r'] = 'AppController/display_tenant_ctrl';
$route['display_restaurant_r'] = 'AppController/display_restaurant_ctrl';
$route['display_item_data_r'] =  'AppController/display_item_data_ctrl';
// $route['add_to_cart_r'] = 'AppController/add_to_cart_ctrl';
$route['addToCartNew_r'] = 'AppController/addToCartNew_ctrl';
$route['selectSuffix_r'] = 'AppController/selectSuffix_ctrl';
$route['getTowns_r'] = 'AppController/getTowns_ctrl';
$route['getbarrio_r'] = 'AppController/getbarrio_ctrl';
$route['savePickup_r'] = 'AppController/savePickup_ctrl';
$route['loadSubTotal'] = 'AppController/loadSubTotal_ctrl';

$route['loadSubTotalnew_r'] = 'AppController/loadSubTotalnew_ctrl';
$route['xsample'] = 'AppController/xsample';

$route['getTrueTime_r'] = 'AppController/getTrueTime_ctrl';
$route['listenCartSubtotal_r'] = 'AppController/listenCartSubtotal_ctrl';

$route['loadFlavor_r'] = 'AppController/loadFlavor_ctrl';
$route['loadDrinks_r'] = 'AppController/loadDrinks_ctrl';
$route['loadFries_r'] = 'AppController/loadFries_ctrl';
$route['loadSide_r'] = 'AppController/loadSide_ctrl';
$route['checkAddon_r'] = 'AppController/checkAddon_ctrl';
$route['loadAddonSide_r'] = 'AppController/loadAddonSide_ctrl';
$route['loadAddonDessert_r'] = 'AppController/loadAddonDessert_ctrl';
$route['cancelOrderSingleFood_r'] = 'AppController/cancelOrderSingleFood_ctrl';
$route['cancelOrderSingleGood_r'] = 'AppController/cancelOrderSingleGood_ctrl';
$route['loadLocation_r'] = 'AppController/loadLocation_ctrl';
$route['displayAddOns_r'] = 'AppController/displayAddOns_ctrl';
// $route['upLoadImage_r'] = 'AppController/upLoadImage_ctrl';
$route['showFlavor_r'] = 'AppController/showFlavor_ctrl';
$route['showDrinks_r'] = 'AppController/showDrinks_ctrl';
$route['getTotal_r'] = 'AppController/getTotal_ctrl';
$route['checkifongoing_r'] = 'AppController/checkifongoing_ctrl';
$route['viewCategories_r'] = 'AppController/viewCategories_ctrl';
$route['checkifemptystore_r'] = 'AppController/checkifemptystore_ctrl';
$route['getCategories_r'] = 'AppController/getCategories_ctrl';
$route['getItemsByCategories_r'] = 'AppController/getItemsBycategories_ctrl';
$route['getItemsByCategoriesAll_r'] = 'AppController/getItemsByCategoriesAll_ctrl';
$route['getGcItems_r'] = 'AppController/getGcItems_ctrl';
$route['addToCartGc_r'] = 'AppController/addToCartGc_ctrl';

$route['gc_cart_r'] = 'AppController/gc_cart_ctrl';
$route['updateGcCartQty_r'] = 'AppController/updateGcCartQty_ctrl';
$route['loadGcSubTotal_r'] = 'AppController/loadGcSubTotal_ctrl';
$route['getGcCounter_r'] = 'AppController/getGcCounter_ctrl';

$route['getGcCategories_r'] = 'AppController/getGcCategories_ctrl';
$route['getItemsByGcCategories_r'] = 'AppController/getItemsByGcCategories_ctrl';
$route['removeGcItemFromCart_r'] = 'AppController/removeGcItemFromCart_r';

$route['getBill_r'] = 'AppController/getBill_ctrl';
$route['gcgroupbyBu_r'] = 'AppController/gcgroupbyBu';
$route['getConFee_r'] = 'AppController/getConFee_ctrl';
$route['gc_submitOrder_r'] = 'AppController/gc_submitOrder_ctrl';
$route['gc_searchProd_r'] = 'AppController/gc_searchProd_ctrl';
$route['gc_getbillperbu_r'] = 'AppController/gc_getbillperbu_ctrl';

$route['gc_select_uom_r'] = 'AppController/gc_select_uom_ctrl';
$route['showDiscount_r'] = 'AppController/showDiscount_ctrl';
$route['uploadId_r'] = 'AppController/uploadId_ctrl';
// $route['uploadId1_r'] = 'AppController/uploadId1_ctrl';
$route['upLoadImage_r'] = 'AppController/upLoadImage_ctrl';
$route['loadIdList_r'] = 'AppController/loadIdList_ctrl';
$route['delete_id_r'] = 'AppController/delete_id_ctrl';
$route['checkidcheckout_r'] = 'AppController/checkidcheckout_ctrl';
$route['changeAccountStat_r'] = 'AppController/changeAccountStat_ctrl';
$route['getUserDetails_r'] = 'AppController/getUserDetails_ctrl';
$route['saveOTPNumber_r'] = 'AppController/saveOTPNumber_ctrl';
$route['checkOtpCode_r'] = 'AppController/checkOtpCode_ctrl';
$route['changePassword_r'] = 'AppController/changePassword_ctrl';
$route['checkUsernameIfExist_r'] = 'AppController/checkUsernameIfExist_ctrl';
$route['checkPhoneIfExist_r'] = 'AppController/checkPhoneIfExist_ctrl';

$route['displayCartAddOns_r'] = 'AppController/displayCartAddOns_ctrl';
$route['getProvince_r'] = 'AppController/getProvince_ctrl';
$route['getTown_r'] = 'AppController/getTown_ctrl';
$route['getBarangay_r'] = 'AppController/getBarangay_ctrl';
$route['selectBuildingType'] = 'AppController/selectBuildingType_ctrl';
$route['submitNewAddress_r'] = 'AppController/submitNewAddress_ctrl';
$route['loadAddress_r'] = 'AppController/loadAddress_ctrl';
$route['deleteAddress_r'] = 'AppController/deleteAddress_ctrl';
$route['checkIfHasAddresses_r'] = 'AppController/checkIfHasAddresses_ctrl';
$route['showRiderDetails_r'] = 'AppController/showRiderDetails_ctrl';
$route['updateDefaultShipping_r'] = 'AppController/updateDefaultShipping_ctrl';

$route['viewTenantCategories_r'] = 'AppController/viewTenantCategories_ctrl';
$route['viewAddon_r'] = 'AppController/viewAddon_ctrl';
$route['checkIfBf_r'] = 'AppController/checkIfBf_ctrl';
$route['getTotalFee_r'] = 'AppController/getTotalFee_ctrl';

$route['getglobalcat_r'] = 'AppController/getglobalcat_ctrl';
$route['search_item_r'] = 'AppController/search_item_ctrl';
$route['searchGc_item_r'] = 'AppController/searchGc_item_ctrl';
$route['updatePassword_r'] = 'AppController/updatePassword_ctrl';
$route['chat_r'] = 'AppController/chat_ctrl';
$route['send_chat_r'] = 'AppController/send_chat_ctrl';
$route['uploadProfilePic_r'] = 'AppController/uploadProfilePic_ctrl';
