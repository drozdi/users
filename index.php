<?php
@session_start();
$login = false;
if (isset($_REQUEST['get_css'])) {
    echo file_get_contents('./users.csv', '');
    exit;
 } elseif ($_SESSION['start']??false) {
    $login = true;
    $head = array(
        'name' => array(
            'title' => "Имя",
        ),
        'surname' => array(
            'title' => "Фамилия",
        ),
        'alias' => array(
            'title' => "Alias",
        ),
        'password' => array(
            'title' => "Пароль",
        ),
        'unit' => array(
            'title' => "Подразделение",
            'values' => array(
                'tsi' => 'Учителя',
                'as' => 'Администрация',
                'ess' => 'Вспом. перс.',
                'pupils' => 'Ученики',
				'_users' => 'Users'
            ),
        ),
        'sub' => array(
            'title' => "Папка",
        ),
        'groups' => array(
            'title' => "Группы",
            'multiple' => true,
            'values' => array(
                'chief' => "Директор",
                'accountants' => "Бухгалтеры",
                'chief_accountant' => "Главный бухгалтер",
                'secretaries' => "Cекретари",
                'teachers' => "Учителя",
                'pupils' => "Ученики",
                'responsible_for_asiou' => "Ответственный за АСИОУ",
                'deputy' => "Зам",
                'deputy_el' => "Зам по НШк",
                'deputy_ew' => "Зам по ВР",
                'deputy_mw' => "Зам по МР",
                'deputy_so' => "Зам по ОБ",
                'deputy_tew' => "Зам по УВР",
                'deputy_sm' => "Зам по АХР",
                'ma' => "Методическое объединение",
                'ma_el' => "Методические объединения начальных классов",
                'ma_es' => "Методические объединения педагогов специалистов",
                'ma_ex' => "Методические объединения точных наук",
                'ma_fl' => "Методические объединения иностранных языков",
                'ma_hb' => "Методические объединения фито",
                'ma_hm' => "Методическое объединение гуманитарных наук",
                'ma_nt' => "Методические объединение естественных наук",
                'ma_ct' => "Методические объединения классных руководителей",
            )
        ),
        'add_groups' => array(
            'title' => "Доп. группы"
        )
    );
    $sortCSV = array('unit', 'sub', 'name', 'surname', 'alias', 'login', 'password', 'groups');
    $loginIndexCSV = null;
    $implodeIndexCSV = array('groups');
    for ($i = 0, $cnt = count($sortCSV); $i < $cnt; $i++) {
        if ("login" == $sortCSV[$i]) {
            $loginIndexCSV = $i;
        }
    }
    if (isset($_POST['update_ldap'])) {
	echo "<pre>";
        var_dump(system('perl /home/school/user.pm'));
        var_dump(system('/home/school/user.corect.sh'));
	echo "</pre>";
    } elseif (isset($_REQUEST['remove'])) {
        if (isset($_REQUEST['file'])) {
            @unlink('users/'.$_REQUEST['file']);
        }
        header('Location: index.php');
    } elseif (isset($_REQUEST['up'])) {
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
                file_put_contents('users/'.$matches[1].'_'.((int)$matches[2] + 1).$matches[3].'.json', json_encode($pupils, JSON_UNESCAPED_UNICODE));
            }
        }
        header('Location: index.php');
    } elseif (isset($_POST['new_file'])) {
        file_put_contents('users/' . $_POST['name'] . '.json', '{}');
        exit;
    } elseif (isset($_POST['add']) && isset($_POST['file'])) {
        $person = array();
        $persons = json_decode(file_get_contents('users/'.$_POST['file']));
        $persons = (array)$persons;
        foreach ($persons as &$p) {
            $p = (array)$p;
        }
        foreach ($head as $key => $v) {
            if (!empty($v['values']) && is_array($_POST[$key])) {
                $person[$key] = implode(' ', $_POST[$key]);
            } else {
                $person[$key] = $_POST[$key];
            }
        }
        $persons[$_POST['login']] = $person;
        file_put_contents('users/'.$_POST['file'], json_encode($persons, JSON_UNESCAPED_UNICODE));
        header('Location: index.php?file='.$_POST['file'].'&unit='.$_POST['unit'].'&sub='.$_POST['sub']);
    } elseif (isset($_POST['json']) && isset($_POST['save']) && isset($_POST['file'])) {
        $data = $_POST['json']?: array();
        foreach ($data as $id => &$person) {
            $person['groups'] = implode(' ', $person['groups']??array());
        }
        file_put_contents('users/'.$_POST['file'], json_encode($data, JSON_UNESCAPED_UNICODE));
    } elseif (isset($_POST['convert_to_csv'])) {
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
    } elseif (isset($_POST['convert_to_print'])) {
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
    } elseif (isset($_POST['convert_from_csv'])) {
        $persons = (array)json_decode(file_get_contents('users/'.$_POST['file']));
        $list = array_filter(array_map(function ($str) {
            if (!($str = trim($str))) {
                return;
            }
            $str = explode(';', $str);
            global $head, $persons, $sortCSV, $loginIndexCSV, $implodeIndexCSV;
            if (empty($loginIndexCSV) || isset($persons[$str[$loginIndexCSV]])) {
                return;
            }
            $res = array();
            foreach ($sortCSV as $i => $key) {
                $res[$key] = trim($str[$i]);
            }
            foreach ($implodeIndexCSV as $key) {
                if (empty($head[$key]['values'])) {
                    continue;
                }
                $all = explode(' ', $res[$key]);
                $res[$key] = '';
                $res['add_'.$key] = '';
                foreach ($all as $val) {
                    if (isset($head[$key]['values'][$val])) {
                        $res[$key] .= ' '.$val;
                    } elseif ($val) {
                        $res['add_'.$key] .= ' '.$val;
                    }
                }
                $res[$key] = trim(str_replace('  ', ' ', $res[$key]));
                $res['add_'.$key] = trim(str_replace('  ', ' ', $res['add_'.$key]));
            }
            return $res;
        }, explode("\n", (string)$_POST['list'])), function ($val) {
            return !empty($val);
        });
        foreach ($list as $person): $person = (array)$person; $id = $person['login']; ?>
            <tr id="tr_<?=$id;?>">
                <td>
                    <button class="remove btn btn-danger"><?=$id;?></button>
                </td>
                <?php foreach ($head as $key => $field): ?>
                    <td>
                        <?php if (!empty($field['values']) && true === $field['multiple'] && false): $person[$key] = explode(' ', $person[$key]); ?>
                            <?php foreach ($field['values'] as $val => $name): ?>
                                <label>
                                    <input type="checkbox" id="f_<?=$id;?>_<?=$key;?>_<?=$val;?>" name="json[<?=$id;?>][<?=$key;?>][]" value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'checked': '');?> >
                                    <?=$name;?>
                                </label>
                            <?php endforeach; ?>
                        <?php elseif (!empty($field['values'])): $person[$key] = explode(' ', $person[$key]);?>
                            <select class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]<?=(true === $field['multiple']? '[]': '');?>" <?=(true === $field['multiple']? 'multiple': '');?>>
                                <?php foreach ($field['values'] as $val => $name): ?>
                                    <option value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'selected': '');?>><?=$name;?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]" value="<?=$person[$key];?>" />
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?></tr>
        <?php endforeach;
        exit;
    } elseif (isset($_POST['convert_from_asiou'])) {
        $persons = (array)json_decode(file_get_contents('users/'.$_POST['file']));
        $unit = (string)$_POST['unit'];
        $sub = (string)$_POST['sub'];

        $groups = '';
        $addGroups = '';
        if ('tsi' === $unit) {
            $groups = 'teachers';
        } elseif ('pupils') {
            $groups = 'pupils';
            $addGroups = 'class_'.str_replace(array('а', 'б', 'в', 'г', 'д'), array('a', 'b', 'v', 'g', 'd'), $sub);
        }

        $list = array_filter(array_map(function ($str) {
            if (strlen($str) < 12) {
                return;
            }
            global $persons, $groups, $addGroups, $unit, $sub;
            preg_match('#(([\W]{1,})\s([\W]{1,} [\W]{1,})), логин: ([\w]{1,}) пароль: ([\w]{1,})#', $str, $matches);
            if (isset($persons[$matches[4]])) {
                return;
            }
            if ('pupils' === $unit) {
                $matches[5] = $matches[4];
            }
            return array(
                'alias' => $matches[1],
                'surname' => $matches[2],
                'name' => $matches[3],
                'login' => $matches[4],
                'password' => $matches[5],
                'sub' => $sub,
                'unit' => $unit,
                'groups' => $groups,
                'add_groups' => $addGroups
            );
        }, explode("\n", (string)$_POST['list'])), function ($val) {
            return !empty($val);
        });

        foreach ($list as $person): $person = (array)$person; $id = $person['login']; ?>
            <tr id="tr_<?=$id;?>">
                <td>
                    <button class="remove btn btn-danger"><?=$id;?></button>
                </td>
                <?php foreach ($head as $key => $field): ?>
                    <td>
                        <?php if (!empty($field['values']) && true === ($field['multiple']??false) && false): $person[$key] = explode(' ', $person[$key]); ?>
                            <?php foreach ($field['values'] as $val => $name): ?>
                                <label>
                                    <input type="checkbox" id="f_<?=$id;?>_<?=$key;?>_<?=$val;?>" name="json[<?=$id;?>][<?=$key;?>][]" value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'checked': '');?> >
                                    <?=$name;?>
                                </label>
                            <?php endforeach; ?>
                        <?php elseif (!empty($field['values'])): $person[$key] = explode(' ', $person[$key]);?>
                            <select class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]<?=(true === $field['multiple']? '[]': '');?>" <?=(true === $field['multiple']? 'multiple': '');?>>
                                <?php foreach ($field['values'] as $val => $name): ?>
                                    <option value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'selected': '');?>><?=$name;?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]" value="<?=$person[$key];?>" />
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?></tr>
        <?php endforeach;
        exit;
    }
} elseif (isset($_POST['login']) && $_POST['password']) {
    $arL = array(
        'root' => "123qweASD"
    );
    if ($arL[$_POST['login']] == $_POST['password']) {
        $login = true;
        $_SESSION['start'] = true;
    }
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/css/bootstrap-theme.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="assets/vendor/underscore.js"></script>
    <script type="text/javascript" src="assets/vendor/jquery.js"></script>
    <script type="text/javascript" src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
        $(function () {
            var head = <?=json_encode($head || array(), JSON_UNESCAPED_UNICODE);?>;
            var keys = Object.keys(head);
            var $head = $('#main').find('thead tr'),
                $body = $('#main').find('tbody');

            $body.on('click', '.remove', function () {
                $(this).closest('tr').remove();
            });
            $('#btn_convert_from_csv, #btn_convert_from_asiou, #btn_add_form').on('click', function (event) {
                event.preventDefault();
            });
            $('#apply_convert_from_csv').on('click', function (event) {
                $.post('index.php', {
                    'convert_from_csv': 1,
                    'file': $('#file_form_base').val(),
                    'list': $('#text_convert_from_csv').val()
                }, function (result) {
                    $body.append(result);
                    $('#text_convert_from_csv').val('');
                });
            });
            $('#apply_convert_from_asiou').on('click', function (event) {
                $.post('index.php', {
                    'convert_from_asiou': 1,
                    'file': $('#file_form_base').val(),
                    'unit': $('#unit_convert_from_asiou').val(),
                    'sub': $('#sub_convert_from_asiou').val(),
                    'list': $('#text_convert_from_asiou').val()
                }, function (result) {
                    $body.append(result);
                    $('#text_convert_from_asiou').val('');
                    $('#sub_convert_from_asiou').val('');
                });
            });
            $('#apply_new_file').on('click', function () {
                $.post('index.php', {
                    'new_file': 1,
                    'name': $('#name_new_file').val()
                }, function () {
                    window.location.reload();
                })
            });
        });
    </script>
</head>
<body>
<?php if (!$login): ?>
    <form method="post">
        <div class="form-group">
            <label for="exampleInputLogin">Login</label>
            <input type="text" name="login" class="form-control" id="exampleInputLogin" placeholder="Login">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword">Password</label>
            <input type="password" name="password" class="form-control" id="exampleInputPassword" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
<?php elseif (empty($_REQUEST['file'])): ?>
    <ul><?php
		$arList = array();
		foreach ((new DirectoryIterator('users')) as $file) {
			if ($file->isDot() || $file->isDir()) {
                continue;
            }
			$arList[$file->getBasename()] = $file->getBasename('.json');
		}
		ksort($arList);
		foreach ($arList as $file => $name):
            $query = '?file='.$file;
            if ($up = preg_match('/(?:users|class)_([0-9]{1,2}\w)/', $name, $matches)) {
                $matches[1] = str_replace(array('a', 'b', 'v', 'g', 'd'), array('а', 'б', 'в', 'г', 'д'), $matches[1]);
                $query .= '&unit=pupils&sub='.$matches[1];
            } ?>
            <li>
                <?php if ($up): ?>
                    <a class="glyphicon glyphicon-chevron-up" href="?file=<?=$file;?>&up=1"></a>
                <?php endif; ?>
                <a href="<?=$query;?>"><?=$file;?></a>
                <a href="?file=<?=$file;?>&remove=1" class="glyphicon glyphicon-remove"></a>
            </li>
        <?php endforeach; ?></ul>
    <div class="navbar navbar-default navbar-fixed-bottom">
        <div class="container clearfix navbar-nav">
            <div class="navbar-left container">
                <form method="POST" class="pull-right">
                    <input class="btn btn-success" type="submit" value="Update LDAP" name="update_ldap">
                    <input class="btn btn-primary" type="submit" value="Convert To csv" name="convert_to_csv">
                    <input class="btn btn-primary" type="submit" value="Convert To Print" name="convert_to_print">
                </form>
                <button class="btn btn-success" data-toggle="modal" data-target="#form_new_file" onclick="">New</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="form_new_file" tabindex="-1" role="dialog" aria-labelledby="title_new_file">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="title_new_file">New File</h4>
                </div>
                <div class="input-group">
                    <input class="form-control" type="text" value="users_" id="name_new_file" />
                    <div class="input-group-addon">.json</div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="apply_new_file" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <form method="POST" id="form_base">
        <input type="hidden" id="file_form_base" name="file" value="<?=(string)$_REQUEST['file'];?>" />
        <table class="table" id="main">
            <thead>
            <tr>
                <th>Удалить</th>
                <?php foreach ($head as $th): ?>
                    <th><?=$th['title'];?></th>
                <?php endforeach; ?></tr>
            </thead>
            <tbody><?php foreach (json_decode(file_get_contents('users/'.$_REQUEST['file'])) as $id => $person): $person = (array)$person; ?>
                <tr id="tr_<?=$id;?>">
                    <td>
                        <button class="remove btn btn-danger"><?=$id;?></button>
                    </td>
                    <?php foreach ($head as $key => $field): ?>
                        <td>
                            <?php if (!empty($field['values']) && true === ($field['multiple']??false) && false): $person[$key] = explode(' ', $person[$key]); ?>
                                <?php foreach ($field['values'] as $val => $name): ?>
                                    <label>
                                        <input type="checkbox" id="f_<?=$id;?>_<?=$key;?>_<?=$val;?>" name="json[<?=$id;?>][<?=$key;?>][]" value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'checked': '');?> >
                                        <?=$name;?>
                                    </label>
                                <?php endforeach; ?>
                            <?php elseif (!empty($field['values'])): $person[$key] = explode(' ', $person[$key]);?>
                                <select class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]<?=(true === $field['multiple']? '[]': '');?>" <?=(true === $field['multiple']? 'multiple': '');?>>
                                    <?php foreach ($field['values'] as $val => $name): ?>
                                        <option value="<?=$val;?>" <?=(in_array($val, $person[$key])? 'selected': '');?>><?=$name;?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input class="form-control" id="f_<?=$id;?>_<?=$key;?>" name="json[<?=$id;?>][<?=$key;?>]" value="<?=$person[$key];?>" />
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?></tr>
            <?php endforeach; ?></tbody>
        </table>
        <div class="navbar navbar-default navbar-fixed-bottom">
            <div class="container clearfix navbar-nav">
                <div class="navbar-left">
                    <input class="btn btn-success" type="submit" value="submit" name="save" />
                    <input class="btn btn-primary" type="submit" value="Convert To csv" name="convert_to_csv">
                    <input class="btn btn-primary" type="submit" value="Convert To Print" name="convert_to_print">
                    <a class="btn btn-link" href=".">Back</a>
                </div>
                <div class="navbar-right">
                    <button class="btn btn-info" id="btn_add_form" data-toggle="modal" data-target="#form_add">Add</button>
                    <button class="btn btn-default" id="btn_convert_from_csv" data-toggle="modal" data-target="#form_convert_from_csv">Convert From csv</button>
                    <button class="btn btn-default" id="btn_convert_from_asiou" data-toggle="modal" data-target="#form_convert_from_asiou">Convert From Asiou</button>
                </div>
            </div>
        </div>
    </form>
    <?php
    $_groups = array();
    $_addGroups = array();
    if (preg_match('/(users|class)_([0-9]{1,2})(\w)\.json/', $_REQUEST['file'], $matches)) {
        $_groups[] = 'pupils';
        $_addGroups[] = 'class_'.$matches[2].$matches[3];
    }
    ?>
    <div class="modal fade" id="form_add" tabindex="-1" role="dialog" aria-labelledby="title_form_add">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-horizontal" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="title_form_add">Add From</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="form_add_base" name="file" value="<?=(string)$_REQUEST['file'];?>" />
                    <div class="form-group">
                        <label for="form_add_login" class="col-sm-4 control-label">Login</label>
                        <div class="col-sm-8">
                            <input name="login" type="text" class="form-control" id="form_add_login" placeholder="Login" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_password" class="col-sm-4 control-label">Пароль</label>
                        <div class="col-sm-8">
                            <input name="password" type="text" class="form-control" id="form_add_password" placeholder="Пароль" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_name" class="col-sm-4 control-label">Имя</label>
                        <div class="col-sm-8">
                            <input name="name" type="text" class="form-control" id="form_add_name" placeholder="Имя" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_surname" class="col-sm-4 control-label">Фамилия</label>
                        <div class="col-sm-8">
                            <input name="surname" type="text" class="form-control" id="form_add_surname" placeholder="Фамилия" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_alias" class="col-sm-4 control-label">Alias</label>
                        <div class="col-sm-8">
                            <input name="alias" type="text" class="form-control" id="form_add_alias" placeholder="Alias" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_unit" class="col-sm-4 control-label">Подразделение</label>
                        <div class="col-sm-8">
                            <select name="unit" class="form-control" id="form_add_unit">
                                <?php foreach ($head['unit']['values'] as $val => $name): ?>
                                    <option value="<?=$val;?>" <?=((string)$_REQUEST['unit'] === $val?'selected': '');?>><?=$name;?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_sub" class="col-sm-4 control-label">Папка</label>
                        <div class="col-sm-8">
                            <input name="sub" type="text" class="form-control" id="form_add_sub" value="<?=((string)$_REQUEST['sub']?:'.');?>" placeholder="Папка" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_groups" class="col-sm-4 control-label">Группы</label>
                        <div class="col-sm-8">
                            <select name="groups[]" multiple class="form-control" id="form_add_groups">
                                <?php foreach ($head['groups']['values'] as $val => $name): ?>
                                    <option value="<?=$val;?>" <?=(in_array($val, $_groups)? 'selected': '');?>><?=$name;?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form_add_add_groups" class="col-sm-4 control-label">Доп. группы</label>
                        <div class="col-sm-8">
                            <input name="add_groups" type="text" class="form-control" id="form_add_add_groups" placeholder="Доп. группы" value="<?=implode(' ', $_addGroups);?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" name="add" value="Apply" />
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="form_convert_from_csv" tabindex="-1" role="dialog" aria-labelledby="title_convert_from_csv">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="title_convert_from_csv">Convert From csv</h4>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" id="text_convert_from_csv" style="height: 200px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="apply_convert_from_csv" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="form_convert_from_asiou" tabindex="-1" role="dialog" aria-labelledby="title_convert_from_asiou">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="title_convert_from_asiou">Convert From asiou</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-xs-6">
                            <select class="form-control" id="unit_convert_from_asiou"><?php foreach ($head['unit']['values'] as $val => $name): ?>
                                    <option value="<?=$val;?>" <?=((string)($_REQUEST['unit']??'') === $val?'selected': '');?>><?=$name;?></option>
                                <?php endforeach; ?></select>
                        </div>
                        <div class="col-xs-6">
                            <input class="form-control" type="text" value="<?=((string)($_REQUEST['sub']??'.'));?>" id="sub_convert_from_asiou" />
                        </div>
                    </div>
                    <hr />
                    <hr />
                    <hr />
                    <textarea class="form-control" id="text_convert_from_asiou" style="height: 200px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="apply_convert_from_asiou" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</body>
</html>

