import { Table } from '@mantine/core'
import { useEffect, useMemo, useState } from 'react'
import { requestUserList } from '../api/api-user'
import { useRouterApp } from '../context/router-context'
import { useQuery, useQueryLoading } from '../hooks/use-query'
import { UserItem } from './user-item'

export function UserList({ file }: { file?: string }) {
	if (!file) {
		return ''
	}
	const [_list, setList] = useState({})
	const ctx = useRouterApp()
	const reqList = useQuery(requestUserList)
	const isLoading = useQueryLoading(reqList)

	async function fetch() {
		const res = await reqList.request(file)
		setList(res)
	}
	const list = useMemo(
		() =>
			Object.keys(_list).map(uid => ({
				..._list[uid],
				uid,
			})),
		[_list]
	)
	console.log(list)
	useEffect(() => {
		fetch()
	}, [file])
	return (
		<Table>
			<Table.Thead>
				<Table.Tr>
					
				</Table.Tr>
			</Table.Thead>
			<Table.Tbody>
				{list.map((item: any) => (
					<UserItem key={item.uid} user={item} />
				))}
			</Table.Tbody>
		</Table.Thead>
	)
}
