<template>
  <div id="post-body" class="metabox-holder columns-2">
    <div v-if="loaded">
      <div v-if="isSaving" class="saving-spinner">
        <div class="spinner is-active" style="float:none; width:auto; height:auto; padding:10px 0 10px 50px; background-position:20px 0;"></div>
      </div>

      <div v-if="!activeTour">
        <CreateTour/>
        <TourList @editTour="onEditTour" v-bind:tours="tours"></TourList>
      </div>

      <transition name="fadeInUp">
        <EditTour v-if="activeTour" @close="onCloseEdit"></EditTour>
      </transition>
    </div>
    <div v-if="!loaded">
      <i class="text-muted">{{ __('Loading...', 'wp_virtualtour') }}</i>
    </div>
  </div>
</template>

<script>
import { doXhr } from '../helpers.js'
import { store, storeAction } from '../store.js'
import TourList from './TourList.vue'
import CreateTour from './CreateTour.vue'
import EditTour from './EditTour.vue'


export default {
  name: 'App',
  components: {TourList, CreateTour, EditTour},
  inject: ['__'],
  data() {
    return {
      tourToEdit: undefined,
      loaded: false
    }
  },
  computed: {
    activeTour() {
      return store.activeTour
    },
    isSaving() {
      return store.isSaving
    },
    tours() {
      return store.tours
    }
  },
  mounted() {
    doXhr({
      do: 'getTours'
    }).then((res) => {
      storeAction.setTours(res)
      this.loaded = true
    })
  },
  methods: {
    onEditTour(tourId) {
      doXhr({
        do: 'getSingleTour',
        data: {id: tourId}
      }).then((res) => {
        storeAction.setActiveTour(res)
      })
    },
    onCloseEdit() {
      storeAction.setActiveTour(undefined)
      storeAction.setActiveScene(undefined)
      storeAction.setActiveHotspot(undefined)
    }
  }
}
</script>
