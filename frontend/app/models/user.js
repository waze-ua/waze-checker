import DS from 'ember-data';
import { computed, get } from '@ember/object';

export default DS.Model.extend({
  userName: DS.attr('string'),
  rank: DS.attr('number'),
  firstEdit: DS.attr('number'),
  lastEdit: DS.attr('number'),

  humanizedRank: computed('rank', function() {
    return get(this, 'rank') + 1;
  }),
});
