const initValue = () => ({
  top: 0,
  bottom: 0,
  left: 0,
  right: 0,
  width: 0,
  height: 0,
});

export default {
  timelineLayersContainer: initValue(),
  timelineRulerContainer: initValue(),
  activeDroppable: initValue(),
  previewProjectContainer: initValue(),
};
