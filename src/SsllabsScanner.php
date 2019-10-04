<?php

namespace SSLLabs\Laravel;


use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Andyftw\SSLLabs\Api;

class SsllabsScanner
{

	private $ssllabsApi = null;

	private $url = null;
	private $state = "NEW";
	private $result = null;

    public function __construct(Api $ssllabsApi)
    {
    	Log::debug('Constructing ssllabsscanner');
    	$this->ssllabsApi = $ssllabsApi;
    } 

    public function scan($url)
    {
    	$this->url = $url;
    	try {
	    	// analyze($host, $publish, $startNew, $fromCache, $maxAge, $all, $ignoreMismatch )
	    	// @see: https://github.com/ssllabs/ssllabs-scan/blob/stable/ssllabs-api-docs.md#invoke-assessment-and-check-progress
	    	$this->ssllabsApi->analyze($url, false, true, false, 0);
	        
	    	// Give SSL ten seconds to start
	        sleep(10);
		
	        while ($this->state != 'READY') {
				// Fetch data from api
				$host = $this->ssllabsApi->analyze($url);
				$currentStatus = $host->getStatus();
				$currentMessage = $host->getStatusMessage();

				foreach($host->getEndpoints() as $endpoint) {
					Log::debug('endpoint: ' . $endpoint->getIpAddress() . ' status details: ' . $endpoint->getStatusDetails());
				}
		        	// update the current state
		        	$this->state = $currentStatus;
		        	// SSLLabs isn't ready
		            sleep(10);
	        }

	        // update the current state
	        $this->state = $currentStatus;
	        $this->result = $this->ssllabsApi->analyze($url);
	        return $this->result;
	    } catch(\Exception $e) {
	    	Log::error('SSL scan error for url: ' . $url . " message: " . $e->getMessage());
	    	$this->state = "FAILED";
	    	return false;
	    }
    }

    public function endpointData($url, $endpoint)
    {
    	return $this->ssllabsApi->getEndpointData($url, $endpoint);
    }

    public function getState()
    {
    	return $this->state;
    }

    public function getUrl() 
    {
    	return $this->url;
    }

    public function getResult()
    {
    	return $this->result;
    }
}
