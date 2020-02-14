import { FRAME_PER_SEC } from '../config/settings';

export function timeFormat(mSec, mZero) {
  const pad = string => `0${string}`.slice(-2);

  const date = new Date(mSec);
  const hh = date.getUTCHours();
  const mm = mZero ? pad(date.getMinutes()) : date.getMinutes();
  const ss = pad(date.getSeconds());
  const ms = date.getMilliseconds();
  const fr = pad(Math.floor(ms / (1000 / FRAME_PER_SEC)));

  if (hh) {
    return `${hh}:${pad(mm)}:${ss}.${fr}`;
  }
  return `${mm}:${ss}.${fr}`;
}

export default timeFormat;
