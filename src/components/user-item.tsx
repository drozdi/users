import { Button, MultiSelect, Select, Table, TextInput } from '@mantine/core'
export function UserItem({ user, headers }: { user: any; headers?: any[] }) {
	return (
		<Table.Tr>
			<Table.Td>
				<Button color='red'>{user.uid}</Button>
			</Table.Td>
			{headers.map(({ field, values = [], multiple = false }) => {
				return (
					<Table.Td key={field}>
						{values.length ? (
							multiple ? (
								<MultiSelect
									data={values}
									defaultValue={user[field].split(/\s+/)}
								/>
							) : (
								<Select
									data={values}
									defaultValue={
										multiple ? user[field].split(/\s+/) : user[field]
									}
								/>
							)
						) : (
							<TextInput variant='default' defaultValue={user[field]} />
						)}
					</Table.Td>
				)
			})}
		</Table.Tr>
	)
}
