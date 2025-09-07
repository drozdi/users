import { Box, Container, Stack } from '@mantine/core'
import { useMemo, useState } from 'react'
import { UserGroup } from './components/user-group'
import { ProviderRouterApp } from './context/router-context'

function App() {
	const [userListFlag, setUserListFlag] = useState<boolean>(false)
	const ctx = useMemo(
		() => ({
			userListFlag,
			setUserListFlag,
		}),
		[userListFlag]
	)

	return (
		<ProviderRouterApp value={ctx}>
			<Stack justify='space-between' w='100vw' h='100vh'>
				<Box>
					<Container p='sm' size={1024}>
						<UserGroup />
					</Container>
				</Box>
				<Box bg='cyan' mih={60}></Box>
			</Stack>
		</ProviderRouterApp>
	)
}

export default App
