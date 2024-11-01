import { registerBlockType } from '@wordpress/blocks'
import { __ } from '@wordpress/i18n'
import {
	useBlockProps,
	InspectorControls,
	__experimentalBlock as Block, BlockControls
} from '@wordpress/block-editor'
import { PanelBody, RangeControl, SelectControl, withFocusOutside, withFocusReturn } from '@wordpress/components'
import { Component } from '@wordpress/element'

registerBlockType('wp-virtualtour/wp-virtualtour-block', {
	apiVersion: 2,
	title: __('WP Virtual Tour', 'wp_virtualtour'),
	description: __('Block to show a virtual tour.', 'wp_virtualtour'),
	keywords: [
		__('tour', 'wp_virtualtour'),
		__('virtual tour', 'wp_virtualtour'),
		__('3d', 'wp_virtualtour'),
		__('360', 'wp_virtualtour')
	],
	icon: 'images-alt',
	category: 'media',
	supports: {
		alignWide: true,
		align: ['full']
	},
	attributes: {
		id: {
			type: 'string',
			default: ''
		},
		focused: {
			type: 'boolean',
			default: false
		},
		height: {
			type: 'integer',
			default: 400
		},
		pannellumInstance: {
			type: 'object',
			default: {}
		}
	},
	// edit: (props) => {
	edit:
		withFocusOutside(
			class extends Component {

				constructor() {
					super(...arguments)

					// example how to bind `this` to the current component for our callbacks
					// this.onChangeContent = this.onChangeContent.bind(this)

					// some place for your state
					this.state = {
						alignment: this.props.attributes.alignment,
						id: this.props.attributes.id,
						height: this.props.attributes.height,
						pannellumInstance: this.props.attributes.pannellumInstance,
						focused: this.props.focused
					}

				}

				handleFocusOutside() {
					this.props.setAttributes({
						focused: false
					})
				}

				render() {
					if (this.props.isSelected) {
						this.props.setAttributes({
							focused: true
						})
					}

					const {
						attributes: {
							alignment,
							height,
							id,
							pannellumInstance,
							focused
						}
					} = this.props

					const onChangeHeight = (newHeight) => {
						this.props.setAttributes({
							height: newHeight
						})
						this.props.attributes.pannellumInstance.resize()
					}

					const onChangeTour = (tourId) => {
						this.props.setAttributes({
							id: tourId
						})
						initPannellum(tourId)
					}

					const initPannellum = (tourId) => {
						if (Object.keys(this.props.attributes.pannellumInstance).length) {
							this.props.attributes.pannellumInstance.destroy()
						}
						setTimeout(() => { // wait for the block to have the id assigned
							this.props.attributes.pannellumInstance = window.pannellum.viewer('panorama-' + tourId, wp_virtualtour.tours.filter(tour => tour.id === tourId)[0])
						}, 100)
					}

					if (this.props.attributes.id) {
						initPannellum(this.props.attributes.id)
					}

					if (!this.props.attributes.id) {
						onChangeTour(wp_virtualtour.tours[0].id)
					}

					const getTourOptions = () => {
						const tourOptions = [{value: null, label: __('Select tour', 'wp_virtualtour'), disabled: true}]
						wp_virtualtour.tours.forEach((tour, i) => {
							tourOptions.push({
								value: tour.id,
								label: tour.default.title
							})
						})
						return tourOptions
					}

					const onChangeAlignment = (updatedAlignment) => {
						this.props.setAttributes({alignment: updatedAlignment})
					}

					return (
						<>
							{/*<BlockControls>*/}
							{/*</BlockControls>*/}
							<InspectorControls>
								<PanelBody title={__('Settings', 'wp_virtualtour')}>
									<RangeControl
										label="Height"
										value={height}
										onChange={onChangeHeight}
										min={0}
										max={1000}
									/>
									<SelectControl
										label={__('Select tour', 'wp_virtualtour')}
										value={id}
										onChange={onChangeTour}
										options={getTourOptions()}
									/>
								</PanelBody>
							</InspectorControls>
							<Block.div>
								<div className={`panorama-wrapper ${focused ? 'focused' : ''}`}>
									<div style={{
										height: height + 'px'
									}} id={'panorama-' + id}></div>
								</div>
							</Block.div>
						</>
					)
				}

			}
		),
	save: (props) => {
		const blockProps = useBlockProps.save()
		const id = props.attributes.id
		const height = props.attributes.height
		return (
			<div {...blockProps}>
				<div
					style={{
						height: height + 'px'
					}}
					id={'panorama_' + id}
					class="wp-virtualtour-element"
					data-id={id}
				></div>
			</div>
		)
	}
})
