<template>

  <div v-if="activeScene">

    <!--    <pre>{{ activeHotspot }}</pre>-->

    <div class="d-flex justify-content-end mb-2">
      <a href="#" class="button button-custom danger" @click.prevent="deleteScene">
        <span class="dashicons dashicons-trash"></span> {{ __('Delete scene', 'wp_virtualtour') }}
      </a>
    </div>

    <div class="">
      <a v-if="!activeScene.panorama || activeScene.panorama === $scenePlaceholder" href="#" @click.prevent="openWpMediaPopup" class="pano-upload-image">
        <span class="dashicons dashicons-format-image pe-2"></span>{{ __('Select image', 'wp_virtualtour') }}
      </a>
      <a v-if="activeScene.panorama && activeScene.panorama !== $scenePlaceholder" href="#" @click.prevent="removeImage" class="pano-upload-image">
        <span class="dashicons dashicons-trash pe-2"></span>{{ __('Remove image', 'wp_virtualtour') }}
      </a>
      <div>
        <label for="scenetitle">{{ __('Scene title:', 'wp_virtualtour') }}</label><br/>
        <input type="text" name="title" v-model="activeScene.title" @blur="saveTour()" placeholder="Scene title" id="scenetitle"/>
      </div>
      <div>
        <label for="title">{{ __('Initial yaw / pitch:', 'wp_virtualtour') }}</label><br/>
        <div class="d-flex">
          <input type="number" name="title" v-model="activeScene.yaw" @blur="saveTour" placeholder="Initial yaw" class="me-2"/>
          <input type="number" name="title" v-model="activeScene.pitch" @blur="saveTour" placeholder="Initial pitch" class="me-2"/>
          <div class="wpvt_tooltip" data-tip="By adding this class you can provide almost any element with a tool tip." tabindex="1">
            <a href="#"
               class="button button-primary me-2"
               @click.prevent="setInitialPitchYaw"
            >
              <span class="dashicons dashicons-marker"></span>
            </a>
          </div>
          <a href="#"
             class="button button-primary"
             @click.prevent="removeInitialPitchYaw"
             :disabled="!activeScene.pitch && !activeScene.yaw"
          >
            <span class="dashicons dashicons-remove"></span>
          </a>
        </div>
      </div>

      <div class="position-relative">
        <label>{{ __('Hotspots:', 'wp_virtualtour') }}</label>
        <div class="hotSpot-menu">
          <a href="#"
             class="button button-primary button-large mb-2"
             @click.prevent="addHotSpot"
          >
            {{ __('+Add Hotpot at current location', 'wp_virtualtour') }}
          </a>
          <div v-if="!activeScene.hotSpots?.length">
            <i class="text-muted">{{ __('No hotspots defined', 'wp_virtualtour') }}</i>
          </div>
          <transition-group name="list" tag="section"
                            :css="doHotSpotListTransition">
            <section id="list" v-for="hotSpot in activeScene.hotSpots" :key="hotSpot.id">
              <div class="hot-spot-button mb-2">
                <a href="#"
                   class="text-ellipsis"
                   @mouseover="hoverHotspot(hotSpot)"
                   @click.prevent.blur="activateHotspot(hotSpot)"
                >
                  <span class="hotSpot-title " v-html="getHotSpotTitle(hotSpot)"></span>
                </a>
                <div class="icons">
                  <a href="#" @click.prevent="onDeleteHotSpot(hotSpot.id)" class="dashicons dashicons-trash"></a>
                  <a href="#" @click.prevent="jumpToHotspot(hotSpot)" class="dashicons dashicons-location"></a>
                  <span v-if="hotSpot.type === 'info'" class="dashicons dashicons-info-outline"></span>
                  <span v-if="hotSpot.type === 'scene'" class="dashicons dashicons-arrow-up-alt"></span>
                </div>
              </div>
              <transition name="fadeInUp">
                <EditHotSpot
                    v-if="activeHotspot?.id === hotSpot.id"
                    @saveHotSpot="saveTour"
                    @closeHotSpot="onCloseActiveHotspot"
                    @deleteHotSpot="onDeleteHotSpot"
                    @updateHotspots="onUpdateHotspots"
                ></EditHotSpot>
              </transition>

            </section>
          </transition-group>
        </div>
      </div>

    </div>

  </div>
</template>

<script>
import { doXhr, generateUuid } from '../helpers.js'
import EditHotSpot from './EditHotSpot.vue'
import { store, storeAction } from '../store.js'

