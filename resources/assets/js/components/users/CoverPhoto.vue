<template>
  <div>
    <div
      class="cover-photo"
      :style="'background-image: url(' + coverPhotoSrc + ');'"
      style="position: relative;">
      <div
        class="hover-border hover-pointer"
        v-if="editing == true"
        v-b-modal.changeCoverPhoto/>
      <div
        class="text-white text-shadow text-center hover-pointer"
        style="text-align: center; position: absolute; bottom: 50px; width: 100%;"
        v-if="editing == true"
        v-b-modal.changeCoverPhoto>
        <i class="fa fa-image fa-3x mb-3"/>
        <h5 class="mb-0">Click to change your cover photo</h5>
      </div>
    </div>

    <form
      method="POST"
      enctype="multipart/form-data"
      v-if="editing == true">
      <b-modal
        id="changeCoverPhoto"
        title="Change Your Cover Photo">
        <div class="form-group">
          <label for="file">Cover Photo (1920x500)</label>
          <div>
            <image-upload
              name="file"
              @loaded="onLoad"/>
          </div>
        </div>
        <div class="form">
          <p>Cover Photo Preview</p>
          <img
            :src="coverPhotoSrc"
            class="img-fluid">
        </div>
        <div
          slot="modal-footer"
          class="w-100">
          <button
            type="button"
            class="btn btn-danger"
            @click="destroy()">Remove Cover Photo</button>
        </div>
      </b-modal>
    </form>
  </div>
</template>

<script>
import { EventBus } from '../../event-bus';

export default {
  props: {
    src: {
      type: String,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      editing: false,
      image: {},
      coverPhotoSrc: this.src,
    };
  },

  mounted() {
    EventBus.$on('editing-profile', (editing) => {
      this.editing = editing;
    });
  },

  methods: {
    onLoad(image) {
      this.store(image);
    },

    store(image) {
      const data = new FormData();

      data.append('file', image.file);

      axios.post(route('api.users.cover_photos.store', this.user.id), data)
        .then((response) => {
          // Set the current cover photo url's <img src> equal to the uploaded cover photo url
          this.coverPhotoSrc = image.src;
        });
    },

    destroy() {
      this.working = true;

      axios.delete(route('api.users.cover_photos.destroy', this.user.id))
        .then((response) => {
          this.coverPhotoSrc = image.src;
        })
        .finally((response) => {
          this.working = false;
        });
    },
  },
};
</script>
