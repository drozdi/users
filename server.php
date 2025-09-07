<?php
	// Указываем заголовок для возврата JSON
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
	header("Access-Control-Allow-Headers: X-Requested-With");

	/*if (empty($requestedPath)) {
		http_response_code(400);
		echo json_encode(['error' => 'Parameter "path" is required']);
		exit;
	}*/

	if ($_REQUEST['list_user_group']??false) {
		$result = [];
		foreach ((new DirectoryIterator('users')) as $file) {
			if ($file->isDot() || $file->isDir()) {
                continue;
            }
			$result[] = [
				'label' => $file->getBasename('.json'),
				'path' => $file->getBasename()
			];
		}
		ksort($result);
		echo json_encode($result);
	} elseif ($_REQUEST['remove_user_group']??false) {
		if (isset($_REQUEST['file'])) {
            @unlink('users/'.$_REQUEST['file']);
        }
		echo json_encode(['status' => 'ok']);
	} elseif ($_REQUEST['add_user_group']??false) {
		file_put_contents('users/' . $_POST['name'] . '.json', '{}');
		echo json_encode([
			'status' => 'ok', 
			'label' => $_POST['name'],
			'path' => $_POST['name'].'.json'
		]);
	} elseif ($_REQUEST['list_groups']??false) {
		echo file_get_contents('groups/groups.json');
	}

	