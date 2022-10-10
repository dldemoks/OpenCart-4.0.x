<?php
namespace Opencart\Catalog\Model\Extension\Payeer\Payment;
class Payeer extends \Opencart\System\Engine\Model
{
  public function getMethod(array $address): array
  {
    $this->load->language('extension/payeer/payment/payeer');
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_payeer_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

    if ($this->cart->hasSubscription()){
        $status = false;
    } elseif (!$this->config->get('payment_payeer_geo_zone_id')) {
        $status = true;
    } elseif ($query->num_rows) {
        $status = true;
    } else {
        $status = false;
    }

    $currencies = array(
    	'RUB',
    	'RUR',
    	'USD',
    	'EUR'
    );

    if (!in_array(strtoupper($this->session->data['currency']), $currencies)) {
    	$status = false;
    }

    $method_data = array();

    if ($status) {
      $method_data = array(
        'code'       => 'payeer',
        'title'      => $this->language->get('text_title'),
        'terms'      => '',
        'sort_order' => $this->config->get('payment_payeer_sort_order')
      );
    }

    return $method_data;
  }
}
