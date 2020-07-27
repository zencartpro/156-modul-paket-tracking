<?php
/**
 * Header code file for the Account History Information/Details page (which displays details for a single specific order)
 *
 * @package page
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: header_php.php for Paket Tracking 2019-08-06 18:51:40Z webchills $
 */
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_ACCOUNT_HISTORY_INFO');

if (!zen_is_logged_in()) {
  $_SESSION['navigation']->set_snapshot();
  zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
  zen_redirect(zen_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

$customer_info_query = "SELECT customers_id
                        FROM   " . TABLE_ORDERS . "
                        WHERE  orders_id = :ordersID";

$customer_info_query = $db->bindVars($customer_info_query, ':ordersID', $_GET['order_id'], 'integer');
$customer_info = $db->Execute($customer_info_query);

if ($customer_info->fields['customers_id'] != $_SESSION['customer_id']) {
  zen_redirect(zen_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

// Begin Paket Tracking 
$statuses_query = "SELECT os.orders_status_name, osh.date_added, osh.comments, osh.track_id1, osh.track_id2, osh.track_id3, osh.track_id4, osh.track_id5, osh.track_id6, osh.track_day, osh.track_month, osh.track_year
                   FROM   " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh
                   WHERE      osh.orders_id = :ordersID
                   AND        osh.orders_status_id = os.orders_status_id
                   AND        os.language_id = :languagesID
                   AND        osh.customer_notified >= 0
                   ORDER BY   osh.date_added";
// End Paket Tracking 

$statuses_query = $db->bindVars($statuses_query, ':ordersID', $_GET['order_id'], 'integer');
$statuses_query = $db->bindVars($statuses_query, ':languagesID', $_SESSION['languages_id'], 'integer');
$statuses = $db->Execute($statuses_query);
$statusArray = array();

while (!$statuses->EOF) {
  $statusArray[] = array(
// Begin Paket Tracking 
  'date_added'=>$statuses->fields['date_added'],
  'orders_status_name'=>$statuses->fields['orders_status_name'],
  'comments'=>$statuses->fields['comments'],
  'track_id1'=>$statuses->fields['track_id1'],
  'track_id2'=>$statuses->fields['track_id2'],
  'track_id3'=>$statuses->fields['track_id3'],
  'track_id4'=>$statuses->fields['track_id4'],
  'track_id5'=>$statuses->fields['track_id5'],
  'track_id6'=>$statuses->fields['track_id6'],
  'track_day'=>$statuses->fields['track_day'],
  'track_month'=>$statuses->fields['track_month'],
  'track_year'=>$statuses->fields['track_year']);
  $statuses->MoveNext();
}
// End Paket Tracking 


require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2, zen_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$breadcrumb->add(sprintf(NAVBAR_TITLE_3, $_GET['order_id']));

require(DIR_WS_CLASSES . 'order.php');
$order = new order($_GET['order_id']);

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_ACCOUNT_HISTORY_INFO');
