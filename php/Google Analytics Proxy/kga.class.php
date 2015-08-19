<?php


class kga {
	private $url;
	private $error;
	private $account;

	public function __constructor($dbc) {
		global $_SERVER;
		$this->error = 0;
		$this->account = "";
		list($domain, $folder, $php) = explode("/", str_replace("http://", "", $_SERVER["SCRIPT_URI"]));
		$this->url = $domain."/".$folder."/";

		$sql = " SELECT a.ga_account FROM stats.gn_portals INNER JOIN stats.ga_accounts a ON (a.gn_portal=p.id) WHERE p.base_url='$this->url' AND a.active='1' ";
		$rs = mysql_query($sql, $dbc->db);
		if (!$rs) {
			$this->error = 1;
		} else {
			if ($obj = mysql_fetch_object($rs)) {
				$this->account = $obj->ga_account;
			} else {
				$this->error = 2;
			}
		}
		return $this->reportError();
	}


	public function __destructor() {
		return $this->reportError();
	}


	public function getAccount() {
		if (($this->error == 0) && ($this->account != "")) {
			return $this->account;
		} else {
			return FALSE;
		}
	}


	private function reportError() {
		switch ($this->error) {
			case "1":
				// error consultando db en constructor

			break;
			case "2":
				// query ok pero no se encontraron datos para la url

			break;
			case "0":
			default:
				// no hay error
		}
	}

}



?>