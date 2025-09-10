<?php
	// Указываем заголовок для возврата JSON
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Credentials: true");
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');

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
		file_put_contents('users/' . $_REQUEST['name'] . '.json', '{}');
		echo json_encode([
			'status' => 'ok', 
			'label' => $_POST['name'],
			'path' => $_POST['name'].'.json'
		]);
	} elseif (isset($_REQUEST['up_user_group'])??false) {
        $result = ['status' => 'error'];
		if (isset($_REQUEST['file'])) {
            if (preg_match('/(users|class)_([0-9]{1,2})(\w)\.json/', $_REQUEST['file'], $matches)) {
                $matches[4] = str_replace(array('a', 'b', 'v', 'g', 'd'), array('а', 'б', 'в', 'г', 'д'), $matches[3]);
                $pupils = json_decode(file_get_contents('users/'.$_REQUEST['file']));
                foreach ($pupils as &$pupil) {
                    $pupil = (array)$pupil;
                    $pupil['sub'] = ((int)$matches[2] + 1).$matches[4];
                    $pupil['add_groups'] = str_replace('class_'.$matches[2].$matches[3], 'class_'.((int)$matches[2] + 1).$matches[3], $pupil['add_groups']);
                }
                @unlink('users/'.$_REQUEST['file']);
                file_put_contents('users/'.$matches[1].'_'.((int)$matches[2] + 1).$matches[3].'.json', json_encode($pupils));
				$result['status'] = 'ok';
				$result['label'] = $matches[1].'_'.((int)$matches[2] + 1).$matches[3];
				$result['path'] = $result['label'].'.json';
            }
        }
        echo json_encode($result);
    } elseif ($_REQUEST['list_users']??false) {
		echo file_get_contents('users/'.$_REQUEST['file']);
	} elseif ($_REQUEST['save_users']??false) {
		$data = $_POST['json']?: [];
		var_dump($data);
        //file_put_contents('users/'.$_POST['file'], json_encode($data));
		echo json_encode([
			'status' => 'ok', 
		]);
	} elseif ($_REQUEST['list_groups']??false) {
		echo file_get_contents('groups/groups.json');
	} elseif ($_REQUEST['list_ous']??false) {
		echo file_get_contents('ous/ous.json');
	}

	