import { Group, TextInput } from '@mantine/core'
export function OuItem({ item, edit }: { item: any; edit?: boolean }) {
	return (
		<Group justify='space-between'>
			{edit ? (
				<>
					<TextInput defaultValue={item.ou} />
					<TextInput defaultValue={item.name} />
				</>
			) : (
				<>
					<div>{item.ou}</div>
					<div>{item.name}</div>
				</>
			)}
		</Group>
	)
}
