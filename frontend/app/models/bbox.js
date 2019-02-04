import DS from 'ember-data';

export default DS.Model.extend({
  east: DS.attr('number'),
  north: DS.attr('number'),
  south: DS.attr('number'),
  west: DS.attr('number'),
  region: DS.attr('string'),
});
