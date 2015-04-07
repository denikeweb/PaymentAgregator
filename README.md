# PaymentAgregator

Open source framework for payment system integraion. 
Has implementation for InterKassa and RoboKassa. 
I haven't demo appication — I used framework only at production.
Demo codes below.

## Redirect Page
```php
			$orderObj = new \Logic\App\Orders();
			$order_id = $orderObj->createOrder ();

			$paymentSystem = new \Annex\Payments\InterkassaService();
			$paymentSystem->setCurrency("UAH");
			$paymentSystem->setDesc('Service or product description');
			$paymentSystem->setSumm($orderObj->price);
			$paymentSystem->setOrderId($order_id);
			$paymentSystem->setEmail($orderObj->email);
			// $paymentSystem->setDebugMode();
			$this->data ['content'] = $paymentSystem->redirect();
```
			
## Fail Page
```php
			$paymentSystem = $this->getPaymentSystem ();
			$this->data ['content'] = $paymentSystem->messFail();
```

## Pending Page
```php
			$paymentSystem = $this->getPaymentSystem ();
			$this->data ['content'] = $paymentSystem->messPending();
```
			
## Success Page
```php
			$paymentSystem = $this->getPaymentSystem ();
			$this->data ['content'] = $paymentSystem->messSuccess();
```

## Authors

[Denis Dragomirik](https://github.com/denikeweb)
