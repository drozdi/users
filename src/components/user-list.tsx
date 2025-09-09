import { Table } from '@mantine/core'
import { useEffect, useMemo, useState } from 'react'
import { requestGroupList } from '../api/api-group'
import { requestOuList } from '../api/api-ou'
import { requestUserList } from '../api/api-user'
import { useRouterApp } from '../context/router-context'
import { useQuery, useQueryLoading } from '../hooks/use-query'
import { UserItem } from './user-item'

export function UserList({ file }: { file?: string }) {
	if (!file) {
		return ''
	}
	const ctx = useRouterApp()
	const [_list, setList] = useState<Record<string, any>>({})
	const [groups, setGroups] = useState<Record<string, string>>({})
	const [ous, setOus] = useState<Record<string, string>>({})
	const list = useMemo(
		() =>
			Object.keys(_list).map(uid => ({
				..._list[uid],
				uid,
			})),
		[_list]
	)
	const reqList = useQuery(requestUserList)
	const reqGroup = useQuery(requestGroupList)
	const reqOu = useQuery(requestOuList)
	const isLoading = useQueryLoading(reqList, reqGroup, reqOu)

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
		const n = { ..._list, [user.uid]: user }
		console.log(n)
		setList(n)
	}
	return (
		<Table striped withRowBorders stickyHeader highlightOnHover>
			<Table.Thead>
				<Table.Tr>
					<Table.Th w='2rem'>uid</Table.Th>
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
						key={item.uid}
						user={item}
						headers={headers}
						onChange={handleChenge}
					/>
				))}
			</Table.Tbody>
		</Table>
	)
}
