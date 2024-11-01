import { reactive } from 'vue'

export const store = reactive({
	tours: [],
	activeTour: undefined,
	activeScene: undefined,
	activeHotspot: undefined,
	pannellum: undefined,
	pannellumIsCtrlClick: false,
	currentYawPitch: [0, 0],
	isSaving: false
})

export const storeAction = {
	setTours(tours) {
		store.tours = tours
	},
	setActiveTour(tour) {
		store.activeTour = tour
	},
	setActiveScene(scene) {
		store.activeScene = scene
	},
	setActiveHotspot(hotspot) {
		store.activeHotspot = hotspot
	},
	setPannellum(pannellum) {
		store.pannellum = pannellum
	},
	setPannellumIsCtrlClick(clickEvent) {
		store.pannellumIsCtrlClick = clickEvent
	},
	setCurrentYawPitch(yaw, pitch) {
		store.currentYawPitch = [yaw, pitch]
	}
}
