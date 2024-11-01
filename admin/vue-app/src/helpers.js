import { store } from './store.js'

const {__} = wp.i18n

export const doXhr = (data) => {
	const request = new XMLHttpRequest()
	store.isSaving = true
	return new Promise(resolve => {
		request.open('POST', ajaxurl, true)
		request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;')
		request.onload = function() {
			if (this.status >= 200 && this.status < 400) {
				store.isSaving = false
				const response = JSON.parse(this.response)
				console.log('desc: ', response)
				if (response.error) {
					alert(response.error)
				} else {
					return resolve(response)
				}
			} else {
				store.isSaving = false
				alert(__('Something went wrong with the Ajax call.', 'wp_virtualtour'))
			}
		}
		request.onerror = function() {
			alert(__('Something went wrong with the Ajax call.', 'wp_virtualtour'))
		}
		data.action = 'general_ajax_action'
		if (data.data)
			data.data = JSON.stringify(data.data)
		request.send(toUri((data)))
	})
}

export const generateUuid = () => {
	return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
		(c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
	)
}

export function arrayMove(arr, old_index, new_index, isObject = false) {
	if (isObject) {
		arr = Object.entries(arr)
	}
	if (new_index >= arr.length) {
		let k = new_index - arr.length + 1
		while (k--) {
			arr.push(undefined)
		}
	}
	arr.splice(new_index, 0, arr.splice(old_index, 1)[0])
	return isObject ? Object.fromEntries(arr) : arr
}

function toUri(data) {
	return Object.keys(data).map(function(k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&')
}
