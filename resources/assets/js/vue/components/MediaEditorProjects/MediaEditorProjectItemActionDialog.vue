<template>
  <div class="MediaEditorProjectItemActionDialog action-dialog">
    <el-dialog
      v-if="action === 'delete'"
      title="Delete project"
      visible
    >
      <div class="action-dialog__text">
        Do you want to delete project?
      </div>
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <button class="action-dialog__button">
          Cancel
        </button>
        <button class="action-dialog__button">
          Delete
        </button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'MediaEditorProjectItemActionDialog',
  props: {
    title: {
      type: String,
      default: '',
    },
    action: {
      type: String,
      default: '',
      validator: val => ['delete', 'rename', ''].indexOf(val) !== -1,
    },
  },
};
</script>
