import { useEffect, useState } from 'react'
import { requestUserListGroup } from '../api/api-user-group'
import { useQuery } from '../hooks/use-query'

export function UserGroup() {
	const { isLoading, error, request } = useQuery(requestUserListGroup)
	const [list, setList] = useState([])
	async function fetch() {
		const res = await request()
		console.log(res)
		setList(res)
	}
	useEffect(() => {
		fetch()
	}, [])
	return <></>
}
