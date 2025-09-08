import { ActionIcon, Box, Group, Stack, Tooltip } from '@mantine/core'
import { useEffect, useMemo, useState } from 'react'
import { TbArrowNarrowUp, TbX } from 'react-icons/tb'
import {
	requestListUserGroup,
	requestRemoveUserGroup,
	requestUpUserGroup,
} from '../api/api-user-group'
import { useQuery } from '../hooks/use-query'
import classes from './style.module.css'

export function UserGroup() {
	const { isLoading, error, request } = useQuery(requestListUserGroup)
	const [_list, setList] = useState<
		Array<{
			label: string
			path: string
		}>
	>([])
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
				up: /(?:users|class)_([0-9]{1,2}\w)/.test(item.label),
				label: item.label
					.replace('a', 'а')
					.replace('b', 'б')
					.replace('v', 'в')
					.replace('g', 'г'),
			})),
		[_list]
	)
	async function handleRemove(path: string) {
		try {
			await requestRemoveUserGroup(path)
			//await fetch()
			setList(_list.filter(item => item.path !== path))
		} catch (error) {
			console.log(error)
		}
	}
	async function handleUp(path: string) {
		try {
			const res = await requestUpUserGroup(path)
			setList(
				list.map(item =>
					item.path === path ? { ...res, status: undefined } : item
				)
			)
		} catch (error) {
			console.log(error)
		}
	}
	return (
		<Stack gap={0}>
			{list.map(item => (
				<Group className={classes.item} key={item.path} justify='space-between'>
					<Group>
						<Box w='1rem'>
							{item.up && (
								<Tooltip label='Перевести в следующий класс'>
									<ActionIcon color='green' onClick={() => handleUp(item.path)}>
										<TbArrowNarrowUp />
									</ActionIcon>
								</Tooltip>
							)}
						</Box>
						<Box justify='center' align='center'>
							{item.label}
						</Box>
					</Group>
					<Tooltip label='Удалить'>
						<ActionIcon color='red' onClick={() => handleRemove(item.path)}>
							<TbX />
						</ActionIcon>
					</Tooltip>
				</Group>
			))}
		</Stack>
	)
}
