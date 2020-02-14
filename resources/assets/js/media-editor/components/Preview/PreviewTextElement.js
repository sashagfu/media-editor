import { fabric } from 'fabric';
import * as fileTypes from '../../config/file-types';

/*
eslint-disable no-underscore-dangle
 */

class PreviewTextElement {
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
      width: 690,
      height: 387,
    };
    this.fabricElements = []; // this._getFabricElements();
    this.destroy = this.destroy.bind(this);

    // Create media element
    this._createElement();
  }
  /**
     * Create native element by type of file
     *
     */
  _createElement() {
    const { file } = this.props;
    this.element = this._getNewTextElement(file);
  }

  /**
     * Get is file playable
     *
     *
     *
     * @param props
     * @returns {boolean}
     * @private
     */
  _getIsPlayable(props = this.props) {
    // return false;
    switch (props.file.fileType) {
      case fileTypes.AUDIO:
      case fileTypes.VIDEO:
        return true;
      case fileTypes.IMAGE:
      case fileTypes.SLIDE:
      default:
        return false;
    }
  }

  /**
     * Is element is drawable
     *
     *
     *
     *
     * @param props
     * @returns {boolean}
     * @private
     */
  _getIsDrawable(props = this.props) {
    // return true;
    switch (props.item.showAs || props.file.fileType) {
      case fileTypes.IMAGE:
      case fileTypes.VIDEO:
      case fileTypes.SLIDE:
        return true;
      case fileTypes.AUDIO:
      default:
        return false;
    }
  }

  // Create new text element
  _getNewTextElement(file) {
    this.props.setCanPlay(true);
    const te = document.createElement('div');
    te.style.height = `${file.canvasSize.height}px`;
    te.style.width = `${file.canvasSize.width}px`;
    // te.style.position = 'relative';
    // return this._createNewTextsFrame(te, file.items);
  }
  // added text to new text element
  _createNewTextsFrame(te, items) {
    items.forEach((i) => {
      const di = document.createElement('div');
      const {
        fontFace,
        fontSize,
        fontStyle,
        fontWeight,
        textAlign,
      } = i;
      di.style.position = 'absolute';
      di.style.top = `${i.position.y - (i.size.h / 2)}px`;
      di.style.left = `${i.position.x - (i.size.w / 2)}px`;
      di.style.color = (i.color || '#fff');
      di.style.fontFace = fontFace;
      di.style.fontSize = fontSize;
      di.style.fontStyle = fontStyle;
      di.style.fontWeight = fontWeight;
      di.style.textAlign = textAlign;
      const ti = document.createTextNode(i.text);
      di.appendChild(ti);
      te.appendChild(di);
    });
    this.ready = true;
    return te;
  }

  /**
     * Create instance of fabric.js class and add it to fabric canvas
     *
     * @private
     */
  _createFabricElements() {
    if (this.drawable && !this.fabricElements.length) {
      const { items } = this.props.file;
      items.forEach((item) => {
        const {
          fontFamily,
          fontSize,
          fontStyle,
          fontWeight,
          textAlign,
        } = item;
        const text = new fabric.Text(item.text, {
          originX: 'center',
          originY: 'center',
          top: Number(item.position.y),
          left: Number(item.position.x),
          fill: (item.color || '#ffffff'),
          fontFamily,
          fontSize,
          fontStyle,
          fontWeight,
          textAlign,
          hoverCursor: 'default',
          selectable: false,
        });
        this.fabricElements.push(text);
        this.props.canvas.add(text);
      });
    }
  }
  /**
     * Set new transform to fabric element
     *
     */
  setElementChanges() {
    this._createFabricElements();
    // Bring text to the top of the canvas
    this.fabricElements.forEach((el) => {
      el.bringToFront();
    });
  }

  /**
     * Correct destroy element
     *
     */
  destroy() {
    // Remove from fabric canvas
    if (this.fabricElements.length) {
      this.fabricElements.forEach(item => this.props.canvas.remove(item));
    }
  }
}

export default PreviewTextElement;
