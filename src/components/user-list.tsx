import { Button, Group, Modal, Stack, Table, Textarea } from '@mantine/core'
import { useDisclosure } from '@mantine/hooks'
import { useEffect, useMemo, useRef, useState } from 'react'
import { requestGroupList } from '../api/api-group'
import { requestOuList } from '../api/api-ou'
import { requestUsersList, requestUsersSave } from '../api/api-user'
import { useRouterApp } from '../context/router-context'
import { Template } from '../context/template-context'
import { useQuery, useQueryLoading } from '../hooks/use-query'
import { UserItem } from './user-item'

export function UserList({ file }: { file?: string }) {
	if (!file) {
		return ''
	}
	const ctx = useRouterApp()
	const refAsiou = useRef<HTMLTextAreaElement>(null)
	const [_list, setList] = useState<Record<string, any>>({})
	const [groups, setGroups] = useState<Record<string, string>>({})
	const [ous, setOus] = useState<Record<string, string>>({})
	const [openedAsiou, handlerAsiou] = useDisclosure(false)
	const list = useMemo(
		() =>
			Object.keys(_list).map(login => ({
				..._list[login],
				login,
			})),
		[_list]
	)
	const reqList = useQuery(requestUsersList)
	const reqGroup = useQuery(requestGroupList)
	const reqOu = useQuery(requestOuList)
	const reqSave = useQuery(requestUsersSave)
	const isLoading = useQueryLoading(reqList, reqGroup, reqOu, reqSave)

	async function fetch() {
		const res = await reqList.request(file)
		const groups = await reqGroup.request()
		const ous = await reqOu.request()
		setList(res)
		setGroups(groups)
		setOus(ous)
	}
	const headers = useMemo(
		() => [
			{
				field: 'name',
				label: 'Имя',
				width: '6rem',
			},
			{
				field: 'surname',
				label: 'Фамилия',
				width: '6rem',
			},
			{
				field: 'alias',
				label: 'Alias',
				width: '8rem',
			},
			{
				field: 'password',
				label: 'Пароль',
				width: '6rem',
			},
			{
				field: 'unit',
				label: 'Подразделение',
				width: '4rem',
				values: Object.keys(ous).map(value => ({
					value,
					label: ous[value],
				})),
			},
			{
				field: 'sub',
				label: 'Папка',
				width: '2rem',
			},
			{
				field: 'groups',
				label: 'Группы',
				multiple: true,
				values: Object.keys(groups).map(value => ({
					value,
					label: groups[value],
				})),
			},
			{
				width: '6rem',
				field: 'add_groups',
				label: 'Доп. группы',
			},
		],
		[groups, ous]
	)

	useEffect(() => {
		fetch()
	}, [file])

	function handleChenge(user: Record<string, string>) {
		const n = { ..._list, [user.login]: user }
		setList(n)
	}
	function parseAsiou() {
		const lines = (refAsiou.current?.value || '').trim().split("\n").map(line => line.trim()).filter(line => line.length > 10);

		let unit = ''
		let groups = ''
		let add_groups = ''
		let sub = '.'

		if (/.*_.*/.test(file)) {	
			const _res = file.match(/(?:users|class)_(?<sub>[0-9]{1,2}\w)(?:.json)?/)
			unit = 'pupils'
			groups = 'pupils';
			sub = _res.groups.sub.replace('a', 'а')
					.replace('b', 'б')
					.replace('v', 'в')
					.replace('g', 'г')
					.replace('d', 'д')
			add_groups = 'class_'+_res.groups.sub
		} else {
			unit = 'tsi'
			groups = 'teachers';
		}

		console.log(file, unit, groups, add_groups, sub)


		const adds = {}
		for (const line of lines) {
			const res = line.match(/(?<alias>[^,]*),\s*логин:\s*(?<login>[\w]{1,})\s*пароль:\s*(?<password>[\w]{1,})\s*/).groups
			const arr = res.alias.split(/\s+/)
			adds[res.login] = {
                alias: arr.join(' '),
                surname: arr.shift(),
                name: arr.join(' '),
                login: res.login,
                password: unit === 'pupils'? res.login: res.password,
                sub,
                unit,
                groups,
                add_groups
			}
		}

		console.log({..._list, ...adds})
		
	}
	//
	return (
		<>
			<Table striped withRowBorders stickyHeader highlightOnHover>
				<Table.Thead>
					<Table.Tr>
						<Table.Th w='2rem'>login</Table.Th>
						{headers.map(({ field, label, width }) => (
							<Table.Th w={width} key={field}>
								{label}
							</Table.Th>
						))}
					</Table.Tr>
				</Table.Thead>
				<Table.Tbody>
					{list.map((item: any) => (
						<UserItem
							key={item.login}
							user={item}
							headers={headers}
							onChange={handleChenge}
						/>
					))}
				</Table.Tbody>
			</Table>
			<Template slot='footer'>
				<>
					<Group>
						<Button
							color='green'
							onClick={() => {
								reqSave.request(file, _list)
							}}
						>
							{isLoading ? 'Сохраняю...' : 'Сохранить'}
						</Button>
						<Button color='dark' onClick={() => ctx.setUserList('')}>
							Назад
						</Button>
					</Group>
					<Group>
						<Button onClick={() => handlerAsiou.open()}>
							Вставить из асиоу
						</Button>
					</Group>
				</>
			</Template>
			<Modal
				opened={openedAsiou}
				size="lg"
				onClose={() => {
					handlerAsiou.close()
				}}
			>
				<Stack>
					<Textarea rows={20} ref={refAsiou}></Textarea>
					<Group>
						<Button onClick={() => parseAsiou()}>Добавить</Button>
					</Group>
				</Stack>
			</Modal>
		</>
	)
}
