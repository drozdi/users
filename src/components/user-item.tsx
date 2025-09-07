export function UserItem({ user }: { user: any }) {
	return (
		<tr id={`tr_${user.id}`}>
			<td>
				<button className='remove btn btn-danger'>${user.id}</button>
			</td>
			{/* <?php foreach ($head as $key => $field): ?>
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
                    <?php endforeach; ?> */}
		</tr>
	)
}
