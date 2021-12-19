<?php
switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		switch ($_GET['query']) {
			case 'version':
				$result = shell_exec("ps aux | grep -oE 'minecraft_server.[[:digit:]]{1,2}.[[:digit:]]{1,2}.[[:digit:]]{1,2}.jar' | head -n1 | awk -F. '{ print $2"."$3"."$4 }'");
				if ($result === false || is_null($result))
					$response = array('status' => 'error', 'error' => 'Version query command failed or returned empty');
				else
					$response = array('status' => 'success', 'response' => $result);
				break;
			case 'status':
				$output = null;
				$retcode = null;
				$result = exec("ps -ax -o stat,comm", $output, $retcode);
				if ($result === false || $retcode != 0)
					$response = array('status' => 'error', 'error' => 'Status query command failed');
				foreach ($output as $line) {
					if (strpos($line, 'java')) {
						if ($line[0] == 'S')
							$response = array('status' => 'success', 'response' => 'up');
						elseif ($line[0] == 'T')
							$response = array('status' => 'success', 'response' => 'paused');
						else
							$response = array('status' => 'error', 'error' => 'Could not determine java process state');

						break;
					}
				}
				if (!isset($response))
					$response = array('status' => 'success', 'response' => 'down');
				break;
			default:
				$response = array('status' => 'error', 'error' => 'Invalid query');
		}
		break;

	case 'POST':
		switch ($_POST['command']) {
			case 'server_on':
				$response = array('status' => 'success');
				break;
			case 'server_off':
				$response = array('status' => 'success');
				break;
			case 'server_restart':
				$response = array('status' => 'success');
				break;
			case 'server_update':
				$response = array('status' => 'success');
				break;
			default:
				$response = array('status' => 'error', 'error' => 'Invalid command');
		}
		break;

	default:
		$response = array('status' => 'error', 'error' => 'Invalid request method');
}
header('Content-type: application/json');
echo json_encode($response);
?>