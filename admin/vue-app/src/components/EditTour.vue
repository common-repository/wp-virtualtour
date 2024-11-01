<template>
  <div class="postbox" v-if="activeTour">
    <div class="postbox-header">
      <h3 class="hndle ui-sortable-handle" v-html="activeTour.default.title" style="cursor: default;"></h3>
      <span class="dashicons dashicons-no pe-2" style="cursor: pointer;" @click="close"></span>
    </div>
    <div class="inside">
      <div class="row">
        <div class="col-md-6">

          <div>
            <div class="card-section w-100 py-0">
              <h3 class="d-flex justify-content-between collapsible-title position-relative" :class="tourDetailsCollapsed ? 'collapsed' : ''">
                <div v-html="__('Tour details', 'wp_virtualtour')"></div>
                <a href="#"
                   class="dashicons ms-auto stretched-link toggle-btn"
                   :class="tourDetailsCollapsed ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-up-alt2'"
                   @click.prevent="tourDetailsCollapsed = !tourDetailsCollapsed"></a><br/>
              </h3>
              <div v-if="!tourDetailsCollapsed" class="row form">
                <div class="col-md-6">
                  <div class="mb-2">
                    <label for="title">{{ __('Title', 'wp_virtualtour') }}</label><br/>
                    <input type="text" v-model="activeTour.default.title" @blur="saveTour(true)" id="title"/>
                  </div>
                  <div class="mb-2">
                    <label for="author">{{ __('Author', 'wp_virtualtour') }}</label><br/>
                    <input type="text" v-model="activeTour.default.author" @blur="saveTour(true)" id="author"/>
                  </div>
                  <div class="mb-2">
                    <label for="authorURL">{{ __('Author URL', 'wp_virtualtour') }}</label><br/>
                    <input type="url" v-model="activeTour.default.authorURL" @blur="saveTour(true)" id="authorURL"/>
                  </div>
                  <div class="mb-2">
                    <!--                      <label for="sceneFadeDuration">sceneFadeDuration</label><br/>-->
                    <!--                      <input type="text" v-model="activeTour.sceneFadeDuration" @blur="saveTour(true)" id="sceneFadeDuration" type="number"/>-->
                  </div>
                  <div class="mb-2">
                    <label class="form-check-label" for="flexSwitchCheckDefault">{{ __('Autoload', 'wp_virtualtour') }}</label><br/>
                    <div class="form-check form-switch p-0">
                      <input v-model="activeTour.default.autoLoad"
                             @change="saveTour(true)"
                             class="form-check-input m-0"
                             type="checkbox"
                             id="flexSwitchCheckDefault">
                    </div>
                  </div>
                  <!--                  <div class="mb-2">-->
                  <!--                    <label class="form-check-label" for="hideSceneTitleAuthor">Hide scene title/author</label><br/>-->
                  <!--                    <div class="form-check form-switch p-0">-->
                  <!--                      <input v-model="activeTour.hideSceneTitleAuthor"-->
                  <!--                             @change="saveTour(true)"-->
                  <!--                             class="form-check-input m-0"-->
                  <!--                             type="checkbox"-->
                  <!--                             id="hideSceneTitleAuthor">-->
                  <!--                    </div>-->
                  <!--                  </div>-->
                  <div class="mb-2">
                    <label class="form-check-label" for="compass">{{ __('Show compass', 'wp_virtualtour') }}</label><br/>
                    <div class="form-check form-switch p-0">
                      <input v-model="activeTour.default.compass"
                             @change="saveTour(true)"
                             class="form-check-input m-0"
                             type="checkbox"
                             id="compass">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">

                  <div class="mb-2">
                    <label for="firstScene">{{ __('Initial scene', 'wp_virtualtour') }}</label><br/>
                    <select type="text"
                            v-if="activeTour.scenes"
                            v-model="activeTour.default.firstScene"
                            @change="saveTour"
                            id="firstScene">
                      <template v-for="scene in activeTour.scenes" :key="scene.id">
                        <option :value="scene.id">{{ getSceneTitle(scene) }}</option>
                      </template>
                    </select>
                    <div v-if="!activeTour.scenes">
                      <i class="text-muted">No scenes found</i>
                    </div>
                  </div>

                  <div class="mb-2">
                    <label for="firstScene">{{ __('Preview image', 'wp_virtualtour') }}</label><br/>
                    <div v-if="activeTour.default.preview" class="tourimage" v-bind:style="{ backgroundImage: 'url(' + activeTour.default.preview + ')' }"></div>
                    <a href="#" @click="openWpMediaPopup" v-if="!activeTour.default.preview" class="tourimage-placeholder text-decoration-none">
                      <div class="text-center">
                        <span class="dashicons dashicons-plus-alt"></span><br/>
                        <span class="scene-title">{{ __('Add image', 'wp_virtualtour') }}</span>
                      </div>
                    </a>
                    <a href="#" v-if="activeTour.default.preview" @click="removeImage">{{ __('Remove image', 'wp_virtualtour') }}</a>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="scenes">
            <h4>Scenes</h4>
            <div class="scene-menu card-section" ref="sceneMenu">
              <template v-if="activeTour.scenes">
                <template v-for="(scene, sceneId) in activeTour.scenes" :key="scene.id">
                  <div class="droppable"
                       v-if="index === 0"
                       :data-index="sceneId"
                       @dragover.prevent="dragOver"
                       @dragenter.prevent="dragEnter"
                       @dragleave.prevent="dragLeave"
                       @drop="dragDrop"
                  ></div>
                  <a href="#"
                     class="mb-3"
                     @dragstart="dragStart($event, scene)"
                     @dragend="dragEnd"
                     draggable="true"
                     v-bind:class="{ 'active': activeScene?.id === scene.id }"
                     @click.prevent="activateScene(scene)"
                     v-bind:style="{ backgroundImage: 'url(' + scene.panorama + ')' }"
                  >
                    <span class="scene-title" v-html="getSceneTitle(scene)"></span>
                  </a>
                  <div class="droppable"
                       :data-index="sceneId"
                       @dragover.prevent="dragOver"
                       @dragenter.prevent="dragEnter"
                       @dragleave.prevent="dragLeave"
                       @drop="dragDrop"
                  ></div>
                </template>
              </template>
              <a href="#"
                 class="add-new mb-3"
                 ref="addNewSceneBtn"
                 @click.prevent="addNewScene"
              >
                <div class="text-center">
                  <span class="dashicons dashicons-plus-alt"></span><br/>
                  <span class="scene-title">{{ __('Add scene', 'wp_virtualtour') }}</span>
                </div>
              </a>
            </div>
          </div>
          <div class="my-3 edit-scene-wrapper">
            <EditScene
                @saveTour="saveTour"
                @closeScene="activeScene = undefined"
                @deleteScene="onDeleteScene"
                @reInitPannellum="initPannellum"
                v-if="activeScene"
            ></EditScene>
          </div>

        </div>
        <div class="col-md-6 empty-space">

          <!--          <pre>{{ activeTour }}</pre>-->

          <div class="pannellum-wrapper" v-if="activeTour.scenes">
            <div class="d-flex align-items-center mb-2 hotspot-selector">
              <label class="pe-2 my-0">{{ __('Yaw:', 'wp_virtualtour') }}</label>
              <input type="number"
                     step="0.1"
                     @change="changeCurrentYawPitch" v-model="currentYawPitch[0]">
              <label class="px-2 my-0">{{ __('Pitch:', 'wp_virtualtour') }}</label>
              <input type="number"
                     step="0.1"
                     class="me-2 d-block"
                     @change="changeCurrentYawPitch" v-model="currentYawPitch[1]">
            </div>
            <div class="panorama-wrapper">
              <div id="panorama" ref="pano"></div>
            </div>
          </div>

          <div class="pannellum-wrapper-placeholder" v-if="!activeTour.scenes">
            <i>{{ __('Please create a new scene', 'wp_virtualtour') }}</i>
          </div>

        </div>

      </div>

    </div>
  </div>
