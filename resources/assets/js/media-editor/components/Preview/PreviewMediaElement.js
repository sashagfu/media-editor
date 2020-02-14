import _ from 'lodash';
import { fabric } from 'fabric';
import * as fileTypes from '../../config/file-types';
import { addMultiEventListener } from '../../utils/helpers';
import { HAVE_FUTURE_DATA } from '../../config/media-element';
import { IMAGE_COLOR_FILTERS } from '../../config/settings';
// import '../libs/WebGLImageFilter/webgl-image-filter'

/*
eslint-disable no-underscore-dangle
 */

class PreviewMediaElement {
  constructor(props) {
    // Save some variable to object to quick access from parent
    this.props = props;
    this.id = props.id;
    this.element = null;
    this.ready = false;
    this.item = props.item;
    this.playable = this._getIsPlayable(props);
    this.drawable = this._getIsDrawable(props);
    // this._fabricFilters = fabric.Image.filters;

    this.canvasSize = {
      width: 640,
      height: 360,
    };

    this._setIsReady = this._setIsReady.bind(this);
    this._isPlayableElementCanPlay = this._isPlayableElementCanPlay.bind(this);
    this._isImageLoaded = this._isImageLoaded.bind(this);
    this.destroy = this.destroy.bind(this);

    // Create media element
    this._createElement();
    // this._createFilter();
  }

  /**
     * Create native element by type of file
     *
     */

  _createElement() {
    const { file } = this.props;

    if (this.playable) {
      this.element = this._getNewPlayableElement(file);
    } else {
      this.element = this._getNewImageElement(file);
    }
  }

  /**
     * Create instance of fabric.js class and add it to fabric canvas
     *
     * @private
     */
  _createFabricElement() {
    if (this.drawable && !this._fabricElement) {
      this._fabricElement = new fabric.Image(this._filteredImage, {
        originX: 'center',
        originY: 'center',
        lockMovementX: true,
        lockMovementY: true,
        hasBorders: false,
        hasControls: false,
        hoverCursor: 'default',
      });
      // Add created element to fabric canvas
      this.props.canvas.add(this._fabricElement);
    }
  }

  /**
     * Create WebGL filter
     *
     * @private
     */
  // _createFilter() {
  // try {
  //     this._filter = new WebGLImageFilter();
  // }
  // catch (err) {
  //     alert('don\'t support WebGL')
  //     // Handle browsers that don't support WebGL
  // }
  // }

  /**
     * Get is file playable
     * Video|Audio - YES
     * Image - NOT
     *
     * @param props
     * @returns {boolean}
     * @private
     */
  _getIsPlayable(props = this.props) {
    switch (props.item.showAs || props.file.fileType) {
      case fileTypes.AUDIO:
      case fileTypes.VIDEO:
      case fileTypes.ASSET:
        return true;
      case fileTypes.IMAGE:
      default:
        return false;
    }
  }

  /**
     * Is element is drawable
     * Image and Video - is
     * Audio - is not
     *
     *
     * @param props
     * @returns {boolean}
     * @private
     */
  _getIsDrawable(props = this.props) {
    switch (props.item.showAs || props.file.fileType) {
      case fileTypes.IMAGE:
      case fileTypes.VIDEO:
      case fileTypes.ASSET:
        return true;
      case fileTypes.AUDIO:
      default:
        return false;
    }
  }

  /**
     * Create new playable element by file
     *
     * @param file
     * @returns {Element}
     * @private
     */
  _getNewPlayableElement(file) {
    // Set can't play because there is media element which haven`t loaded yet
    this.props.setCanPlay(false);
    const ve = document.createElement('video');
    ve.crossOrigin = 'anonymous';
    ve.src = file.filePath;
    ve.width = file.width;
    ve.height = file.height;
    ve.preload = 'auto';
    // On canplaythrough try go ahead
    addMultiEventListener(['canplay', 'seeking', 'seeked'], ve, this._setIsReady);
    // this._setPlayableElementSize(ve);

    // Debug
    ve.addEventListener('ended', () => console.log('ended')); // eslint-disable-line no-console

    return ve;
  }

  // Create new image element by file
  _getNewImageElement(file) {
    // Set can't play because there is media element which haven`t loaded yet
    this.props.setCanPlay(false);
    const ie = document.createElement('img');
    ie.crossOrigin = 'anonymous';
    // Set ready when image loaded
    addMultiEventListener(['load'], ie, this._setIsReady);

    ie.src = file.filePath;
    // ie.src = 'http://yuustar.dev/images/sergey-brin.jpg';
    // this._setImageElementSize(ie);

    return ie;
  }

  /**
     * Get picture size on canvas
     *
     * @returns {{width: number, height: number}}
     * @private
     */
  _getPictureSize() {
    const { canvasSize } = this;
    const { scale, size } = this.item.transform;
    return {
      width: canvasSize.width * size.width * scale,
      height: canvasSize.height * size.height * scale,
    };
  }

  /**
     * get picture position on canvas
     *
     * @returns {{x: number, y: number}}
     * @private
     */
  _getPicturePosition(isCenterPosition = false) {
    const { canvasSize } = this;
    const { position } = this.item.transform;

    let pos = {
      x: canvasSize.width * position.x,
      y: canvasSize.height * position.y,
    };

    if (isCenterPosition) {
      pos = {
        x: pos.x + (isCenterPosition.width / 2),
        y: pos.y + (isCenterPosition.height / 2),
      };
    }

    return pos;
  }

