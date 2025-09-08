import {
	ActionIcon,
	Button,
	createTheme,
	Group,
	Input,
	MantineProvider,
	NumberInput,
	Stack,
	Textarea,
	TextInput,
} from '@mantine/core'
import '@mantine/core/styles.css'
import { createRoot } from 'react-dom/client'
import App from './App.tsx'
import { TemplateProvider } from './context/template-context'

const theme = createTheme({
	components: {
		ActionIcon: ActionIcon.extend({
			defaultProps: {
				size: 'sm',
			},
		}),
		Group: Group.extend({
			defaultProps: {
				mih: 30,
				gap: 'xs',
			},
		}),
		Stack: Stack.extend({
			defaultProps: {
				gap: 'xs',
			},
		}),
		Button: Button.extend({
			defaultProps: {
				size: 'xs',
				variant: 'filled',
			},
		}),
		Input: Input.extend({
			defaultProps: {
				variant: 'filled',
				size: 'xs',
				radius: 0,
				rightSectionWidth: 'auto',
			},
		}),
		Textarea: Textarea.extend({
			defaultProps: {
				variant: 'filled',
				size: 'xs',
				rows: 3,
				radius: 0,
				rightSectionWidth: 'auto',
				rightSectionProps: {
					style: {
						alignItems: 'flex-start',
					},
				},
			},
		}),
		TextInput: TextInput.extend({
			defaultProps: {
				variant: 'filled',
				size: 'xs',
				radius: 0,
				rightSectionWidth: 'auto',
			},
		}),
		NumberInput: NumberInput.extend({
			defaultProps: {
				variant: 'filled',
				size: 'xs',
				radius: 0,
				rightSectionWidth: 'auto',
			},
		}),
	},
})

createRoot(document.getElementById('root')!).render(
	<MantineProvider theme={theme}>
		<TemplateProvider>
			<App />
		</TemplateProvider>
	</MantineProvider>
)
