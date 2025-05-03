<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('listSideBarMenuByRoleLogin')) {
    function listSideBarMenuByRoleLogin($position = 'left')
    {
        $CI = &get_instance();

        $id = get_session('id_user');
        $role = $CI->db->select('role_id')
                       ->where('id', $id)
                       ->get('users')
                       ->row_array();

        if (!$role) return [];

        $role_id = $role['role_id'];

        $menus = $CI->db->where('position', $position)
                        ->where('is_active', 1)
                        ->order_by('order_by', 'ASC')
                        ->get('menus')
                        ->result_array();

        $permissionShortName = 'Show';
        $return = [];

        foreach ($menus as $menu) {
            $menu_id = $menu['id'];

            $showPermission = $CI->db->select('p.id, p.name')
                                     ->from('permissions p')
                                     ->join('role_has_permissions rhp', 'p.id = rhp.permission_id')
                                     ->where('p.id_menu', $menu_id)
                                     ->where('p.short_name', $permissionShortName)
                                     ->where('rhp.role_id', $role_id)
                                     ->get()
                                     ->row_array();

            if ($showPermission) {
                $order = $CI->db->select('order_by')
                                ->from('role_has_permissions')
                                ->where('role_id', $role_id)
                                ->where('permission_id', $showPermission['id'])
                                ->get()
                                ->row_array();
                $order_id = isset($order['order_by']) ? $order['order_by'] : 0;

                $return[$order_id][] = $menu;
            }
        }

        ksort($return);

        $return_new = [];
        foreach ($return as $value) {
            foreach ($value as $menu) {
                $return_new[] = $menu;
            }
        }
        return $return_new;
    }
}

if (!function_exists('listSideBarMenuTree')) {
    function listSideBarMenuTree($items, $parentId = 0, $position = 'left')
    {
        $result = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = listSideBarMenuTree($items, $item['id'], $position);
                if ($children) {
                    $item['children'] = $children;
                }
                $result[] = $item;
            }
        }
        return $result;
    }
}

if (!function_exists('buildMenuHTML')) {
    function buildMenuHTML($position = 'left', $menus = [])
    {
        $func = "buildMenu" . ucfirst($position) . "HTML";
        if (function_exists($func)) {
            return $func($menus, $position);
        }
        return '';
    }
}

if (!function_exists('buildMenuLeftHTML')) {
    function buildMenuLeftHTML($menus = [], $position = 'left')
    {
        if (count($menus) == 0) {
            $items = listSideBarMenuByRoleLogin($position);
            $menus = listSideBarMenuTree($items, 0, $position);
            $html = '<ul class="sidebar-menu" data-widget="tree">';
        } else {
            $html = '<ul class="treeview-menu">';
        }

        foreach ($menus as $mn) {
            if ($mn['is_separator'] == 1) {
                $html .= '<li class="header">' . $mn['menu_name'] . '</li>';
            } else {
                if (!empty($mn['children'])) {
                    $html .= '<li class="treeview">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">';
                    if ($mn['menu_icon'] != '') {
                        $html .= '<i class="fa ' . $mn['menu_icon'] . '"></i>';
                    }
                    $html .= '<span>' . $mn['menu_name'] . '</span>
                          <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>';
                    $html .= buildMenuLeftHTML($mn['children'], $position);
                    $html .= '</li>';
                } else {
                    $route_name = (!empty($mn['route_name'])) ? $mn['route_name'] : '#';
                    $html .= '<li>
                        <a href="' . $route_name . '">';
                    if ($mn['menu_icon'] != '-') {
                        $html .= '<i class="fa ' . $mn['menu_icon'] . '"></i>';
                    }
                    $html .= '<span>' . $mn['menu_name'] . '</span></a>
                      </li>';
                }
            }
        }

        $html .= '</ul>';
        return $html;
    }
}


