<?php
/**
 * @package paket tracking 
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: track_orders.php 2019-08-06 20:19:14 webchills $
*/
// test if track orders sidebox should show
  $show_track_orders= false;

if ($show_track_orders = true) {
  if (!zen_is_logged_in()) {
// retrieve the last x products purchased
  $orders_history_query = 'select o.orders_id, o.date_purchased
                   from ' . TABLE_ORDERS . " o
                   where o.customers_id = '" . (int)$_SESSION['customer_id'] . "'
                   order by o.date_purchased desc
                   limit " . MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX;

    $orders_history = $db->Execute($orders_history_query);

    if ($orders_history->RecordCount() > 0) {
      $orders_ids = '';
      while (!$orders_history->EOF) {
        $orders_ids .= (int)$orders_history->fields['orders_id'] . ',';
        $orders_history->MoveNext();
      }
      $orders_ids = substr($orders_ids, 0, -1);
      $rows=0;
      $customer_orders_string = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
      $products_history_query = 'select orders_id, date_purchased
                         from ' . TABLE_ORDERS . '
                         where orders_id in (' . $orders_ids . ')
                         order by date_purchased desc';

      $products_history = $db->Execute($products_history_query);

      while (!$products_history->EOF) {
        $rows++;
        $customer_orders[$rows]['id'] = $products_history->fields['orders_id'];
        $customer_orders[$rows]['date'] = $products_history->fields['date_purchased'];
        $products_history->MoveNext();
      }
      $customer_orders_string .= '</table>';
    }
  }
      require $template->get_template_dir('tpl_track_orders.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_track_orders.php';
      $title =  BOX_HEADING_TRACK_ORDERS;
      $title_link = false;
      require $template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default;
} // $show_track_orders