  /**
     * Get is picture need to flip
     * h - is flip horizontal
     * v - is flip vertical
     *
     * @returns {{h, v}|{}}
     * @private
     */
  _getPictureFlip() {
    return this.item.transform.flip || {};

    // return {
    //   h: flip.h ? -1 : 1,
    //   v: flip.v ? -1 : 1
    // }
  }

  /**
     * Get angle of rotate picture
     *
     * @returns {Number}
     * @private
     */
  _getPictureRotate() {
    return this.item.transform.rotation;
  }

  /**
     * Set ready property
     * If element loaded and ready to show|play
     *
     * @private
     */
  _setIsReady() {
    if (this.playable) {
      this._isPlayableElementCanPlay();
    } else {
      this._isImageLoaded();
    }

    this.props.setCanPlay();
  }

  /**
     * Check if playable element can play
     * Means loaded and isn't seeking
     *
     * @param element
     * @private
     */
  _isPlayableElementCanPlay(element = this.element) {
    this.ready = element.readyState >= HAVE_FUTURE_DATA;
  }

  /**
     * Check if image loaded and can be showed
     *
     * @param element
     * @private
     */
  _isImageLoaded(element = this.element) {
    this.ready = element.complete && element.naturalHeight !== 0;
  }

  /**
     * Get and SET currentTime from playable media element
     *
     * @returns {*|Number}
     */
  get currentTime() {
    if (this.playable) {
      return this.element.currentTime;
    }
    return false;
  }

  set currentTime(newCurrentTime) {
    if (this.playable) {
      this.element.currentTime = newCurrentTime;
    }
  }

  /**
     * Get and SET currentTime from playable media element
     *
     */
  get volume() {
    if (this.playable) {
      return this.element.volume;
    }
    return 0;
  }

  set volume(volume) {
    // Set volume only for playable element
    if (!this.playable) return;

    const { muted, unlinked, file } = this.item;
    const { element } = this;

    // If element muted or was unlinked audio
    if (muted || (unlinked && file.fileType === fileTypes.VIDEO)) {
      // If element are not muted
      if (!element.muted) {
        // Mute it
        element.muted = true;
      }
    } else {
      // If element muted, unmute it
      if (element.muted) {
        element.muted = false;
      }
      // and set volume
      element.volume = volume;
    }
  }

  setItem(item) {
    this.item = item;
  }

  /**
     * Set play for playable media element
     *
     */
  play() {
    const { playable, element } = this;
    if (playable && element.paused) {
      element.play();
    }
  }

  /**
     * Set play for playable media element
     */
  pause() {
    const { playable, element } = this;
    if (playable && !element.paused) {
      element.pause();
      // this.element.pause();
    }
  }

  /**
     * Set new transform to fabric element
     *
     */
  setElementChanges() {
    const { width, height } = this._getPictureSize();
    const { x, y } = this._getPicturePosition({ width, height });
    const { h, v } = this._getPictureFlip();
    const angle = this._getPictureRotate();

    this._setFilteredImage();
    this._createFabricElement();

    this._fabricElement.set({
      left: x,
      top: y,
      flipX: h,
      flipY: v,
      angle,
      opacity: 1,
    });
    this._fabricElement.scaleToHeight(height);
    this._fabricElement.scaleToWidth(width);
    // Set item to front of other canvas items
    this._fabricElement.bringToFront();
  }

  /**
     * Set filter image from element and set it to instant
     *
     * @private
     */
  _setFilteredImage() {
    this._setFilter();

    // If there are filter
    if (this._atLeastOneFilter) {
      // Use filter
      this._filteredImage = this._filter.apply(this.element);
    } else {
      // Otherwise use plain element wo filters
      this._filteredImage = this.element;
    }

    // Set image to fabric element
    this._setImageToFabric();
  }

  /**
     * Check if filtered image set into fabric
     * if not, set right one
     *
     * @private
     */
  _setImageToFabric() {
    // If there is fabric element
    // and image in fabric is not which should be
    if (this._fabricElement && this._fabricElement._element !== this._filteredImage) {
      // Set it
      this._fabricElement.setElement(this._filteredImage);
    }
  }

  /**
     * Setup filter when there are new values
     *
     * @private
     */
  _setFilter() {
    if (this._oldColor === this.item.color) return;
    // There is at least one filter value which need to set
    this._atLeastOneFilter = false;

    // Reset filter
    this._filter.reset();
    // For each filter from props
    _.each(this.item.color, (filterValue, filterName) => {
      if (filterValue) {
        // Get filter object from setting
        const filter = IMAGE_COLOR_FILTERS.find(fil => fil.name === filterName);
        // Value props need to divide on divider to prepare what filter numbers required
        this._filter.addFilter(filterName, filterValue / filter.divider);
        this._atLeastOneFilter = true;
      }
    });
    // Save new filter as old
    this._oldColor = this.item.color;
  }

  /**
   * Hide element
   *
   */
  hide() {
    if (this._fabricElement) {
      this._fabricElement.set({ opacity: 0 });
    }
  }

  /**
     * Correct destroy element
     *
     */
  destroy() {
    // Remove from fabric canvas
    this.props.canvas.remove(this._fabricElement);
    if (this.playable && this.element) {
      if (this.ready) {
        this.pause();
        this.element.src = '';
        this.element.load();
      } else {
        this.element.addEventListener('canplay', this.destroy);
      }
    }
  }
}

export default PreviewMediaElement;
