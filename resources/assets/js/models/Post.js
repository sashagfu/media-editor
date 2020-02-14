import moment from 'moment';

export default class Post {
    id = 0;
    slug = '';
    content = '';
    isPerformance = false;
    author = {};
    authorId = 0;
    commentVisibility = false;
    commentsCount = 0;
    parsedContent = '';
    createdAtDiff = '';
    userReaction = false;
    shareable = true;
    images = [];
    stars = [];
    likes = [];
    comments = [];

    constructor() {
      this.createdAtDiff = moment().fromNow();
    }
}
