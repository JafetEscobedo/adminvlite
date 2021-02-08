<?php

namespace App\Controllers;

use App\Libraries\Template;
use App\Models\ConfigModel;
use CodeIgniter\Controller;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Config\Database;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
  protected $helpers = ["form", "handle", "array"];
  protected $db;
  protected $session;
  protected $template;
  protected $menus;

  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
  {
    parent::initController($request, $response, $logger);

    $this->db      = Database::connect();
    $this->session = Services::session();

    $this->configureMenus();
    $this->configureTemplate();
    $this->configureBusiness();
  }

  public function sessionRouter(): ResponseInterface
  {
    $firstRoute = json_decode($this->session->get("user_role_access"))[0];
    return redirect($firstRoute);
  }

  private function configureBusiness(): void
  {
    if (!$this->session->has("business_name"))
    {
      $this->db->transBegin();
      $configModel  = new ConfigModel();
      $configEntity = $configModel->readSingle();
      $favicon      = new File("public/img/config/" . $configEntity->configBusinessIcon);

      $this->session->set("business_name", $configEntity->configBusinessName);
      $this->session->set("business_name_uc", $configEntity->configBusinessNameUc);
      $this->session->set("business_logo", $configEntity->configBusinessLogo);
      $this->session->set("business_icon", $configEntity->configBusinessIcon);
      $this->session->set("favicon_mime", $favicon->getMimeType());
      $this->db->transCommit();
    }
  }

  private function configureTemplate(): void
  {
    $this->template = new Template();
    $this->template->dataset("templateFluid", false);
    $this->template->dataset("templateMenu", $this->menus["default"]["menu"]);
    $this->template->dataset("templateAllMenus", $this->menus);
    $this->template->select("adminlte");
  }

  private function configureMenus(): void
  {
    $this->menus = [
      "default" => [
        "name" => "Menú principal",
        "menu" => [
          [
            "icon" => "fas fa-shopping-cart",
            "name" => "Venta",
            "path" => "sale",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Venta nueva",
                "path" => "sale/view/create",
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Cancelar venta",
                "path" => "sale/view/cancel",
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Historial",
                "path" => "sale/view/sales-list",
              ]
            ]
          ],
          [
            "icon" => "fas fa-boxes",
            "name" => "Inventario",
            "path" => "item-history",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Entradas",
                "path" => "item-history/view/ingress"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Salidas",
                "path" => "item-history/view/egress"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Artículos",
                "path" => "item-history/view/inventory"
              ]
            ]
          ],
          [
            "icon" => "fas fa-barcode",
            "name" => "Artículo",
            "path" => "item",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Crear artículo",
                "path" => "item/view/create"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Lista de artículos",
                "path" => "item/view/items-list"
              ]
            ]
          ],
          [
            "icon" => "fas fa-capsules",
            "name" => "Unidad",
            "path" => "unit",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Crear unidad",
                "path" => "unit/view/create"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Lista de unidades",
                "path" => "unit/view/units-list"
              ]
            ]
          ],
          [
            "icon" => "fas fa-user",
            "name" => "Usuario",
            "path" => "user",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Crear usuario",
                "path" => "user/view/create"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Lista de usuarios",
                "path" => "user/view/users-list"
              ]
            ]
          ],
          [
            "icon" => "fas fa-user-lock",
            "name" => "Acceso",
            "path" => "user-access",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Bitacora",
                "path" => "user-access/view/user-access-list"
              ]
            ]
          ],
          [
            "icon" => "fas fa-users",
            "name" => "Rol de usuario",
            "path" => "user-role",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Crear rol",
                "path" => "user-role/view/create"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Lista de roles",
                "path" => "user-role/view/user-roles-list"
              ]
            ]
          ],
          [
            "icon" => "fas fa-cogs",
            "name" => "Configuración",
            "path" => "config",
            "menu" => [
              [
                "icon" => "far fa-dot-circle",
                "name" => "Nombre de empresa",
                "path" => "config/view/business-name"
              ],
              [
                "icon" => "far fa-dot-circle",
                "name" => "Imagen de empresa",
                "path" => "config/view/business-img"
              ]
            ]
          ]
        ]
      ]
    ];
  }
}