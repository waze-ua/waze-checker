import Component from '@ember/component';
import { get } from '@ember/object';

export default Component.extend({
  tagName: 'tr',

  actions: {
    setTransition() {
      const segment = this.get('segment');
      segment.set('hasTransition', true);
      segment.save();
    },

    removeSegment() {
      const segment = this.get('segment');
      segment.destroyRecord().then(() => {
        get(this, 'updateState')();
      });
    },
  },
});
