<template>

  <div v-if="activeHotspot">
    <div class="card-section form mb-2 pt-4" style="margin-top: -1rem">

      <div class="row">
        <div class="col-md-6">

          <div class="mb-2">
            <label>{{ __('Text:', 'wp_virtualtour') }}</label><br/>
            <input type="text" v-model="activeHotspot.text" @blur="updateHotspots()"/>
          </div>
          <div class="mb-2">
            <label>{{ __('Type:', 'wp_virtualtour') }}</label><br/>
            <select type="text" v-model="activeHotspot.type" @change="updateHotspots()">
              <option value="info">{{ __('Info', 'wp_virtualtour') }}</option>
              <option value="scene">{{ __('Scene', 'wp_virtualtour') }}</option>
            </select>
          </div>

        </div>
        <div class="col-md-6">

          <div class="mb-2">
            <label>{{ __('CSS class:', 'wp_virtualtour') }}</label><br/>
            <input type="text" v-model="activeHotspot.cssClass" @blur="updateHotspots()"/>
          </div>
          <div class="mb-2">
            <label>{{ __('Url:', 'wp_virtualtour') }}</label><br/>
            <input type="url" v-model="activeHotspot.URL" @blur="updateHotspots()"/>
          </div>

        </div>
      </div>

      <transition name="fadeInUp">
        <div v-if="activeHotspot.type === 'scene'">
          <label>{{ __('Target scene:', 'wp_virtualtour') }}</label><br/>
          <div class="scene-menu">
            <template v-for="scene in activeTour.scenes" :key="scene.id">
              <a href="#"
                 v-if="activeHotspot.sceneId === scene.id || !activeHotspot.sceneId"
                 class="image mb-3"
                 v-bind:class="{ 'active': activeHotspot.sceneId === scene.id }"
                 @click.prevent="selectTargetScene(scene)"
                 v-bind:style="{ backgroundImage: 'url(' + scene.panorama + ')' }"
              >
                <span class="scene-title" v-html="getSceneTitle(scene)"></span>
              </a>
            </template>
            <a
                href="#"
                class="add-new mb-3"
                v-if="activeHotspot.sceneId"
                @click.prevent="activeHotspot.sceneId = undefined"
            >
              <div class="text-center">
                <span class="dashicons dashicons-remove"></span><br/>
                <span class="scene-title">{{ __('Change scene', 'wp_virtualtour') }}</span>
              </div>
            </a>
          </div>
        </div>
      </transition>

      <div v-if="activeHotspot.type === 'scene'">
        <label for="yawpitch">{{ __('Target yaw / pitch:', 'wp_virtualtour') }}</label><br/>
        <div class="d-flex">
          <input type="text" id="yawpitch" v-model="activeHotspot.targetYaw" @blur="saveHotSpot(true)" placeholder="Initial yaw" class="me-2"/>
          <input type="text" id="yawpitch2" v-model="activeHotspot.targetPitch" @blur="saveHotSpot(true)" placeholder="Initial pitch" class="me-2"/>
          <a href="#"
             class="button button-primary"
             @click.prevent="updateTargetPosition"
          >
            <span class="dashicons dashicons-marker"></span>
          </a>
        </div>
      </div>

      <div>
        <label>{{ __('Hotspot yaw / pitch:', 'wp_virtualtour') }}</label><br/>
        <div class="d-flex">
          <input type="number" id="hotspotyaw" v-model="activeHotspot.yaw" @blur="saveHotSpot" placeholder="Hotspot yaw" class="me-2"/>
          <input type="number" id="hotspotpitch" v-model="activeHotspot.pitch" @blur="saveHotSpot" placeholder="Hotspot pitch" class="me-2"/>
          <a href="#"
             class="button button-primary"
             @click.prevent="updateHotspotPosition"
          >
            <span class="dashicons dashicons-marker"></span>
          </a>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { doXhr } from '../helpers.js'
import { store } from '../store.js'

export default {
  name: 'EditHotSpot',
  inject: ['__'],
  data() {
    return {
      selectedScene: undefined
    }
  },
  computed: {
    activeHotspot() {
      return store.activeHotspot
    },
    activeTour() {
      return store.activeTour
    },
    currentYawPitch() {
      return store.currentYawPitch
    }
  },
  mounted() {
  },
  methods: {
    validate() {
      if (!parseFloat(this.activeHotspot.targetYaw)) {
        if (this.activeHotspot.targetYaw !== 'same' && this.activeHotspot.targetYaw !== 'sameAzimuth') {
          this.activeHotspot.targetYaw = 'same'
        }
      }
      if (!parseFloat(this.activeHotspot.targetPitch)) {
        if (this.activeHotspot.targetPitch !== 'same' && this.activeHotspot.targetPitch !== 'sameAzimuth') {
          this.activeHotspot.targetPitch = 'same'
        }
      }
    },
    updateHotspots() {
      this.$emit('updateHotspots')
    },
    saveHotSpot(validate) {
      if (validate)
        this.validate()
      this.$emit('saveHotSpot')
    },
    closeHotSpot() {
      this.$emit('closeHotSpot')
    },
    selectTargetScene(scene) {
      this.activeHotspot.sceneId = scene.id
      this.updateHotspots()
    },
    updateHotspotPosition() {
      this.activeHotspot.yaw = this.currentYawPitch[0]
      this.activeHotspot.pitch = this.currentYawPitch[1]
      this.updateHotspots()
    },
    updateTargetPosition() {
      this.activeHotspot.targetYaw = this.currentYawPitch[0]
      this.activeHotspot.targetPitch = this.currentYawPitch[1]
      this.updateHotspots()
    },
    getSceneTitle(scene) {
      return (scene.title !== '') ? scene.title : this.__('Untitled Scene', 'wp_virtualtour')
    }
  }
}

</script>
