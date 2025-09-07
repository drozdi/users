<?php
	// Указываем заголовок для возврата JSON
	header('Content-Type: application/json');

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
			$result[$file->getBasename()] = $file->getBasename('.json');
		}
		ksort($result);
		echo json_encode($result);
	} elseif ($_REQUEST['list_groups']??false) {
		echo file_get_contents('groups/groups.json');
	}

	