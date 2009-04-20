<?php

class YahooGeocodingComponent extends Object {

	var $http;
	var $req;

	var $app_id;
	var $api_url = 'http://local.yahooapis.com/MapsService/V1/geocode';
	var $response = 'php';
	
	var $meth = "get";
	
	//Setup the basics
	function startup(&$controller) 
	{
		//not sure why I originally moved this here, put it back into the request() function
		require_once('vendors/HTTP/Request.php');
		
		$this->req = new Http_Request();
		
		$this->app_id = Configure::read('Yahoo.app_id');
	}
	
	function getGeocode($address)
	{
		$url = $this->api_url.'?appid='.$this->app_id;
		
		$runit = false;
		
		if(isset($address['street']) && $address['street']!='')
		{
			$runit = true;
			$url .= '&street='.urlencode($address['street']);
		}
		
		if(isset($address['city']) && $address['city']!='')
		{
			$runit = true;
			$url .= '&city='.urlencode($address['city']);
		}
		
		if(isset($address['state']) && $address['state']!='')
		{
			$runit = true;
			$url .= '&state='.urlencode($address['state']);
		}
		
		if(isset($address['zip']) && $address['zip']!='')
		{
			$runit = true;
			$url .= '&zip='.urlencode($address['zip']);
		}
		
		$url .= '&output=php';
		
		if($runit)
		{
			//set the URL for the request 
			$this->req->setURL($url);
		} else {
			$this->cakeError('getGeocode', 'Sorry, but you need to set parameters in ADDRESS in order to get a geocode response.');
		}
		//set the method
		$this->req->setMethod($this->meth);

		//send the request
		if ($res = $this->req->sendRequest())
		{
			//pr($this->req);
			// FIXME: error nicely.
			if (PEAR::isError($res))  
				die("PEAR ERROR: " . $res->getMessage() . "\n");
			
			$php_arr = unserialize($this->req->getResponseBody());
			
			if(isset($php_arr['ResultSet']['Result']))
			{
				return $php_arr['ResultSet'];
			} else {
				return false;
			}
		}
		else
			die("Didn't get request\n");
	}
}

?>