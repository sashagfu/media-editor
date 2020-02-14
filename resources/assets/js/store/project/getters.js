export default {
  saving: state => state.saving,
  saved: state => state.saved,
  projectData: state => state.projectData,
  id: state => state.projectData.id,
  items: state => state.projectData.value,
  title: state => state.projectData.title,
  layers: state => state.projectData.layers,
  pendingProjectValue: state => state.pendingProjectValue,
};
