<?php

namespace Beyonic\Beyonicgateway\Controller\Redirect;

define("BEYONIC_CLIENT_VERSION", "0.0.9");

class Index extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
	protected $_scopeConfig;
	protected $_url;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_url = $context->getUrl();
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$this->authorize_beyonic_gw();
		$orderId = $this->_getCheckout()->getLastRealOrderId();
		$this->_orderFactory = $this->_objectManager->get('Magento\Sales\Model\OrderFactory');
		$order = $this->_orderFactory->create()->loadByIncrementId($orderId);

		$total = $order->getGrandTotal();
		$total_ammount_formatted = number_format($total, "2", ".", "");
		$billingAddress = $order->getBillingAddress();
		$ShippingAddress = $order->getShippingAddress();

		//get saved data
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$beyonic_ipn_url = $storeManager->getStore()->getBaseUrl() . 'beyonicgateway/redirect/ipn/';

		$url = str_replace("http:", "https:", $beyonic_ipn_url);
		$get_hooks = \Beyonic_Webhook::getAll();

		$hook_exist = FALSE;
		if (!empty($get_hooks['results'])) {
			foreach ($get_hooks['results'] as $key => $get_hook) {
				if ($get_hook->target == $url) {
					$hook_exist = TRUE;
					break;
				}
			}
		}
		if ($hook_exist == FALSE) {
			try {
				$hooks = \Beyonic_Webhook::create(array(
					"event" => "collection.received",
					"target" => "$url",
				));
			} catch (\Exception $e) {
				$errorMsg = 'Your payment could not be processed.Please try again later.';
				$this->messageManager->addError(__($errorMsg));
				return $this->resultRedirectFactory->create()->setUrl($this->_url->getUrl('checkout/onepage/failure'));
			}
		}

		try {
//             $currency = $order->getBaseCurrencyCode();
			$request = \Beyonic_Collection_Request::create(array(
				"phonenumber" => $billingAddress->getTelephone(),
				"first_name" => $billingAddress->getFirstName(),
				"last_name" => $billingAddress->getLastName(),
				"email" => $billingAddress->getEmail(),
				"amount" => $total_ammount_formatted,
				"success_message" => 'Thank you for your payment!',
				"send_instructions" => true,
				"currency" => $order->getBaseCurrencyCode(),
				"metadata" => array("order_id" => $orderId),
			));

			$beyonic_collection_id = intval($request->id);

			$note = 'Order payment pending.';
			$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($order->getId());
			$payment = $order->getPayment();

			$payment->setTransactionId($beyonic_collection_id)
				->setCurrencyCode($order->getBaseCurrencyCode())
				->setPreparedMessage($note)
				->setParentTransactionId($beyonic_collection_id)
				->setShouldCloseParentTransaction(true)
				->setIsTransactionClosed(0)
				->registerCaptureNotification($total_ammount_formatted);
			$order->setState('payment_pending')->setStatus('payment_pending');
			$order->save();
			$this->messageManager->addSuccess(__('<p style="color:red; font-weight:bold;">Note: Payment instructions have been sent to your phone ' . $billingAddress->getTelephone() . '. Please check your phone to complete the payment.<br>Your order cannot be delivered until you complete the payment on your phone.</p>'));
			return $this->resultRedirectFactory->create()->setUrl($this->_url->getUrl('checkout/onepage/success'));
		} catch (\Exception $e) {
			$json = json_decode($e->responseBody);
			$error = '';
			if (!empty($json)) {
				foreach ($json as $value) {
					$error = $value[0];
					break;
				}
			}

			$note = 'Order payment canceled.';
			$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($order->getId());
			$payment = $order->getPayment();
			$payment->setPreparedMessage($note);
			$order->setState('canceled')->setStatus('canceled');
			$order->save();
			$errorMsg = $error . ' Your payment could not be processed.Please try again later.';
			$this->messageManager->addError(__($errorMsg));
			return $this->resultRedirectFactory->create()->setUrl($this->_url->getUrl('checkout/onepage/failure'));
		}
	}

	private function authorize_beyonic_gw() {

		$apikey = $this->_scopeConfig->getValue('payment/beyonicgateway/apikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$version = 'v1';
		\Beyonic::setApiVersion($version);
		\Beyonic::setApiKey($apikey);
	}

	protected function _getCheckout() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManager->get('Magento\Checkout\Model\Session');
	}

}
