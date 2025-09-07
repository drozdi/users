import { ActionIcon, Box, Group, Stack } from '@mantine/core'
import { useEffect, useMemo, useState } from 'react'
import { TbX } from 'react-icons/tb'
import {
	requestListUserGroup,
	requestRemoveUserGroup,
} from '../api/api-user-group'
import { useQuery } from '../hooks/use-query'
import classes from './style.module.css'

export function UserGroup() {
	const { isLoading, error, request } = useQuery(requestListUserGroup)
	const [_list, setList] = useState([])
	async function fetch() {
		const res = await request()
		setList(res)
	}
	useEffect(() => {
		fetch()
	}, [])
	const list = useMemo(
		() =>
			_list.map(item => ({
				...item,
				up: /(?:users|class)_([0-9]{1,2}\w)/.test(item.lable),
				label: item.label
					.replace('a', 'а')
					.replace('b', 'б')
					.replace('v', 'в')
					.replace('g', 'г'),
			})),
		[_list]
	)
	console.log(list)
	async function handleRemove(id) {
		try {
			await requestRemoveUserGroup(id)
			await fetch()
		} catch (error) {
			console.log(error)
		}
	}
	return (
		<Stack gap={0}>
			{list.map(item => (
				<Group className={classes.item} key={item.path} justify='space-between'>
					<Group>
						<Box>{item.label}</Box>
						<Box></Box>
					</Group>
					<ActionIcon
						color='red'
						title='Удалить'
						onClick={() => handleRemove(item.path)}
					>
						<TbX />
					</ActionIcon>
				</Group>
			))}
		</Stack>
	)
}
