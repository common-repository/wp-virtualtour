<template>
  <div>
    <input type="button" name="save" id="publish"
           className="button button-primary button-large"
           :value="__('+ Create Tour', 'wp_virtualtour')"
           style="margin-bottom: 1rem;"
           @click="newTourVisible = !newTourVisible"
    >
    <template v-if="newTourVisible || !tours?.length">
      <div id="dashboard_site_health" className="postbox ">
        <div className="postbox-header">
          <h2 className="hndle ui-sortable-handle">{{ __('New Tour', 'wp_virtualtour') }}</h2>
        </div>
        <div className="inside">
          <label for="newTourTitle">{{ __('Title', 'wp_virtualtour') }}</label><br/>
          <input type="text"
                 name="title"
                 :placeholder="__('Title', 'wp_virtualtour')"
                 id="newTourTitle"
                 class="me-2"
                 v-model="newTourTitle"/>
          <input type="button" name="save"
                 className="button button-primary button-large"
                 :value="__('Create WP Virtual Tour', 'wp_virtualtour')"
                 @click="addNewTour()"
                 :disabled="!newTourTitle.length"
          >
        </div>
      </div>
    </template>

  </div>
</template>
<script>
import { doXhr } from '../helpers.js'
import { store, storeAction } from '../store.js'

export default {
  name: 'CreateTour',
  inject: ['__'],
  computed: {
    tours() {
      return store.tours
    }
  },
  data() {
    return {
      newTourVisible: false,
      newTourTitle: ''
    }
  },
  methods: {
    addNewTour() {
      doXhr({
        do: 'addNewTour',
        data: {
          default: {
            title: this.newTourTitle
          }
        }
      }).then((res) => {
        this.newTourTitle = ''
        this.newTourVisible = false
        storeAction.setTours(res)
      })
    }
  }
}
</script>
