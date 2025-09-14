import { Box, Button, Center, PasswordInput, TextInput, Tooltip } from '@mantine/core'
import { useForm } from '@mantine/form'
import { useState } from 'react'
import { requestLogin } from '../api/api-login'
import { useRouterApp } from '../context/router-context'

export function Login() {
	const ctx = useRouterApp()
	const [opened, setOpened] = useState(false)
	const form = useForm({
		mode: 'uncontrolled',
		initialValues: {
			login: '',
			password: '',
		},
	})

	const valid = form.values.password.length >= 6

	return (
		<Center w='100vw' h='100vh'>
			<Box
				component='form'
				w='300px'
				onSubmit={form.onSubmit(async data => {
					const res = await requestLogin(data)
					if (res.status === 'ok') {
						ctx.setIsAuth(true)
					}
				})}
			>
				<TextInput placeholder='Your login' key={form.key('login')} {...form.getInputProps('login')} />
				<Tooltip
					label={valid ? 'All good!' : 'Password must include at least 6 characters'}
					position='bottom-start'
					withArrow
					opened={opened}
					color={valid ? 'teal' : undefined}
					withinPortal
				>
					<PasswordInput
						label='Tooltip shown onFocus'
						required
						placeholder='Your password'
						onFocus={() => setOpened(true)}
						onBlur={() => setOpened(false)}
						mt='md'
						key={form.key('password')}
						{...form.getInputProps('password')}
					/>
				</Tooltip>
				<Button type='submit' mt='lg'>
					Submit
				</Button>
			</Box>
		</Center>
	)
}
