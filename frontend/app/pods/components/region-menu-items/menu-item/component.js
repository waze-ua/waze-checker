import Component from '@ember/component';
import { computed } from '@ember/object';

export default Component.extend({
  tagName: '',
  
  color: computed('amounts', 'type', function() {
    if (this.get('amounts')) {
      const type = this.get('type');
      const count = +this.get(`amounts.${type}`);
      if (count > 0) {
        return 'teal';
      }
      return 'green';
    }
    return '';
  }),
});
