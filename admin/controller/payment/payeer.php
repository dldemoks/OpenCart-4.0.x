<?php
namespace Opencart\Admin\Controller\Extension\Payeer\Payment;
class Payeer extends \Opencart\System\Engine\Controller
{
  private $error = [];

  public function index(): void {
    $this->load->language('extension/payeer/payment/payeer');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('payment_payeer', $this->request->post);
      $this->session->data['success'] = $this->language->get('text_success');
      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['url'])) {
      $data['error_url'] = $this->error['url'];
    } else {
      $data['error_url'] = '';
    }

    if (isset($this->error['merchant'])) {
      $data['error_merchant'] = $this->error['merchant'];
    } else {
      $data['error_merchant'] = '';
    }

    if (isset($this->error['security'])) {
      $data['error_security'] = $this->error['security'];
    } else {
      $data['error_security'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_payment'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true),
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/payeer/payment/payeer', 'user_token=' . $this->session->data['user_token'], true),
    );

    $data['action'] = $this->url->link('extension/payeer/payment/payeer', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
    $this->load->model('localisation/order_status');
    $this->load->model('localisation/geo_zone');
    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    if (isset($this->request->post['payment_payeer_status'])) {
      $data['payment_payeer_status'] = $this->request->post['payment_payeer_status'];
    } else {
      $data['payment_payeer_status'] = $this->config->get('payment_payeer_status');
    }

    if (isset($this->request->post['payment_payeer_url'])) {
      $data['payment_payeer_url'] = $this->request->post['payment_payeer_url'];
    } else {
      $data['payment_payeer_url'] = $this->config->get('payment_payeer_url');
    }

    if (isset($this->request->post['payment_payeer_merchant'])) {
      $data['payment_payeer_merchant'] = $this->request->post['payment_payeer_merchant'];
    } else {
      $data['payment_payeer_merchant'] = $this->config->get('payment_payeer_merchant');
    }

    if (isset($this->request->post['payment_payeer_security'])) {
      $data['payment_payeer_security'] = $this->request->post['payment_payeer_security'];
    } else {
      $data['payment_payeer_security'] = $this->config->get('payment_payeer_security');
    }

    if (isset($this->request->post['payment_payeer_logfile'])) {
      $data['payment_payeer_logfile'] = $this->request->post['payment_payeer_logfile'];
    } else {
      $data['payment_payeer_logfile'] = $this->config->get('payment_payeer_logfile');
    }

    if (isset($this->request->post['payment_payeer_ipfilter'])) {
      $data['payment_payeer_ipfilter'] = $this->request->post['payment_payeer_ipfilter'];
    } else {
      $data['payment_payeer_ipfilter'] = $this->config->get('payment_payeer_ipfilter');
    }

    if (isset($this->request->post['payment_payeer_email'])) {
      $data['payment_payeer_email'] = $this->request->post['payment_payeer_email'];
    } else {
      $data['payment_payeer_email'] = $this->config->get('payment_payeer_email');
    }

    if (isset($this->request->post['payment_payeer_order_wait_id'])) {
      $data['payment_payeer_order_wait_id'] = $this->request->post['payment_payeer_order_wait_id'];
    } else {
      $data['payment_payeer_order_wait_id'] = $this->config->get('payment_payeer_order_wait_id');
    }

    if (isset($this->request->post['payment_payeer_order_success_id'])) {
      $data['payment_payeer_order_success_id'] = $this->request->post['payment_payeer_order_success_id'];
    } else {
      $data['payment_payeer_order_success_id'] = $this->config->get('payment_payeer_order_success_id');
    }

    if (isset($this->request->post['payment_payeer_order_fail_id'])) {
      $data['payment_payeer_order_fail_id'] = $this->request->post['payment_payeer_order_fail_id'];
    } else {
      $data['payment_payeer_order_fail_id'] = $this->config->get('payment_payeer_order_fail_id');
    }

    if (isset($this->request->post['payment_payeer_geo_zone_id'])) {
      $data['payment_payeer_geo_zone_id'] = $this->request->post['payment_payeer_geo_zone_id'];
    } else {
      $data['payment_payeer_geo_zone_id'] = $this->config->get('payment_payeer_geo_zone_id');
    }

    if (isset($this->request->post['payment_payeer_sort_order'])) {
      $data['payment_payeer_sort_order'] = $this->request->post['payment_payeer_sort_order'];
    } else {
      $data['payment_payeer_sort_order'] = $this->config->get('payment_payeer_sort_order');
    }

    $payment_payeer_url_val = $this->config->get('payment_payeer_url');
    if(!isset($payment_payeer_name_val)){
      $data['payment_payeer_url'] = 'https://payeer.com/merchant/';
    }

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $this->response->setOutput($this->load->view('extension/payeer/payment/payeer', $data));
  }

  private function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payeer/payment/payeer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['payment_payeer_url']) {
			$this->error['url'] = $this->language->get('error_url');
		}
		if (!$this->request->post['payment_payeer_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		if (!$this->request->post['payment_payeer_security']) {
			$this->error['security'] = $this->language->get('error_security');
		}
    return !$this->error;
  }
}
