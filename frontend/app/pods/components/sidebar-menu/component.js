import Component from '@ember/component';
import $ from 'jquery';

export default Component.extend({
  tagName: '',
  didRender() {
    $('.ui.sidebar').sidebar({
      context: '.pushable',
    });
  },
  actions: {
    hideSidebar() {
      $('.ui.sidebar').sidebar('hide');
    },
  },
});
