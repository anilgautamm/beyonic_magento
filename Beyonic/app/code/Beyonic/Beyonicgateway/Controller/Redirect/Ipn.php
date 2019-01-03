<?php

namespace Beyonic\Beyonicgateway\Controller\Redirect;

use Magento\Sales\Model\Order;

class Ipn extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_url = $context->getUrl();
        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        $data_received = file_get_contents('php://input');
        $decoded_data = json_decode($data_received, true);
        $transaction_id = $decoded_data['transactionId'];
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $ptable = $resource->getTableName('sales_order_payment');
        $query = 'SELECT parent_id FROM ' . $resource->getTableName('sales_order_payment') . ' WHERE last_trans_id = "' . $transaction_id . '"';
        $oid = $connection->fetchOne($query);
        $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($oid);
        $order->setState(Order::STATE_PAYMENT_REVIEW)->setStatus(Order::STATE_PAYMENT_REVIEW);
        $order->save();
    }

}
