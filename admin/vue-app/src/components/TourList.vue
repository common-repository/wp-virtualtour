<template>
  <div>
    <table class="widefat" v-if="tours?.length">
      <thead>
      <tr>
        <th class="row-title">
          {{ __('ID', 'wp_virtualtour') }}
        </th>
        <th class="row-title">
          {{ __('Name', 'wp_virtualtour') }}
        </th>
        <th class="row-title">
          {{ __('Shortcode', 'wp_virtualtour') }}
        </th>
        <th class="row-title">
          {{ __('Actions', 'wp_virtualtour') }}
        </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="tour in tours" :key="tour.id">
        <td>
          <div v-html="tour.id"></div>
        </td>
        <td class="row-title">
          <div v-html="tour.title"></div>
        </td>
        <td class="row-title">
          <input type="text" :value="getShortcode(tour)" @focus="selectAll" readonly/>
          <i :class="showCopied ? 'visible' : 'invisible'"
             class="ms-2 text-muted fw-normal">{{ __('Copied!', 'wp_virtualtour') }}</i>
        </td>
        <td class="row-title">
          <input class="button-primary me-2"
                 type="submit"
                 name="Example"
                 @click="editTour(tour.id)"
                 :value="__('Edit', 'wp_virtualtour')"/>
          <input class="button"
                 type="submit"
                 name="Example"
                 @click="deleteTour(tour.id)"
                 :value="__('Delete', 'wp_virtualtour')"/>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

</template>

<script>
import { doXhr } from '../helpers.js'
import { store, storeAction } from '../store.js'

export default {
  name: 'TourList',
  inject: ['__'],
  data() {
    return {
      showCopied: false
    }
  },
  props: ['tours'],
  methods: {
    deleteTour(tourId) {
      const conf = confirm(this.__('Do you really want to delete this tour?', 'wp_virtualtour'))
      if (conf === true) {
        doXhr({
          do: 'deleteTour',
          data: {id: tourId}
        }).then((res) => {
          storeAction.setTours(res)
        })
      }
    },
    editTour(tourId) {
      this.$emit('editTour', tourId)
    },
    getShortcode(tour) {
      return '[wp_virtualtour id="' + tour.id + '"]'
    },
    selectAll(e) {
      e.target.select()
      if (document.execCommand('copy')) {
        this.showCopied = true
        setTimeout(() => {
          this.showCopied = false
        }, 2000)
      }
    }
  }
}

</script>
