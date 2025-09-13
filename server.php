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
	function convertToCSV () {
		$sortCSV = array('unit', 'sub', 'name', 'surname', 'alias', 'login', 'password', 'groups');
		$temp = str_replace('%groups%', '%groups% %add_groups%', '%'.implode('%;%', $sortCSV).'%');
        file_put_contents('users.csv', '');
		foreach ((new DirectoryIterator('users')) as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $res = array();
            foreach (json_decode(file_get_contents('users/'.$file->getBasename())) as $login => $person) {
                $person = (array)$person;
                $person['login'] = $login;
                $str = $temp;
                foreach ($person as $key => $val) {
                    $str = str_replace('%'.$key.'%', $val, $str);
                }
                $res[] = trim($str);
            }
            file_put_contents('users.csv', implode("\n", $res)."\n",FILE_APPEND);
        }
	}
	function convertToPrint () {
		$temp = '%alias%, логин: %login% пароль: %password%';
        foreach ((new DirectoryIterator('users/print')) as $file) {
            @unlink('users/print/'.$file->getBasename());
        }
        foreach ((new DirectoryIterator('users')) as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $res = array($file->getBasename('.json'));
            foreach (json_decode(file_get_contents('users/'.$file->getBasename())) as $login => $person) {
                $person = (array)$person;
                $person['login'] = $login;
                $str = $temp;
                foreach ($person as $key => $val) {
                    $str = str_replace('%'.$key.'%', $val, $str);
                }
                $res[] = trim($str);
            }
            file_put_contents('users/print/'.$file->getBasename('.json').'.txt', implode("\n", $res));
		}
	}

	if (isset($_REQUEST['get_css'])) {
		echo file_get_contents('./users.csv', '');
		exit;
	} elseif ($_REQUEST['list_user_group']??false) {
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
			convertToCSV();
			convertToPrint();
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
				convertToCSV();
				convertToPrint();
            }
        }
        echo json_encode($result);
    } elseif ($_REQUEST['list_users']??false) {
		echo file_get_contents('users/'.$_REQUEST['file']);
	} elseif ($_REQUEST['save_users']??false) {
		//$data = $_POST['json']?: [];
		$json_input = file_get_contents('php://input');
    	$data = json_decode($json_input, true);
        file_put_contents('users/'.$_REQUEST['file'], json_encode($data['json'], JSON_UNESCAPED_UNICODE));
		convertToCSV();
		convertToPrint();
		echo json_encode([
			'status' => 'ok', 
		]);
	} elseif ($_REQUEST['list_groups']??false) {
		echo file_get_contents('groups/groups.json');
	} elseif ($_REQUEST['list_ous']??false) {
		echo file_get_contents('ous/ous.json');
	}

	