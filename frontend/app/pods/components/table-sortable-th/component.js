import Component from '@ember/component';
import { get, computed } from '@ember/object';
import { htmlSafe } from '@ember/string';

export default Component.extend({
  tagName: 'th',
  classNames: [],
  classNameBindings: ['sorted', 'isAscending:ascending:descending'],

  displayedText: computed('text', function() {
    return htmlSafe(get(this, 'text'));
  }),

  isAscending: computed('orderType', function() {
    return get(this, 'orderType') === 'ASC';
  }),

  sorted: computed('order', 'currentOrder', function() {
    return get(this, 'currentOrder') === get(this, 'order');
  }),

  click() {
    get(this, 'setOrder')(get(this, 'order'));
  },
});
