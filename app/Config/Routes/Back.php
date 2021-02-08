<?php
#Item
$routes->post("item/create/single", "CreateController::single", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/read/single/(:num)", "ReadController::single/$1", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/read/single-by-code", "ReadController::singleByItemCode", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/read/single-by-code/(:alphanum)", "ReadController::singleByItemCode/$1", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/read/items-summary", "ReadController::itemsSummary", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/update/single", "UpdateController::single", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/list/items", "ListController::items", ["namespace" => "App\Controllers\Item"]);
$routes->post("item/list/active-items", "ListController::activeItems", ["namespace" => "App\Controllers\Item"]);

#ItemHistory
$routes->post("item-history/create/single-using-batch", "CreateController::singleUsingBatch", ["namespace" => "App\Controllers\ItemHistory"]);
$routes->post("item-history/list/item/(:num)", "ListController::byItemId/$1", ["namespace" => "App\Controllers\ItemHistory"]);

#OffSite
$routes->get("offsite/action/logout", "ActionController::logout", ["namespace" => "App\Controllers\OffSite"]);
$routes->post("offsite/action/login", "ActionController::login", ["namespace" => "App\Controllers\OffSite"]);

#Sale
$routes->post("sale/create/single-and-details", "CreateController::singleAndDetails", ["namespace" => "App\Controllers\Sale"]);
$routes->post("sale/update/cancel-single", "UpdateController::cancelSingle", ["namespace" => "App\Controllers\Sale"]);
$routes->post("sale/list/sales", "ListController::sales", ["namespace" => "App\Controllers\Sale"]);

#SaleDetail
$routes->post("sale-detail/list/sale-details-by-sale-serial", "ListController::saleDetailsBySaleSerial", ["namespace" => "App\Controllers\SaleDetail"]);
$routes->post("sale-detail/list/sale-details-by-sale-serial/(:alphanum)", "ListController::saleDetailsBySaleSerial/$1", ["namespace" => "App\Controllers\SaleDetail"]);
$routes->post("sale-detail/read/sales-global-summary", "ReadController::salesGlobalSummary", ["namespace" => "App\Controllers\SaleDetail"]);

#Unidad
$routes->post("unit/create/single", "CreateController::single", ["namespace" => "App\Controllers\Unit"]);
$routes->post("unit/read/single/(:num)", "ReadController::single/$1", ["namespace" => "App\Controllers\Unit"]);
$routes->post("unit/update/single", "UpdateController::single", ["namespace" => "App\Controllers\Unit"]);
$routes->post("unit/list/units", "ListController::units", ["namespace" => "App\Controllers\Unit"]);
$routes->post("unit/list/active-units", "ListController::activeUnits", ["namespace" => "App\Controllers\Unit"]);

#User
$routes->post("user/create/single", "CreateController::single", ["namespace" => "App\Controllers\User"]);
$routes->post("user/update/single", "UpdateController::single", ["namespace" => "App\Controllers\User"]);
$routes->post("user/list/users", "ListController::users", ["namespace" => "App\Controllers\User"]);
$routes->post("user/list/active-users", "ListController::activeUsers", ["namespace" => "App\Controllers\User"]);

#UserAccess
$routes->post("user-access/list/user-access", "ListController::userAccess", ["namespace" => "App\Controllers\UserAccess"]);

#UserRole
$routes->post("user-role/create/single", "CreateController::single", ["namespace" => "App\Controllers\UserRole"]);
$routes->post("user-role/read/single/(:num)", "ReadController::single/$1", ["namespace" => "App\Controllers\UserRole"]);
$routes->post("user-role/update/single", "UpdateController::single", ["namespace" => "App\Controllers\UserRole"]);
$routes->post("user-role/list/user-roles", "ListController::userRoles", ["namespace" => "App\Controllers\UserRole"]);
$routes->post("user-role/list/active-user-roles", "ListController::activeUserRoles", ["namespace" => "App\Controllers\UserRole"]);

#Config
$routes->post("config/update/business-logo", "UpdateController::businessLogo", ["namespace" => "App\Controllers\Config"]);
$routes->post("config/update/business-icon", "UpdateController::businessIcon", ["namespace" => "App\Controllers\Config"]);
$routes->post("config/update/business-name", "UpdateController::businessName", ["namespace" => "App\Controllers\Config"]);
