import { Drawer } from '@mantine/core'
import { OuList } from './ou-list'

export function DrawerOu() {
	return (
		<Drawer opened={true} onClose={() => {}} title='Подразделения' position='right'>
			<OuList />
		</Drawer>
	)
}
