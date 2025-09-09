import { Button, MultiSelect, Select, Table, TextInput } from '@mantine/core'
export function UserItem({
	user,
	headers,
	onChange,
	onRemove,
}: {
	user: Record<string, string>
	headers: {
		label: string
		field: string
		values?:
			| {
					label: string
					value: string
			  }[]
			| string[]
		multiple?: boolean
	}[]
	onChange?: (user: any) => void
	onRemove?: (user: any) => void
}) {
	function nandleChangeText({ target }: React.ChangeEvent<HTMLInputElement>) {
		onChange?.({ ...user, [target.name]: target.value })
	}
	function nandleChangeSelect(field: string, value: null | string | string[]) {
		onChange?.({
			...user,
			[field]: [].concat((value as never) || '').join(' '),
		})
	}
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
									name={field}
									defaultValue={user[field].split(/\s+/)}
									onChange={v => nandleChangeSelect(field, v)}
								/>
							) : (
								<Select
									data={values}
									name={field}
									defaultValue={user[field]}
									onChange={v => nandleChangeSelect(field, v)}
								/>
							)
						) : (
							<TextInput
								variant='default'
								name={field}
								defaultValue={user[field]}
								onChange={nandleChangeText}
							/>
						)}
					</Table.Td>
				)
			})}
		</Table.Tr>
	)
}
