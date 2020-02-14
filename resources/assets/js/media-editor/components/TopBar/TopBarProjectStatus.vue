<template>
  <div class="TopBarProjectStatus me-project-status">
    <div className="me-project-status__text">
      {{ projectStatus }}
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { AUTOSAVE_DELAY } from '../../config/settings';

export default {
  name: 'TopBarProjectStatus',
  data() {
    return {
      saveTimeout: null,
    };
  },
  computed: {
    ...mapGetters('project', [
      'saved',
      'saving',
      'projectData',
    ]),
    projectStatus() {
      if (this.saving) {
        return this.trans('projects.saving');
      } else if (this.saveTimeout) {
        return this.trans('projects.saving_scheduled');
      } else if (this.saved) {
        return this.trans('projects.save_nothing');
      }
      return this.trans('projects.save_needed');
    },
  },
  watch: {
    saved() {
      if (!this.projectData.isPublished) {
        this.setScheduleSaving();
        this.setCloseWindowConfirm();
      }
    },
  },
  methods: {
    ...mapActions('project', [
      'saveProject',
    ]),
    /**
         * Schedule saving project
         * base on param AUTOSAVE_DELAY which get from config
         *
         * @param props
         * @private
         */
    setScheduleSaving() {
      // If there is some to save
      if (!this.saved) {
        // If timeout was not set
        if (!this.saveTimeout) {
          // Set timeout
          this.saveTimeout = setTimeout(this.saveProject, AUTOSAVE_DELAY);
        }
        // Otherwise if nothing to save
        // If timeout was set and not clear
      } else if (this.saveTimeout) {
        // Clear timeout
        clearTimeout(this.saveTimeout);
        // and variable with timeout id
        this.saveTimeout = null;
      }
    },
    /**
         * Set if show confirm on close browser tab or window
         */
    setCloseWindowConfirm() {
      window.onbeforeunload = this.saved ? null : this.showOnCloseConfirm;
    },
    /**
         * Function to set confirm on close browser tab or window
         * ~*~*~ It's magic here ~*~*~
         * (I don't know how it works and is it works at all)
         *
         * @param e
         * @returns {string}
         * @private
         */
    showOnCloseConfirm(e) {
      const localEv = e || window.event;
      if (localEv) {
        // localEv.returnValue = 'Changes you made not be saved';
        Object.assign(localEv, {
          returnValue: this.trans('projects.changes_not_saved'),
        });
      }
      // For Safari
      return this.trans('projects.changes_not_saved');
    },
  },
};
</script>
