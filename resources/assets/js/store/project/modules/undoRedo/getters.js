export default {
  canUndo: state => state.done.length,
  canRedo: state => state.undone.length,
};
