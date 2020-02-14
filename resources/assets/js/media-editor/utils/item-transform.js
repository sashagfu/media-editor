import _ from 'lodash';
import { RESOLUTION_RATIO } from '../config/settings';

/**
 * Get ratio of width and height of file
 *
 * @param width
 * @param height
 * @returns {number}
 */
export function getFileRatio({ width, height }) {
  return (width / height);
}

export function getFileRatioByRelativeSize({ width, height }) {
  return width > height ? RESOLUTION_RATIO * height : RESOLUTION_RATIO * width;
}

/**
 * Get initial relative position of file
 * @param width
 * @param height
 * @returns {{x: number, y: number}}
 */
export function getInitialRelativePosition({ width, height }) {
  // Return null if there are no width or height of file
  if (_.isUndefined(width) || _.isUndefined(height)) return null;

  const ratio = getFileRatio({ width, height });
  const position = { x: 0, y: 0 };
  const smallerSide = ratio > RESOLUTION_RATIO ? 'y' : 'x';

  position[smallerSide] = (1 - (ratio / RESOLUTION_RATIO)) / 2;

  return position;
}

/**
 * Get initial relative size of file
 *
 * @param width
 * @param height
 * @param ratio
 * @returns {*}
 */
export function getInitialRelativeSize({ width, height }, ratio) {
  // Return null if there are no width or height of file
  if (_.isUndefined(width) || _.isUndefined(height)) return null;

  const rat = _.isUndefined(ratio) ? getFileRatio({ width, height }) : ratio;

  if (rat > RESOLUTION_RATIO) {
    return {
      width: 1,
      // 1000 / 300 = 3.333 > 1.77
      height: RESOLUTION_RATIO / rat,
    };
  }
  return {
    // 1.33 / 1.77
    width: rat / RESOLUTION_RATIO,
    height: 1,

  };
}

export function getScaleAndPositionFitScreen(relativeSize) {
  const ratio = getFileRatioByRelativeSize(relativeSize);

  let scale;
  let position;
  if (ratio > RESOLUTION_RATIO) {
    scale = ratio / RESOLUTION_RATIO;
    position = {
      y: 0,
      x: (1 - scale) / 2,
    };
    // x = (1 - scale) / 2;
  } else {
    scale = RESOLUTION_RATIO / ratio;
    position = {
      x: 0,
      y: (1 - scale) / 2,
    };
  }

  return { scale, position };
}