</template>

<script>
import 'pannellum'

import { arrayMove, doXhr, generateUuid } from '../helpers.js'
import { store, storeAction } from '../store.js'

import EditScene from './EditScene.vue'

export default {
  name: 'EditTour',
  components: {EditScene},
  inject: ['__', '$scenePlaceholder'],
  data() {
    return {
      tourImage: '',
      wpMedia: undefined,
      draggedScene: undefined,
      tourDetailsCollapsed: true
    }
  },
  computed: {
    activeHotspot() {
      return store.activeHotspot
    },
    activeTour() {
      return store.activeTour
    },
    activeScene() {
      return store.activeScene
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
    console.log('desc: ', this.activeTour)
    if (this.activeTour.scenes) {
      storeAction.setActiveScene(this.activeTour.scenes[Object.keys(this.activeTour.scenes)[0]])
      this.initPannellum()
    }
  },
  methods: {
    changeCurrentYawPitch() {
      storeAction.setCurrentYawPitch(
          parseFloat(this.currentYawPitch[0]),
          parseFloat(this.currentYawPitch[1])
      )
      this.pannellum.setYaw(this.currentYawPitch[0], 200)
      this.pannellum.setPitch(this.currentYawPitch[1], 200)
    },
    initPannellum(sceneId) {
      if (this.pannellum)
        this.pannellum.destroy()

      if (!this.activeTour.scenes)
        return

      const penScenes = {}

      // add click handler to hotspot
      for (const [id, scene] of Object.entries(this.activeTour.scenes)) {
        if (typeof scene.hotSpots !== 'undefined') {
          scene.hotSpots.filter(hotSpot => {
            hotSpot.clickHandlerFunc = (e) => {
              storeAction.setPannellumIsCtrlClick(e.ctrlKey)
            }
          })
        }
        penScenes[scene.id] = scene
      }

      // drop author if empty, otherwise "by" will still be shown
      if (this.activeTour.default.author === '') {
        delete this.activeTour.default.author
      }

      if (sceneId) {
        this.activeTour.firstScene = sceneId
      }

      console.log('this.activeTour: ', this.activeTour)

      storeAction.setPannellum(
          window.pannellum.viewer(this.$refs['pano'], this.activeTour)
      )

      if ((this.currentYawPitch[0] || this.currentYawPitch[1])) {
        setTimeout(() => {
          this.pannellum.setYaw(this.currentYawPitch[0], 0)
          this.pannellum.setPitch(this.currentYawPitch[1], 0)
        }, 100)
      }

      this.pannellum.on('mouseup', (e) => {
        storeAction.setCurrentYawPitch(
            this.pannellum.getYaw(),
            this.pannellum.getPitch()
        )
      })

      this.pannellum.on('scenechange', (sceneId) => {
        if (!this.pannellumIsCtrlClick) {
          storeAction.setActiveScene(undefined) // to re-mount edit scene window
          storeAction.setActiveHotspot(undefined)
          this.$nextTick(() => {
            storeAction.setActiveScene(this.activeTour.scenes[sceneId])
            storeAction.setActiveHotspot(this.activeHotspot)
          })
        }
      })
    },
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
        this.activeTour.default.preview = attachment.url
        this.initPannellum()
      }).open()
    },
    removeImage() {
      this.activeTour.default.preview = undefined
      this.initPannellum()
    },
    addNewScene() {
      this.$refs['addNewSceneBtn'].blur()
      storeAction.setActiveScene(undefined) // to re-mount edit scene window
      this.$nextTick(() => {
        const newSceneId = generateUuid()
        const newScene = {
          id: newSceneId,
          title: '',
          cssClass: '',
          panorama: this.$scenePlaceholder,
          pitch: 0,
          yaw: 0,
          type: 'equirectangular',
          hotSpots: [],
          isNewlyAdded: true
        }
        storeAction.setActiveScene(newScene)

        // create the scene object if not present
        if (typeof this.activeTour.scenes === 'undefined') {
          this.activeTour['scenes'] = {}
        }

        // add new scene
        this.activeTour.scenes[newSceneId] = newScene

        // set new scene to initial scene for our tour, if it has no initial scene yet
        if (!this.activeTour.default.firstScene) {
          this.activeTour.default.firstScene = newSceneId
        } else {
        }
        this.$nextTick(() => {
          this.initPannellum(newSceneId)
          this.saveTour()
        })
      })
    },
    activateScene(scene) {
      storeAction.setActiveHotspot(undefined)
      storeAction.setActiveScene(scene)
      this.initPannellum(scene.id)
    },
    saveTour(reInitPannellum) {
      if (reInitPannellum) {
        this.initPannellum()
      }
      doXhr({
        do: 'updateTour',
        data: this.activeTour
      }).then((res) => {
        // console.log(res)
        // storeAction.setActiveTour(res)
      })
    },
    getSceneTitle(scene) {
      return (scene.title !== '') ? scene.title : this.__('Untitled Scene', 'wp_virtualtour')
    },
    onDeleteScene() {
      // this.activeTour.scenes = this.activeTour.scenes.filter(scene => scene.id !== this.activeScene.id)
      delete this.activeTour.scenes[this.activeScene.id]
      storeAction.setActiveScene(this.activeTour.scenes[0])
      this.initPannellum()
      this.saveTour()
    },
    close() {
      this.$emit('close')
    },
    getTourTitle() {

    },
    dragStart(e, scene) {
      let oldIndex = 0
      let i = 0
      for (const [id, sc] of Object.entries(this.activeTour.scenes)) {
        if (sc.id === scene.id) {
          oldIndex = i
        }
        i++
      }
      e.target.blur()
      e.dataTransfer.setData('id', scene.id)
      e.dataTransfer.setData('oldIndex', oldIndex)
      e.target.classList.add('hold')
      setTimeout(() => {
        this.$refs['sceneMenu'].classList.add('is-dragging')
      }, 10)
    },
    dragEnd(e) {
      this.$refs['sceneMenu'].classList.remove('is-dragging')
      this.$refs['sceneMenu'].querySelectorAll('.active-dragging')
          .forEach(item => item.classList.remove('active-dragging'))
    },
    dragEnter(e) {
      e.target.classList.add('active-dragging')
    },
    dragLeave(e) {
      e.target.classList.remove('active-dragging')
    },
    dragOver() {
    },
    dragDrop(e) {
      const sceneId = e.dataTransfer.getData('id')
      const oldIndex = e.dataTransfer.getData('oldIndex')
      // this.draggedScene = this.activeTour.scenes.find(sc => sc === sceneId)

      for (const [id, sc] of Object.entries(this.activeTour.scenes)) {
        if (sc === sceneId) {
          this.draggedScene = sc
        }
      }

      let newIndex = 0
      let i = 0
      for (const [id, sc] of Object.entries(this.activeTour.scenes)) {
        if (sc.id === e.target.getAttribute('data-index')) {
          newIndex = i
        }
        i++
      }

      this.activeTour.scenes = arrayMove(this.activeTour.scenes, oldIndex, newIndex, true)
      this.saveTour()
    }
  }
}

</script>