if (!function_exists('getMenuIndex')) {
    function getMenuIndex($role_id = '', $position = 'left', $parent_id = NULL, $active = 1)
    {
        $CI = &get_instance();

        $returnSub = [];
        $i = 0; $no = 1;

        if (is_null($parent_id)) {
            $CI->db->where('parent_id IS NULL', null, false);
        } else {
            $CI->db->where('parent_id', $parent_id);
        }

        $CI->db->where('position', $position)
               ->where('is_active', $active)
               ->order_by('order_by', 'ASC');

        $subMenus = $CI->db->get('menus')->result_array();

        foreach ($subMenus as $key2 => $submenu) {
            $submenu_id = $submenu['id'];
            $no2 = '';

            // Ambil permissions untuk submenu
            $subPermissions = $CI->db->where('id_menu', $submenu_id)
                                     ->where('short_name', 'Show')
                                     ->order_by('id', 'ASC')
                                     ->get('permissions')
                                     ->result_array();

            foreach ($subPermissions as &$subPermission) {
                $subPermission_id = $subPermission['id'];

                // Cek apakah role memiliki permission
                $hasPermission = $CI->db->where('role_id', $role_id)
                                        ->where('permission_id', $subPermission_id)
                                        ->count_all_results('role_has_permissions');

                $submenu['hasPermission'] = $hasPermission > 0;

                // Ambil order_by kalau ada
                $sub_order = $CI->db->select('order_by')
                                    ->where('permission_id', $subPermission_id)
                                    ->where('role_id', $role_id)
                                    ->get('role_has_permissions')
                                    ->row_array();

                $order_id2 = isset($sub_order['order_by']) ? $sub_order['order_by'] : '';
                $no2 = $order_id2 . $key2;
            }

            $returnSub[$no2] = $submenu;
        }

        if (!empty($returnSub)) {
            ksort($returnSub);
        }

        return $returnSub;
    }
}


if (!function_exists('getMenuWithPermissionsByRoleName')) {
  function getMenuWithPermissionsByRoleName($role_id, $position = 'left', $active = 1)
  {
      $CI = &get_instance();
      $CI->db->where('position', $position);
      $CI->db->where('parent_id IS NULL', NULL, FALSE);
      $CI->db->where('is_active', $active);
      $CI->db->order_by('order_by', 'ASC');
      $menus = $CI->db->get('menus')->result_array();

      $return = [];
      $order_id = 0;
      $order_id2 = 0;
      $no = "00";

      foreach ($menus as $key => $menu) {
          $menu_id = $menu['id'];

          // Get permissions
          $CI->db->select('*');
          $CI->db->from('permissions');
          $CI->db->where('id_menu', $menu_id);
          $CI->db->group_by('short_name');
          $CI->db->order_by('id', 'ASC');
          $permissions = $CI->db->get()->result_array();

          foreach ($permissions as &$permission) {
              $permission_id = $permission['id'];

              $count = $CI->db->where('role_id', $role_id)
                                ->where('permission_id', $permission_id)
                                ->count_all_results('role_has_permissions');
              $permission['hasPermission'] = $count > 0;

              if ($permission['short_name'] == 'Show') {
                  $CI->db->select('order_by');
                  $order = $CI->db->get_where('role_has_permissions', [
                      'permission_id' => $permission_id,
                      'role_id' => $role_id
                  ])->row_array();

                  $order_id = $order ? $order['order_by'] : $order_id;
                  $no = "$order_id$key";
              }
          }

          $menu['permissions'] = $permissions;

          // Get submenus
          $CI->db->where('position', $position);
          $CI->db->where('parent_id', $menu_id);
          $CI->db->where('is_active', $active);
          $CI->db->order_by('order_by', 'ASC');
          $subMenus = $CI->db->get('menus')->result_array();

          $returnSub = [];

          foreach ($subMenus as $key2 => $submenu) {
              $submenu_id = $submenu['id'];
              $no2 = '';

              // Get permissions for submenu
              $CI->db->select('*');
              $CI->db->from('permissions');
              $CI->db->where('id_menu', $submenu_id);
              $CI->db->group_by('short_name');
              $CI->db->order_by('id', 'ASC');
              $subPermissions = $CI->db->get()->result_array();

              foreach ($subPermissions as &$subPermission) {
                  $subPermission_id = $subPermission['id'];

                  $count = $CI->db->where('role_id', $role_id)
                                    ->where('permission_id', $subPermission_id)
                                    ->count_all_results('role_has_permissions');
                  $subPermission['hasPermission'] = $count > 0;

                  if ($subPermission['short_name'] == 'Show') {
                      $CI->db->select('order_by');
                      $sub_order = $CI->db->get_where('role_has_permissions', [
                          'permission_id' => $subPermission_id,
                          'role_id' => $role_id
                      ])->row_array();

                      $order_id2 = $sub_order ? $sub_order['order_by'] : $order_id2;
                      $no2 = "$order_id2$key2";
                  }
              }

              $submenu['permissions'] = $subPermissions;
              $returnSub["$no2"] = $submenu;
          }

          $i = 0;
          $return["$no$i"] = $menu;

          if (!empty($returnSub)) {
              ksort($returnSub);
              foreach ($returnSub as $k => $v) {
                  $i++;
                  $return["$no$i"] = $v;
              }
          }
      }

      ksort($return);
      return $return;
  }
}

function firstWhere($data=[], $name="", $val="")
{
  if (empty($data) || $name=="" || $val=="") { return []; }
  $get = array_filter($data, function($item) use ($name, $val) {
      return isset($item[$name]) && $item[$name] === $val;
  });
  return reset($get);
}
