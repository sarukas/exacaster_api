<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

/**
 * Exacaster Metrics PHP API.
 *
 * @version 0.0.2
 * @copyright Exacaster.com
 */
class ExacasterMetrics {
  /**
	 * Mandatory parameter list.
	 *
	 * @var array
	 */
	private $_data = array();
	/**
	 * User-defined persisting parameters.
	 *
	 * @var array
	 */
	private $_params = array();
	/**
	 * @var string Name for the agent
	 */
	private $_agent;

	private $_trackingUrl = 'trk.exacaster.com/e';

	public function __construct($apiKey) {
		if (!$apiKey) {
			throw new Exacaster_NoApiKeyException('No API key given');
		}
		$this -> _data['_x'] = $apiKey;
	}

	public function useTransferAgent($agentName) {
		if (method_exists($this, '_send_' . $agentName)) {
			$this -> _agent = $agentName;
		} else {
			throw new Exacaster_AgentNotFoundException(sprintf('Exacaster Metrics agent "%s" does not exist', $agentName));
		}
	}

	public function identify($id) {
		$this -> _data['_p'] = $id;
	}

	public function set($values) {
		$this -> _params = array_merge($this -> _params, (array)$values);
	}

	public function setTrackingUrl($url) {
		$url = preg_replace('#^https?://#i', '', $url);
		$this -> _trackingUrl = $url;
	}

	public function record($event, $params = array()) {
		$toSend = array_merge($this -> _params, $params, $this -> _data, array('_n' => $event));

		$this -> _send($toSend);
	}

	public function alias($aliasName, $name) {
		$params = array_merge($this -> _data, array('_cmd' => 'alias', 'alias' => $aliasName, 'name' => $name, ));

		$this -> _send($params);
	}

	// Sending agent helper functions

	protected function _buildSearch($params) {
		$queryParams = array();

		foreach ($params as $key => $val) {
			if (is_string($val) || is_numeric($val)) {
				$queryParams[$key] = $val;
			}
		}

		return http_build_query($queryParams);
	}

	protected function _buildUrl($params) {
		return 'http://' . $this -> _trackingUrl . '?' . $this -> _buildSearch($params);
	}

	/**
	 * Delegates sending to the chosen agent.
	 */
	private function _send($params) {
		if (!$this -> _agent) {
			throw new Exacaster_AgentNotFoundException('Exacaster Metrics agent not defined');
		}

		$methodName = '_send_' . $this -> _agent;
		call_user_func(array($this, $methodName), $params);
	}

	// Sending agents

	private function _send_test($params) {
		echo 'Requesting: ' . $this -> _buildUrl($params) . ' <br />' . PHP_EOL;
	}

	private function _send_fsock($params) {
		$fp = @fsockopen($this -> _host, 80, $errno, $errstr, $this -> _timeout);

		if (!$fp) {
			// Request failed.
			return;
		}

		$uri = $this -> _path . '?' . $this -> _buildSearch($params);

		$out = "GET {$uri} HTTP/1.1\r\n";
		$out .= "Host: {$this->_host}\r\n\r\n";

		// We're not interested in the response. Just send the request, close and return.
		fwrite($fp, $out);
		fclose($fp);
	}

	private function _send_wget($params) {
		$arg = escapeshellarg($this -> _buildUrl($params));
		exec("wget -O - {$arg} > /dev/null 2>&1 &");
	}

}

// Exceptions
class Exacaster_AgentNotFoundException extends Exception {

}

class Exacaster_NoApiKeyException extends Exception {

}

?>
