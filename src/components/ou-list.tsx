import { Button, Group, Stack } from '@mantine/core'
import { useEffect, useState } from 'react'
import { requestOuList } from '../api/api-ou'
import { useQuery } from '../hooks/use-query'
import { OuItem } from './ou-item'

export function OuList() {
	const [ous, setOus] = useState<Record<string, string>>({})
	const [newOus, setNewOus] = useState<Record<string, string>>({})
	const reqOuList = useQuery(requestOuList)

	async function fetch() {
		const res = await reqOuList.request()
		setOus(res)
	}

	async function addOu() {
		const newOu = 'newOu'
		setNewOus(v => ({ ...v, [newOu]: '' }))
	}

	useEffect(() => {
		fetch()
	}, [])
	return (
		<Stack justify='space-between' h='100%'>
			<Stack>
				{Object.entries(ous).map(([ou, name]) => (
					<OuItem key={ou} item={{ ou, name }} />
				))}
				{Object.entries(newOus).map(([ou, name]) => (
					<OuItem key={ou} item={{ ou, name }} edit />
				))}
			</Stack>
			<Group justify='space-between'>
				<Button color='green'>Сохранить</Button>
				<Button onClick={addOu}>Добавить</Button>
			</Group>
		</Stack>
	)
}
