export const FRAME_PER_SEC = 25;
export const PX_PER_SEC = 0.004;
export const ONE_FRAME_LENGTH = 1000 / FRAME_PER_SEC;
// export const ITEM_THUMB_HEIGHT = 47;
// export const ITEM_THUMB_FRAME_WIDTH = 89;
// export const ITEM_THUMB_IMAGE_DEFAULT_DURATION = 10000;
export const RESOLUTION = { width: 1920, height: 1080 };
export const RESOLUTION_RATIO = RESOLUTION.width / RESOLUTION.height;
export const AUTOSAVE_DELAY = 5000;
export const ITEM_THUMB = {
  timelineLayerHeight: 59,
  timelineLayerItemHeight: 58,
  // boundRestrictSelector: '.me-tl__layers-container',
  padding: 2,
  borderWidth: 2,
  boxBorder: 4,
  // paddingSum: 6,
  // backgroundColor: '#555',
  strokeColor: '#4a4a4a',
  fillColor: '#4a4a4a',
  lineWidth: 1,
  frameWidth: 89,
  imageDefaultDuration: 10000,
  height: 50,
  spriteFrameHeight: 50,
};
export const TIMELINE_DURATIONS = 10; // min
export const MAX_DURATIONS = 5; // min
export const TIMELINE_ZOOM = {
  min: 1,
  max: 10,
};
export const TIMELINE_RULER = {
  // How much pixels between each line
  // This value can float in order to adopt to rounded seconds
  pxBtwLine: 80,
  // If space between line more than this value
  // then small line on half will be draw
  // separateNeededIf: 82,
  lineHeight: 15,
  // lineHeightSmall: 5,
  lineWidth: 1,
  lineColor: '#4a4a4a',
  textColor: '#4a4a4a',
  textMarginLeft: 5,
  textMarginBottom: 4,
};
// Which color filters can be apply for image
export const IMAGE_COLOR_FILTERS = [
  {
    name: 'brightness',
    divider: 100,
    min: -100,
    max: 100,
    step: 1,
  }, {
    name: 'contrast',
    divider: 100,
    min: -100,
    max: 100,
    step: 1,
  }, {
    name: 'saturation',
    divider: 100,
    min: -100,
    max: 100,
    step: 1,
  }, {
    name: 'hue',
    divider: 1,
    min: -180,
    max: 180,
    step: 1,
  },
];

export const CANVAS_SIZE = {
  width: 640,
  height: 360,
};
