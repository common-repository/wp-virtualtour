import { nodeResolve } from '@rollup/plugin-node-resolve'
import replace from '@rollup/plugin-replace'
import vuePlugin from 'rollup-plugin-vue'
import scss from 'rollup-plugin-scss'
import postcss from 'postcss'
import autoprefixer from 'autoprefixer'
import { terser } from 'rollup-plugin-terser'
import commonjs from '@rollup/plugin-commonjs'

const plugins = [
	scss({
		output: 'dist/style.css',
		processor: css => postcss([autoprefixer()])
	}),
	vuePlugin(),
	replace({
		'process.env.NODE_ENV': JSON.stringify('production')
	}),
	nodeResolve({preferBuiltins: true}),
	commonjs({
		include: 'node_modules/**',
		namedExports: {
			'node_modules/@emotion/memoize/dist/memoize.cjs.js': ['memoize']
		}
	})
]
const isWatching = process.argv.includes('-w') || process.argv.includes('--watch')

let output
if (isWatching) {
	output = {
		dir: 'dist',
		format: 'iife'
	}
} else {
	output = {
		dir: 'dist',
		format: 'iife',
		name: 'version',
		plugins: [terser()]
	}
}
export default [
	{
		input: 'src/main.js',
		output: output,
		plugins: plugins
	}
]
