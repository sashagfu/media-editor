// import uuid from 'uuid';
import moment from 'moment';

export default class Comment {
    id = 0;
    text = '';
    isLiked = false;
    createdAtDiff = '';
    repliesLength = 0;
    projectId = 0;
    parentId = null;

    constructor() {
      this.id = 0; // uuid();
      this.createdAtDiff = moment().fromNow();
    }
}
