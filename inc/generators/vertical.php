<?php

require_once 'WBFInstaller.php';
require_once 'WabootGenerator.php';

class VerticalGenerator extends WabootGenerator {
	use WBFInstaller;

	const DOWNLOAD_URL = 'http://update.waboot.org/resource/get/plugin/wbf';
}