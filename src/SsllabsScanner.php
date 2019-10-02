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
	    	$this->ssllabsApi::analyze($url, false, true, false, 0);
	        
	    	// Give SSL ten seconds to start
	        sleep(10);

	        $currentState = $this->ssllabsApi::analyze($url)->getStatus();
	        while ($currentState != 'READY') {
	        	// update the current state
	        	$this->state = $currentState;
	        	// SSLLabs isn't ready
	            sleep(5);
	        }

	        // update the current state
	        $this->state = $currentState;
	        $this->result = $this->api::analyze($url);
	        
	        return $this->result;
	    } catch(\Exception $e) {
	    	Log::error('SSL scan error for url: ' . $url . " message: " . $e->getrMessage());
	    	$this->state = "FAILED";
	    	return false;
	    }
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