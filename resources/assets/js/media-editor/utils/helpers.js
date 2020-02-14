import _ from 'lodash';

/**
 * Add event listeners for many types
 * @param types
 * @param element
 * @param listener
 */
export function addMultiEventListener(types, element, listener) {
  types.forEach(type => element.addEventListener(type, listener));
}

/**
 * Remove event listeners for many types
 * @param types
 * @param element
 * @param listener
 */
export function removeMultiEventListener(types, element, listener) {
  types.forEach(type => element.removeEventListener(type, listener));
}

/**
 * Check two object for diff
 * @param dataObj
 * @param changesObj
 * @returns {boolean}
 */
export function isObjectChange(dataObj, changesObj) {
  let isChange = false;
    _.forOwn(changesObj, (val, key) => { // eslint-disable-line
    if (dataObj[key] !== val) {
      isChange = true;
      return false;
    }
  });
  return isChange;
}

/**
 * Simple object check.
 * @param item
 * @returns {boolean}
 */
export function isObject(item) {
  return (item && typeof item === 'object' && !Array.isArray(item));
}

/**
 * Deep merge two objects.
 * @param target
 * @param sources
 */
export function objectAssignDeep(target, ...sources) {
  if (!sources.length) return target;
  const source = sources.shift();

  if (isObject(target) && isObject(source)) {
    Object.keys(source).forEach((key) => {
      if (isObject(source[key])) {
        if (!target[key]) Object.assign(target, { [key]: {} });
        objectAssignDeep(target[key], source[key]);
      } else {
        Object.assign(target, { [key]: source[key] });
      }
    });
  }

  return objectAssignDeep(target, ...sources);
}

/**
 * Deep (not shallow) clone of the object
 *
 * @param obj {object} Source object to clone
 * @returns {object}
 * @example
 * const a = {b: 1, {d: 2} };
 * const b = a;
 * const c = cloneDeep(a);
 * console.log(b === a); // true
 * console.log(c === a); // false
 *
 * a.d = 123;
 * console.log(b.d); // 123
 * console.log(c.d); // undefined
 */

export function cloneDeep(obj) {
  return JSON.parse(JSON.stringify(obj, (k, v) => (k === '__typename' ? undefined : v)));
}

export function getHashParams() {
  return window.location.hash.split('#')[1].split('/');
}

export function getProjectEditId() {
  const hashParams = getHashParams();
  return hashParams[hashParams.indexOf('edit') + 1];
}

