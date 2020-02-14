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

export function mergeUnique(arg1, arg2) {
  const result = [];
  const map = new Map();
  const items = arg1.concat(arg2);
  items.forEach((item) => {
    if (!map.has(item.uuid)) {
      map.set(item.uuid, true);
      result.push(item);
    }
  });
  return result;
}