export default {
  name: 'EditScene',
  components: {EditHotSpot},
  inject: ['__', '$scenePlaceholder'],
  data() {
    return {
      wpMedia: undefined,
      doHotSpotListTransition: false
    }
  },
  computed: {
    activeTour() {
      return store.activeTour
    },
    activeScene() {
      return store.activeScene
    },
    activeHotspot() {
      return store.activeHotspot
    },
    pannellum() {
      return store.pannellum
    },
    currentYawPitch() {
      return store.currentYawPitch
    },
    pannellumIsCtrlClick() {
      return store.pannellumIsCtrlClick
    }
  },
  mounted() {
    if (this.activeScene.isNewlyAdded) {
      this.openWpMediaPopup()
      delete this.activeScene.isNewlyAdded
    }
  },
  methods: {
    openWpMediaPopup() {
      this.wpMedia = wp.media({
        title: 'Insert image',
        library: {
          type: 'image'
        },
        button: {
          text: 'Use this image'
        },
        multiple: false
      }).on('select', () => {
        const attachment = this.wpMedia.state().get('selection').first().toJSON()
        this.activeScene.panorama = attachment.url
        this.$emit('reInitPannellum')
        this.saveTour()
      }).open()
    },
    removeImage() {
      this.activeScene.panorama = this.$scenePlaceholder
      this.activeMousePitch = undefined
      this.$emit('reInitPannellum')
      this.saveTour()
    },
    activateHotspot(hotSpot) {
      if (this.activeHotspot?.id === hotSpot.id) {
        storeAction.setActiveHotspot(undefined)
      } else {
        storeAction.setActiveHotspot(hotSpot)
        this.jumpToHotspot(hotSpot)
      }
    },
    jumpToHotspot(hotSpot) {
      this.pannellum.setYaw(hotSpot.yaw, 200)
      this.pannellum.setPitch(hotSpot.pitch, 200)
      storeAction.setCurrentYawPitch(hotSpot.yaw, hotSpot.pitch)
    },
    saveTour() {
      this.$emit('reInitPannellum', this.activeScene.id)
      this.$emit('saveTour')
    },
    deleteScene() {
      this.$emit('deleteScene')
    },
    closeScene() {
      this.saveTour()
      this.$emit('closeScene')
    },
    onUpdateHotspots() {
      // if (this.activeHotspot) {
      //   this.$emit('reInitPannellum', this.activeScene.id)
      // }
      this.saveTour()
    },
    onCloseActiveHotspot() {
      storeAction.setActiveHotspot(undefined)
    },
    addHotSpot() {
      const newHotspot = {
        id: generateUuid(),
        yaw: this.currentYawPitch[0],
        pitch: this.currentYawPitch[1],
        targetYaw: 'same',
        targetPitch: 'same',
        text: '',
        type: 'info',
        URL: '',
        clickHandlerFunc: (e) => {
          storeAction.setPannellumIsCtrlClick(e.ctrlKey)
        },
        sceneId: undefined
      }
      storeAction.setActiveHotspot(newHotspot)
      this.pannellum.addHotSpot(this.activeHotspot)
      // this.saveTour()
      // this.$emit('reInitPannellum', this.activeScene.id)
      this.$emit('saveTour')
    },
    onDeleteHotSpot(id) {
      // we do the transition only on delete, because otherwise it will flicker between scene changes
      this.doHotSpotListTransition = true
      setTimeout(() => {
        this.doHotSpotListTransition = false
      }, 200)
      if (!id)
        id = this.activeHotspot.id
      this.pannellum.removeHotSpot(id)
      storeAction.setActiveHotspot(undefined)
      this.saveTour()
    },
    getHotSpotTitle(hotSpot) {
      return (hotSpot.text !== '') ? hotSpot.text : this.__('Untitled Hotspot', 'wp_virtualtour')
    },
    hoverHotspot(hotSpot) {
    },
    setInitialPitchYaw() {
      if (this.currentYawPitch[0] && this.currentYawPitch[1]) {
        this.activeScene.yaw = this.currentYawPitch[0]
        this.activeScene.pitch = this.currentYawPitch[1]
      }
      this.saveTour()
    },
    removeInitialPitchYaw() {
      this.activeScene.pitch = 0
      this.activeScene.yaw = 0
      this.saveTour()
    }
  }
}

</script>
