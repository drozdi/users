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
	const [_list, setList] = useState({})
	const [groups, setGroups] = useState({})
	const [ous, setOus] = useState({})
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
	console.log(ous)
	const headers = [
		{
			field: 'name',
			label: 'Имя',
			width: 100,
		},
		{
			field: 'surname',
			label: 'Фамилия',
			width: 75,
		},
		{
			field: 'alias',
			label: 'Alias',
			width: 100,
		},
		{
			field: 'password',
			label: 'Пароль',
			width: 75,
		},
		{
			field: 'unit',
			label: 'Подразделение',
			width: 75,
			values: Object.keys(ous).map(value => ({
				value,
				label: ous[value],
			})),
		},
		{
			field: 'sub',
			label: 'Папка',
			width: 50,
		},
		{
			field: 'groups',
			label: 'Группы',
			multiple: true,
			width: 100,
			values: Object.keys(groups).map(value => ({
				value,
				label: groups[value],
			})),
		},
		{
			width: 100,
			field: 'add_groups',
			label: 'Доп. группы',
		},
	]

	console.log(headers)
	useEffect(() => {
		fetch()
	}, [file])
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
					<UserItem key={item.uid} user={item} headers={headers} />
				))}
			</Table.Tbody>
		</Table>
	)
}
