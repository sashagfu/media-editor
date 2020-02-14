<template>
  <el-dialog
    :visible.sync="visible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    title="Flag Project"
    width="30%"
    class="FlagProjectBox flag-project-box"
    @close="closeFlagDialog(false)"
  >
    <div
      class="reasons"
    >
      <el-checkbox-group
        v-model="flagReasons"
        class="input-box__reasons"
      >
        <el-checkbox
          :label="3"
          class="input-box--checkbox"
        >
          {{ trans('reports.user_inappropriate_content') }}
        </el-checkbox>
        <el-checkbox
          :label="6"
          class="input-box--checkbox"
        >
          {{ trans('reports.copyright') }}
        </el-checkbox>
        <div
          class="input-box"
        >
          <label
            class="input-box__label"
            for="input-box__desc"
          >
            {{ trans('reports.description') }}
          </label>
          <textarea
            id="input-box__desc"
            ref="textarea"
            v-model.trim="flag.description"
            type="text"
            name="description"
            class="input-box__input input-box__input--textarea"
          />
        </div>
      </el-checkbox-group>
    </div>
    <div
      slot="footer"
      class="action-dialog__footer"
    >
      <el-button
        plain
        size="small"
        @click="closeFlagDialog"
      >
        {{ trans('common.cancel') }}
      </el-button>
      <el-button
        :disabled="!flagReasons.length"
        type="info"
        size="small"
        @click="flagProject"
      >
        <font-awesome-icon
          v-if="loading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--spinner"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'flag']"
          class="fa-icon fa-icon--spinner"
        />
        <span class="action-dialog__btn-title">
          {{ trans('projects.flag') }}
        </span>
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
import * as constants from 'Helpers/constants';

import CREATE_REPORT from 'Gql/reports/mutations/createReport.graphql';

export default {
  name: 'FlagProjectBox',
  props: {
    project: {
      type: Object,
      default: () => {
      },
    },
  },
  data() {
    return {
      loading: false,
      visible: true,
      flagReasons: [],
      flag: {
        description: '',
      },
    };
  },
  computed: {
    copyrightSelected() {
      return this.flagReasons.find(f => f === constants.REPORT_REASON_COPYRIGHT);
    },
  },
  methods: {
    closeFlagDialog({ flagged = false }) {
      this.visible = false;
      this.$emit('close-dialog', flagged);
    },
    async flagProject() {
      if (this.loading) return;
      this.loading = true;
      await this.$apollo.mutate({
        mutation: CREATE_REPORT,
        variables: {
          report: {
            reportableId: this.project.id,
            reportableType: 'project',
            reasons: this.flagReasons,
            description: this.flag.description,
          },
        },
      });
      this.loading = false;
      this.$notify.success({
        title: 'Success',
        message: this.trans('reports.project_flagged'),
      });
      this.closeFlagDialog({ flagged: true });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .flag-project-box
    text-align left

  .reasons
    .input-box
      &__label
        font-size 14px
        margin-bottom 5px

  .input-box {
    align-items: flex-start;
    background-color: $background-light;
    border-radius: $radius;
    border: 1px solid $border;
    display: flex;
    flex-direction: column;
    padding: 0 18px;
    position: relative;
    margin-top: 20px;

    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display: flex;
      flex-direction: column;
      height: 20px;
      justify-content: flex-end;
    }

    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      border: none;
      height: 32px;
      outline: none;
      width: 100%;
      resize: none;

      &--textarea {
        padding: 8px 0;
      }
    }
  }
</style>
