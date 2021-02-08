<?php
#Base
$routes->get('/', "BaseController::sessionRouter");

#Item
$routes->addRedirect("item", "item/view/create");
$routes->get("item/view/create", "ViewController::create", ["namespace" => "App\Controllers\Item"]);
$routes->get("item/view/items-list/update/(:num)", "ViewController::update/$1", ["namespace" => "App\Controllers\Item"]);
$routes->get("item/view/items-list", "ViewController::itemsList", ["namespace" => "App\Controllers\Item"]);
$routes->get("item/view/items-list/history/(:num)", "ViewController::history/$1", ["namespace" => "App\Controllers\Item"]);

#ItemHistory (Inventario)
$routes->addRedirect("item-history", "item-history/view/ingress");
$routes->get("item-history/view/ingress", "ViewController::ingress", ["namespace" => "App\Controllers\ItemHistory"]);
$routes->get("item-history/view/egress", "ViewController::egress", ["namespace" => "App\Controllers\ItemHistory"]);
$routes->get("item-history/view/inventory", "ViewController::inventory", ["namespace" => "App\Controllers\ItemHistory"]);

#OffSite
$routes->addRedirect("offsite", "offsite/view/login");
$routes->get("offsite/view/login", "ViewController::login", ["namespace" => "App\Controllers\OffSite"]);

#Sale
$routes->addRedirect("sale", "sale/view/create");
$routes->get("sale/view/create", "ViewController::create", ["namespace" => "App\Controllers\Sale"]);
$routes->get("sale/view/cancel", "ViewController::cancel", ["namespace" => "App\Controllers\Sale"]);
$routes->get("sale/view/sales-list", "ViewController::salesList", ["namespace" => "App\Controllers\Sale"]);

#Unit
$routes->addRedirect("unit", "unit/view/create");
$routes->get("unit/view/create", "ViewController::create", ["namespace" => "App\Controllers\Unit"]);
$routes->get("unit/view/units-list/update/(:num)", "ViewController::update/$1", ["namespace" => "App\Controllers\Unit"]);
$routes->get("unit/view/units-list", "ViewController::unitsList", ["namespace" => "App\Controllers\Unit"]);

#User
$routes->addRedirect("user", "user/view/create");
$routes->get("user/view/create", "ViewController::create", ["namespace" => "App\Controllers\User"]);
$routes->get("user/view/users-list", "ViewController::usersList", ["namespace" => "App\Controllers\User"]);
$routes->get("user/view/users-list/update/(:num)", "ViewController::update/$1", ["namespace" => "App\Controllers\User"]);

#UserAccess
$routes->addRedirect("user-access", "user-access/view/user-access-list");
$routes->get("user-access/view/user-access-list", "ViewController::userAccessList", ["namespace" => "App\Controllers\UserAccess"]);

#UserRole
$routes->addRedirect("user-role", "user-role/view/create");
$routes->get("user-role/view/create", "ViewController::create", ["namespace" => "App\Controllers\UserRole"]);
$routes->get("user-role/view/user-roles-list", "ViewController::userRolesList", ["namespace" => "App\Controllers\UserRole"]);
$routes->get("user-role/view/user-roles-list/update/(:num)", "ViewController::update/$1", ["namespace" => "App\Controllers\UserRole"]);

#Config
$routes->addRedirect("config", "config/view/business-name");
$routes->get("config/view/business-name", "ViewController::businessName", ["namespace" => "App\Controllers\Config"]);
$routes->get("config/view/business-img", "ViewController::businessImg", ["namespace" => "App\Controllers\Config"]);
