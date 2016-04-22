<?php

require 'vendor/autoload.php';

$app = new Slim\App(array('settings' => array('displayErrorDetails' => true)));
date_default_timezone_set('America/Montevideo');

define('DB_HOST', 'localhost');

if($_SERVER['HTTP_HOST'] == 'api.erp.personal') {
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('DB_NAME', 'api-db');
} else {
	define('DB_USER', 'erpapiwex');
	define('DB_PASS', '220506MsPg');
	define('DB_NAME', 'erp_api_db');
}

$status_error   = array('status' => 'error', 'message' => 'An error occurred');
$status_success = array('status' => 'success');

$app->get('/', function($request, $response) {
	echo 'Welcome';
	return $response;
});

// Post Login (username, password);
$app->post('/login/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$data   = $request->getParsedBody();
	$fields = array('id', 'email', 'first_name', 'last_name', 'user_name');

	$db->where('user_name', $data['username']);
	$db->where('password', md5($data['password'].'##'.$data['username']));

	$result = $db->getOne('users', $fields);

	if (count($result)) {
		$status_success['data'] = array('user' => $result);
		echo json_encode($status_success);
	} else {
		$status_error['message'] = 'User does not exists';
		echo json_encode($status_error);
	}

    return $response;
});

// Get all Products
$app->get('/products/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = $db->get('products');

	$status_success['data'] = array('products' => $result);
	echo json_encode($result);

	return $response;
});

// Get products by page
$app->get('/products/page/{page}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db            = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$page          = $args['page'];
	$db->pageLimit = 20;
	$result        = $db->arraybuilder()->paginate('products', $page);

	$status_success['data'] = array('products' => $result);
	echo json_encode($result);

	return $response;
});

$app->get('/product/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('id', $args['id']);
	$result = $db->getOne('products');

	echo json_encode($result);

	return $response;
});

$app->get('/product/barcode/{barcode}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('bar_code', $args['barcode']);
	$result = $db->getOne('products');

	echo json_encode($result);

	return $response;
});

// Post Product save
$app->post('/product/save/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$data = $request->getParsedBody();
	$id   = $db->insert('products', $data);

	echo json_encode($status_success);

	return $response;
});

$app->post('/product/update/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$data = $request->getParsedBody();

	$db->where('id', $args['id']);
	$db->update('products', $data);

	echo json_encode($status_success);

	return $response;
});

$app->get('/product/delete/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('id', $args['id']);
	$db->delete('products');

	echo json_encode($status_success);

	return $response;
});

$app->get('/clients/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = $db->get('clients');

	$status_success['data'] = array('clients' => $result);
	echo json_encode($result);

	return $response;
});

$app->get('/clients/page/{page}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db            = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$page          = $args['page'];
	$db->pageLimit = 20;
	$result        = $db->arraybuilder()->paginate('clients', $page);

	$status_success['data'] = array('clients' => $result);
	echo json_encode($result);

	return $response;
});

$app->get('/client/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('id', $args['id']);
	$result = $db->getOne('clients');

	echo json_encode($result);

	return $response;
});

$app->get('/client/bydocument/{document}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db     = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('document', $args['document']);
	$result = $db->getOne('clients');

	echo json_encode($result);

	return $response;
});

$app->post('/client/save/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$data = $request->getParsedBody();
	$id   = $db->insert('clients', $data);

	echo json_encode($status_success);

	return $response;
});

$app->post('/client/update/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$data = $request->getParsedBody();

	$db->where('id', $args['id']);
	$db->update('clients', $data);

	echo json_encode($status_success);

	return $response;
});

$app->get('/client/delete/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db   = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$db->where('id', $args['id']);
	$db->delete('clients');

	echo json_encode($status_success);

	return $response;
});

$app->post('/transaction/save/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$db          = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$transaction = $request->getParsedBody();
	$transProds  = (!empty($transaction['ticket']) && !empty($transaction['ticket']['products']) ? $transaction['ticket']['products'] : null);

	$transToSave = array(
		'subtotal'   => $transaction['ticket']['subtotal'],
		'discount'   => $transaction['ticket']['discount'],
		'taxes'      => $transaction['ticket']['taxes'],
		'total'      => $transaction['ticket']['total']
	);

	$tProdToSave = array();

	if(!empty($transaction['ticket']['id'])) {
		$transId = $transaction['ticket']['id'];
		$db->where('id', $transId);
		$db->update('transactions', $transToSave);
	} else {
		$transId = $db->insert('transactions', $transToSave);
	}

	if(!empty($transId) && !empty($transProds)) {
		foreach($transProds as $product) {
			$addP = array(
				'id_product'     => $product['id'],
				'id_transaction' => $transId,
				'amount'         => 1,
				'sell_price'     => $product['sell_price'],
				'subtotal'       => $product['sell_price']
			);
			$db->insert('transaction_products', $addP);
		}

		$db->where('id', $transId);
		$status_success['transaction'] = $db->getOne('transactions');
		echo json_encode($status_success);
	} elseif(!empty($transId)) {
		$db->where('id', $transId);
		$status_success['transaction'] = $db->getOne('transactions');
		echo json_encode($status_success);
	} else {
		echo json_encode($status_error);
	}

	return $response;
});

$app->post('/transaction/pay/{id}/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$transactionDd      = $args['id'];
	$transactionPayment = $request->getParsedBody()['transaction'];

	$db                 = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$transUpdate        = array(
		'pay_type'      => !empty($transactionPayment['pay_type']) ? $transactionPayment['pay_type'] : '',
		'pay_amount'    => !empty($transactionPayment['pay_amount']) ? $transactionPayment['pay_amount'] : 0,
		'pay_change'    => !empty($transactionPayment['pay_change']) ? $transactionPayment['pay_change'] : 0
	);

	$db->where('id', $transactionDd);
	$db->update('transactions', $transUpdate);

	echo json_encode($status_success);
	return $response;
});

$app->post('/transaction/{id}/update-client/', function ($request, $response, $args) {
	global $status_error, $status_success;

	$transactionDd = $args['id'];
	$clientId      = $request->getParsedBody()['clientid'];
	$db            = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$transUpdate   = array(
		'id_client' => $clientId
	);

	$db->where('id', $transactionDd);
	$db->update('transactions', $transUpdate);

	echo json_encode($status_success);
	return $response;
});

// $app->get('/ticket/addproduct/{productid}/', function($response, $response, $args) {
// 	var_dump($args['productid']);
// });

$app->run();

?>